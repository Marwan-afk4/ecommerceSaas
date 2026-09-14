<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Shop;

it('shows the public store profile and products', function () {
    $shop = Shop::factory()->create([
        'name' => 'Acme Phones',
        'tagline' => 'Better calls',
        'description' => 'We sell phones.',
        'public_owner_name' => 'Ada Owner',
        'public_email' => 'hello@acme.test',
        'public_phone' => '555-0100',
    ]);
    Product::factory()->active()->for($shop)->create(['name' => 'Pixel 15']);

    $this->get(route('shops.show', $shop))
        ->assertOk()
        ->assertSee('Acme Phones', false)
        ->assertSee('Better calls', false)
        ->assertSee('We sell phones.', false)
        ->assertSee('Ada Owner', false)
        ->assertSee('hello@acme.test', false)
        ->assertSee('555-0100', false)
        ->assertSee('Pixel 15', false);
});

it('escapes untrusted store copy on the public page', function () {
    $shop = Shop::factory()->create([
        'name' => 'Acme <script>alert(1)</script>',
        'description' => '<img src=x onerror=alert(1)>',
        'public_owner_name' => 'Ada <b>Owner</b>',
    ]);

    $this->get(route('shops.show', $shop))
        ->assertOk()
        ->assertSee('Acme &lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});

it('returns 404 for an inactive store', function () {
    $shop = Shop::factory()->inactive()->create();

    $this->get(route('shops.show', $shop))->assertNotFound();
});

it('does not show another stores products', function () {
    $shop = Shop::factory()->create();
    $otherShop = Shop::factory()->create();
    Product::factory()->active()->for($shop)->create(['name' => 'Our Phone']);
    Product::factory()->active()->for($otherShop)->create(['name' => 'Secret Laptop']);

    $this->get(route('shops.show', $shop))
        ->assertOk()
        ->assertSee('Our Phone', false)
        ->assertDontSee('Secret Laptop', false);
});

it('does not show draft products on the public store', function () {
    $shop = Shop::factory()->create();
    Product::factory()->for($shop)->create([
        'name' => 'Hidden Draft',
        'status' => ProductStatus::Draft,
    ]);

    $this->get(route('shops.show', $shop))
        ->assertOk()
        ->assertDontSee('Hidden Draft', false);
});
