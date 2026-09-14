<?php

use App\Actions\PlaceOrderRequest;
use App\Enums\OrderStatus;
use App\Livewire\ShopStorefront;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use Livewire\Livewire;

it('lets a customer send an order request from the public store', function () {
    $shop = Shop::factory()->create();
    $product = Product::factory()->active()->for($shop)->create(['name' => 'Pixel 15']);

    Livewire::test(ShopStorefront::class, ['shop' => $shop])
        ->set('customer_name', 'Ada Buyer')
        ->set('customer_email', 'ada@example.com')
        ->set('customer_phone', '555-0100')
        ->set('product_id', $product->id)
        ->set('quantity', 2)
        ->call('requestOrder')
        ->assertHasNoErrors()
        ->assertSee('Your order request was sent to the store.', false);

    $order = Order::query()->with('items')->where('customer_email', 'ada@example.com')->first();

    expect($order)->not->toBeNull()
        ->and($order->shop_id)->toBe($shop->id)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->quantity)->toBe(2)
        ->and($order->items->first()->name)->toBe('Pixel 15');
});

it('requires a variant when the product has skus', function () {
    $shop = Shop::factory()->create();
    $product = Product::factory()->active()->for($shop)->create();
    ProductVariant::factory()->for($shop)->for($product)->create();

    Livewire::test(ShopStorefront::class, ['shop' => $shop])
        ->set('customer_name', 'Ada Buyer')
        ->set('customer_email', 'ada@example.com')
        ->set('product_id', $product->id)
        ->set('quantity', 1)
        ->call('requestOrder')
        ->assertHasErrors(['product_variant_id']);

    $this->assertDatabaseCount('orders', 0);
});

it('returns 404 when the requested product belongs to another store', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    Product::factory()->active()->for($shop)->create();
    $foreign = Product::factory()->active()->for($otherShop)->create();

    Livewire::test(ShopStorefront::class, ['shop' => $shop])
        ->set('customer_name', 'Ada Buyer')
        ->set('customer_email', 'ada@example.com')
        ->set('product_id', $foreign->id)
        ->set('quantity', 1)
        ->call('requestOrder')
        ->assertNotFound();
});

it('creates an order request through the action for an active store', function () {
    $shop = Shop::factory()->create();
    $product = Product::factory()->active()->for($shop)->create(['name' => 'Case']);

    $order = app(PlaceOrderRequest::class)->handle($shop, [
        'customer_name' => 'Ada Buyer',
        'customer_email' => 'ada@example.com',
        'customer_phone' => null,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->items->first()->name)->toBe('Case');
});
