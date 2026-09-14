<?php

use App\Enums\ShopRole;
use App\Livewire\Shop\Admins;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Context;
use Livewire\Livewire;

it('redirects guests from store admins to login', function () {
    $this->get(route('shop.admins'))->assertRedirect(route('login'));
});

it('lets a store owner add another admin', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);
    Context::add('shop_id', $shop->id);

    Livewire::test(Admins::class)
        ->set('name', 'Casey Manager')
        ->set('email', 'casey@example.com')
        ->set('password', 'password')
        ->set('role', ShopRole::Manager->value)
        ->call('addAdmin')
        ->assertHasNoErrors();

    $admin = User::query()->where('email', 'casey@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->belongsToShop($shop))->toBeTrue()
        ->and($admin->roleInShop($shop))->toBe(ShopRole::Manager);
});

it('forbids managers from adding admins', function () {
    $shop = Shop::factory()->create();
    $manager = User::factory()->create();
    $shop->addMember($manager, ShopRole::Manager);

    $this->actingAs($manager);
    session(['current_shop_id' => $shop->id]);
    Context::add('shop_id', $shop->id);

    Livewire::test(Admins::class)
        ->set('name', 'New Owner')
        ->set('email', 'new-owner@example.com')
        ->set('password', 'password')
        ->set('role', ShopRole::Owner->value)
        ->call('addAdmin')
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'new-owner@example.com']);
});
