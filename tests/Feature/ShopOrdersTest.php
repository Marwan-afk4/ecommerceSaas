<?php

use App\Enums\OrderStatus;
use App\Enums\ShopRole;
use App\Livewire\Shop\Orders;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Context;
use Livewire\Livewire;

it('lists only the current stores order requests', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $product = Product::factory()->for($shop)->create();
    $order = Order::factory()->for($shop)->create(['customer_name' => 'Ada Buyer']);
    OrderItem::factory()->for($shop)->for($order)->for($product)->create(['name' => 'Our Phone']);
    $foreign = Order::factory()->for($otherShop)->create(['customer_name' => 'Secret Buyer']);

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);
    Context::add('shop_id', $shop->id);

    Livewire::test(Orders::class)
        ->assertSee('Ada Buyer', false)
        ->assertDontSee('Secret Buyer', false);

    $this->assertModelExists($foreign);
});

it('lets a shop admin accept a pending order request', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $order = Order::factory()->for($shop)->pending()->create();

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);
    Context::add('shop_id', $shop->id);

    Livewire::test(Orders::class)
        ->call('accept', $order->id)
        ->assertHasNoErrors();

    expect($order->refresh()->status)->toBe(OrderStatus::Accepted);
});

it('returns 404 when accepting another shops order', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);
    $foreign = Order::factory()->for($otherShop)->pending()->create();

    $this->actingAs($owner);
    session(['current_shop_id' => $shop->id]);
    Context::add('shop_id', $shop->id);

    Livewire::test(Orders::class)
        ->call('accept', $foreign->id)
        ->assertNotFound();

    expect($foreign->refresh()->status)->toBe(OrderStatus::Pending);
});
