<?php

use App\Enums\ShopRole;
use App\Models\Shop;
use App\Models\ShopApplication;
use App\Models\User;

it('allows platform admins to review store requests', function () {
    $admin = User::factory()->platformAdmin()->create();
    $application = ShopApplication::factory()->pending()->create();

    expect($admin->can('viewAny', ShopApplication::class))->toBeTrue()
        ->and($admin->can('view', $application))->toBeTrue()
        ->and($admin->can('update', $application))->toBeTrue();
});

it('forbids shop members from reviewing store requests', function (ShopRole $role) {
    $shop = Shop::factory()->create();
    $member = User::factory()->create();
    $shop->addMember($member, $role);
    $application = ShopApplication::factory()->pending()->create();

    expect($member->can('viewAny', ShopApplication::class))->toBeFalse()
        ->and($member->can('view', $application))->toBeFalse()
        ->and($member->can('update', $application))->toBeFalse();
})->with([
    'owner' => ShopRole::Owner,
    'manager' => ShopRole::Manager,
]);
