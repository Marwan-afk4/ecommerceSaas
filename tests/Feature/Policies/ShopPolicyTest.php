<?php

use App\Enums\ShopRole;
use App\Models\Shop;
use App\Models\User;

it('allows platform admins to manage shops', function () {
    $admin = User::factory()->platformAdmin()->create();
    $shop = Shop::factory()->create();

    expect($admin->can('viewAny', Shop::class))->toBeTrue()
        ->and($admin->can('create', Shop::class))->toBeTrue()
        ->and($admin->can('update', $shop))->toBeTrue()
        ->and($admin->can('delete', $shop))->toBeTrue();
});

it('allows shop owners to update their shop profile', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    expect($owner->can('update', $shop))->toBeTrue()
        ->and($owner->can('viewAny', Shop::class))->toBeFalse()
        ->and($owner->can('delete', $shop))->toBeFalse();
});

it('forbids managers from updating the shop profile', function () {
    $shop = Shop::factory()->create();
    $manager = User::factory()->create();
    $shop->addMember($manager, ShopRole::Manager);

    expect($manager->can('update', $shop))->toBeFalse();
});
