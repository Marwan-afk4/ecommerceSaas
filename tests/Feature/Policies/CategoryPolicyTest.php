<?php

use App\Enums\ShopRole;
use App\Models\Category;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Context;

afterEach(function (): void {
    Context::flush();
});

it('allows shop members to update catalog records in their shop', function (ShopRole $role) {
    $shop = Shop::factory()->create();
    $member = User::factory()->create();
    $shop->addMember($member, $role);
    $category = Category::factory()->for($shop)->create();

    expect($member->can('update', $category))->toBeTrue();
})->with([
    'owner' => ShopRole::Owner,
    'manager' => ShopRole::Manager,
]);

it('forbids updating catalog records that belong to another shop', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $foreignCategory = Category::factory()->for($otherShop)->create();

    expect($owner->can('update', $foreignCategory))->toBeFalse();
});

it('allows creating catalog records when the shop is stored in the session', function () {
    $shop = Shop::factory()->create();
    $member = User::factory()->create();
    $shop->addMember($member, ShopRole::Owner);

    $this->actingAs($member);
    session(['current_shop_id' => $shop->id]);

    expect($member->can('create', Category::class))->toBeTrue();
});

it('allows listing catalog records only for the current shop tenant', function () {
    $shop = Shop::factory()->create();
    $member = User::factory()->create();
    $shop->addMember($member, ShopRole::Owner);

    $this->actingAs($member);
    Context::add('shop_id', $shop->id);

    expect($member->can('viewAny', Category::class))->toBeTrue();
});

it('forbids listing catalog records when the user is not a member of the current shop', function () {
    $shop = Shop::factory()->create();
    $outsider = User::factory()->create();

    $this->actingAs($outsider);
    Context::add('shop_id', $shop->id);

    expect($outsider->can('viewAny', Category::class))->toBeFalse();
});
