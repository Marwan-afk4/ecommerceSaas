<?php

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\QueryException;

it('allows the same category slug in different shops', function () {
    $firstShop = Shop::factory()->create();
    $secondShop = Shop::factory()->create();

    Category::factory()->for($firstShop)->create(['name' => 'Phones', 'slug' => 'phones']);
    Category::factory()->for($secondShop)->create(['name' => 'Phones', 'slug' => 'phones']);

    $this->assertDatabaseCount('categories', 2);
});

it('rejects a duplicate category slug inside the same shop', function () {
    $shop = Shop::factory()->create();
    Category::factory()->for($shop)->create(['name' => 'Phones', 'slug' => 'phones']);

    Category::factory()->for($shop)->create(['name' => 'Mobiles', 'slug' => 'phones']);
})->throws(QueryException::class);
