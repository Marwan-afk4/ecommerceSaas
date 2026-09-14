<?php

use App\Enums\ShopRole;
use App\Livewire\Login;
use App\Models\Shop;
use App\Models\ShopApplication;
use App\Models\User;
use Livewire\Livewire;

it('lets a shop owner sign in to shop admin', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    Livewire::test(Login::class)
        ->set('email', $owner->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('shop.dashboard'));
});

it('lets a platform admin sign in to superadmin', function () {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::test(Login::class, ['superadmin' => true])
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('superadmin.dashboard'));
});

it('rejects a shop owner from the superadmin login', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    Livewire::test(Login::class, ['superadmin' => true])
        ->set('email', $owner->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('rejects a platform admin without a store from shop login', function () {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::test(Login::class)
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('tells a pending store applicant to wait for superadmin approval', function () {
    ShopApplication::factory()->pending()->create(['admin_email' => 'wait@acme.test']);

    Livewire::test(Login::class)
        ->set('email', 'wait@acme.test')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email' => 'Your store request is waiting for superadmin approval.']);

    $this->assertGuest();
});

it('rejects an invalid password', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('authenticate')
        ->assertHasErrors(['email']);
});
