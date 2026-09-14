<?php

use App\Actions\Catalog\GenerateProductVariants;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Validation\ValidationException;

it('creates a default sku when the category has no variant attributes', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();

    $variants = app(GenerateProductVariants::class)->handle($product, []);

    expect($variants)->toHaveCount(1)
        ->and($variants->first()->is_default)->toBeTrue();

    $this->assertDatabaseCount('product_variants', 1);
});

it('does not create a second default sku when none are needed', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();
    $generate = app(GenerateProductVariants::class);

    $generate->handle($product, []);
    $generate->handle($product, []);

    $this->assertDatabaseCount('product_variants', 1);
});

it('creates one sku for each selected attribute combination', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();

    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);
    $small = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'S', 'slug' => 's']);
    $medium = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'M', 'slug' => 'm']);
    $color = VariantAttribute::factory()->for($shop)->create(['name' => 'Color', 'slug' => 'color']);
    $red = VariantAttributeValue::factory()->for($shop)->for($color, 'variantAttribute')->create(['name' => 'Red', 'slug' => 'red']);

    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);
    $category->variantAttributes()->attach($color, ['is_required' => true, 'sort_order' => 1]);

    $created = app(GenerateProductVariants::class)->handle($product, [
        $size->id => [$small->id, $medium->id],
        $color->id => [$red->id],
    ]);

    expect($created)->toHaveCount(2);
    $this->assertDatabaseCount('product_variants', 2);
    $this->assertDatabaseCount('product_variant_values', 4);
});

it('skips attribute combinations that already exist', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();
    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);
    $small = VariantAttributeValue::factory()->for($shop)->for($size, 'variantAttribute')->create(['name' => 'S', 'slug' => 's']);

    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);

    $generate = app(GenerateProductVariants::class);
    $generate->handle($product, [$size->id => [$small->id]]);
    $created = $generate->handle($product, [$size->id => [$small->id]]);

    expect($created)->toHaveCount(0);
    $this->assertDatabaseCount('product_variants', 1);
});

it('rejects generation when a required attribute has no selected values', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();
    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);

    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);

    app(GenerateProductVariants::class)->handle($product, []);
})->throws(ValidationException::class);

it('rejects values that do not belong to the selected attribute', function () {
    $shop = Shop::factory()->create();
    $category = Category::factory()->for($shop)->create();
    $product = Product::factory()->for($shop)->for($category)->create();
    $size = VariantAttribute::factory()->for($shop)->create(['name' => 'Size', 'slug' => 'size']);
    $color = VariantAttribute::factory()->for($shop)->create(['name' => 'Color', 'slug' => 'color']);
    $red = VariantAttributeValue::factory()->for($shop)->for($color, 'variantAttribute')->create(['name' => 'Red', 'slug' => 'red']);

    $category->variantAttributes()->attach($size, ['is_required' => true, 'sort_order' => 0]);

    app(GenerateProductVariants::class)->handle($product, [
        $size->id => [$red->id],
    ]);
})->throws(ValidationException::class);
