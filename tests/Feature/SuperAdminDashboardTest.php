<?php

use App\Enums\ShopRole;
use App\Livewire\SuperAdmin\Products;
use App\Livewire\SuperAdmin\Stores;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

it('redirects guests from superadmin to the superadmin login', function () {
    $this->get(route('superadmin.dashboard'))->assertRedirect(route('superadmin.login'));
});

it('forbids shop owners from opening superadmin', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner)
        ->get(route('superadmin.dashboard'))
        ->assertForbidden();
});

it('shows platform stats on the superadmin home', function () {
    $admin = User::factory()->platformAdmin()->create(['name' => 'Platform Admin']);
    Shop::factory()->create(['name' => 'Visible Store']);

    $this->actingAs($admin)
        ->get(route('superadmin.dashboard'))
        ->assertOk()
        ->assertSee('Welcome back', false)
        ->assertSee('Stores', false)
        ->assertSee('Store requests', false)
        ->assertSee('Users', false)
        ->assertSee('Visible Store', false)
        ->assertSee('Switch to dark mode', false);
});

it('lists every store, admin, and product for a platform admin', function () {
    $admin = User::factory()->platformAdmin()->create(['name' => 'Platform Admin']);
    $shop = Shop::factory()->create(['name' => 'Visible Store']);
    $owner = User::factory()->create(['name' => 'Shop Owner']);
    $shop->addMember($owner, ShopRole::Owner);
    Product::factory()->for($shop)->create(['name' => 'Visible Product']);

    $this->actingAs($admin);

    $this->get(route('superadmin.shops'))
        ->assertOk()
        ->assertSee('Visible Store', false);

    $this->get(route('superadmin.applications'))
        ->assertOk()
        ->assertSee('Store requests', false);

    $this->get(route('superadmin.users'))
        ->assertOk()
        ->assertSee('Shop Owner', false)
        ->assertSee('Platform Admin', false);

    $this->get(route('superadmin.products'))
        ->assertOk()
        ->assertSee('Visible Product', false);
});

it('lets a platform admin turn a store off', function () {
    $admin = User::factory()->platformAdmin()->create();
    $shop = Shop::factory()->create(['is_active' => true]);

    $this->actingAs($admin);

    Livewire::test(Stores::class)
        ->call('toggleShop', $shop->id)
        ->assertHasNoErrors();

    expect($shop->refresh()->is_active)->toBeFalse();
});

it('lets a platform admin delete a product from any store', function () {
    $admin = User::factory()->platformAdmin()->create();
    $shop = Shop::factory()->create();
    $product = Product::factory()->for($shop)->create();

    $this->actingAs($admin);

    Livewire::test(Products::class)
        ->call('deleteProduct', $product->id)
        ->assertHasNoErrors();

    $this->assertModelMissing($product);
});
