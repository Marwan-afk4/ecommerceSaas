<?php

use App\Enums\ShopRole;
use App\Livewire\Shop\Products;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

it('redirects guests from shop admin to login', function () {
    $this->get(route('shop.dashboard'))->assertRedirect(route('login'));
});

it('forbids users who do not belong to a store', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('shop.dashboard'))
        ->assertForbidden();
});

it('shows the store admin sidebar on the home dashboard', function () {
    $shop = Shop::factory()->create(['name' => 'Pixel Phones']);
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner)
        ->withSession(['current_shop_id' => $shop->id])
        ->get(route('shop.dashboard'))
        ->assertOk()
        ->assertSee('Welcome back', false)
        ->assertSee('Pixel Phones', false)
        ->assertSee('Products', false)
        ->assertSee('Categories', false)
        ->assertSee('Admins', false)
        ->assertSee('Orders', false)
        ->assertSee('Switch to dark mode', false);
});

it('lets a shop owner add a product to their store', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Products::class)
        ->set('product_name', 'Pixel 15')
        ->set('product_description', 'Flagship phone')
        ->call('createProduct')
        ->assertHasNoErrors();

    $product = Product::query()->where('name', 'Pixel 15')->first();

    expect($product)->not->toBeNull()
        ->and($product->shop_id)->toBe($shop->id)
        ->and($product->description)->toBe('Flagship phone');
});

it('does not list another stores products in shop admin', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    Product::factory()->for($shop)->create(['name' => 'Our Phone']);
    Product::factory()->for($otherShop)->create(['name' => 'Secret Laptop']);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Products::class)
        ->assertSee('Our Phone', false)
        ->assertDontSee('Secret Laptop', false);
});

it('returns 404 when deleting another shops product through shop admin', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $foreignProduct = Product::factory()->for($otherShop)->create();

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);

    Livewire::test(Products::class)
        ->call('deleteProduct', $foreignProduct->id)
        ->assertNotFound();

    $this->assertModelExists($foreignProduct);
});
