<?php

use App\Enums\ShopRole;
use App\Livewire\Shop\Categories;
use App\Livewire\Shop\ProductEditor;
use App\Livewire\Shop\Products;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Livewire\Livewire;

it('lets a shop owner create a category', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Categories::class)
        ->set('category_name', 'Phones')
        ->set('category_description', 'Mobile devices')
        ->call('createCategory')
        ->assertHasNoErrors()
        ->assertSee('Category added.', false)
        ->assertSee('Phones', false);

    $category = Category::query()->where('name', 'Phones')->first();

    expect($category)->not->toBeNull()
        ->and($category->shop_id)->toBe($shop->id)
        ->and($category->description)->toBe('Mobile devices');
});

it('rejects an empty category form', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Categories::class)
        ->call('createCategory')
        ->assertHasErrors(['category_name']);

    expect(Category::query()->whereBelongsTo($shop)->where('name', '')->exists())->toBeFalse();
});

it('rejects an empty product form', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Products::class)
        ->call('createProduct')
        ->assertHasErrors(['product_name']);

    expect(Product::query()->whereBelongsTo($shop)->exists())->toBeFalse();
});

it('lets a shop owner add a product to a chosen category', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $category = Category::factory()->for($shop)->create(['name' => 'Laptops']);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Products::class)
        ->set('product_name', 'Notebook Pro')
        ->set('category_id', $category->id)
        ->call('createProduct')
        ->assertHasNoErrors()
        ->assertSee('Product added.', false);

    expect(Product::query()->where('name', 'Notebook Pro')->first())
        ->category_id->toBe($category->id)
        ->shop_id->toBe($shop->id);
});

it('generates a default sku when the category has no variant attributes', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $product = Product::factory()->for($shop)->create();

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(ProductEditor::class, ['product' => $product])
        ->call('generateVariants')
        ->assertHasNoErrors()
        ->assertSee('Variants generated.', false);

    expect(ProductVariant::query()->where('product_id', $product->id)->count())->toBe(1);
});

it('generates one sku for each selected attribute combination', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();

    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);
    $small = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'S', 'slug' => 's']);
    $medium = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'M', 'slug' => 'm']);
    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(ProductEditor::class, ['product' => $product])
        ->set('selectedValues', [$size->id => [$small->id, $medium->id]])
        ->call('generateVariants')
        ->assertHasNoErrors();

    expect(ProductVariant::query()->where('product_id', $product->id)->count())->toBe(2);
});

it('generates skus from livewire checkbox payloads', function (string $shape) {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();

    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);
    $small = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'S', 'slug' => 's']);
    $medium = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'M', 'slug' => 'm']);
    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);

    $selectedValues = match ($shape) {
        'strings' => [(string) $size->id => [(string) $small->id, (string) $medium->id]],
        'booleans' => [$size->id => [$small->id => true, $medium->id => true]],
        'extras' => [$size->id => [$small->id, $medium->id, 999_999]],
        'nested' => [$size->id => [$small->id => true, $medium->id => false]],
    };

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(ProductEditor::class, ['product' => $product])
        ->set('selectedValues', $selectedValues)
        ->call('generateVariants')
        ->assertHasNoErrors();

    $expectedCount = $shape === 'nested' ? 1 : 2;

    expect(ProductVariant::query()->where('product_id', $product->id)->count())->toBe($expectedCount);
})->with(['strings', 'booleans', 'extras', 'nested']);

it('lets a shop owner add a variant type and generate skus from it', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $category = Category::factory()->for($shop)->create(['name' => 'Phones']);
    $product = Product::factory()->for($shop)->for($category)->create(['name' => 'Pixel 15']);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Categories::class)
        ->set('attribute_name', 'Storage')
        ->set('attribute_values', '128GB, 256GB')
        ->call('createAttribute')
        ->assertHasNoErrors()
        ->assertSee('Variant type added.', false);

    $attribute = VariantAttribute::query()->where('name', 'Storage')->first();

    expect($attribute)->not->toBeNull()
        ->and($attribute->shop_id)->toBe($shop->id)
        ->and($attribute->values()->pluck('name')->all())->toEqualCanonicalizing(['128GB', '256GB']);

    Livewire::test(Categories::class)
        ->set("categoryAttributeIds.{$category->id}", [$attribute->id])
        ->call('saveCategoryAttributes', $category->id)
        ->assertHasNoErrors();

    $valueIds = $attribute->values()->orderBy('sort_order')->orderBy('id')->pluck('id')->all();

    Livewire::test(ProductEditor::class, ['product' => $product->fresh()])
        ->set('selectedValues', [$attribute->id => $valueIds])
        ->call('generateVariants')
        ->assertHasNoErrors();

    expect(ProductVariant::query()->where('product_id', $product->id)->count())->toBe(2);
});

it('lets a shop owner save variant price and stock', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $product = Product::factory()->for($shop)->create();
    $variant = ProductVariant::factory()->for($shop)->for($product)->create([
        'price' => '0.00',
        'stock' => 0,
    ]);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(ProductEditor::class, ['product' => $product])
        ->set("variantPrices.{$variant->id}", '12.50')
        ->set("variantStocks.{$variant->id}", 8)
        ->call('saveVariant', $variant->id)
        ->assertHasNoErrors()
        ->assertSee('Variant updated.', false);

    expect($variant->fresh())
        ->price->toEqual('12.50')
        ->stock->toBe(8);
});

it('returns 404 when opening another shops product editor', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $foreignProduct = Product::factory()->for($otherShop)->create();

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(ProductEditor::class, ['product' => $foreignProduct])
        ->assertNotFound();
});
