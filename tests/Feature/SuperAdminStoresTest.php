<?php

use App\Livewire\SuperAdmin\Stores;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

it('lets a platform admin delete a store', function () {
    $admin = User::factory()->platformAdmin()->create();
    $shop = Shop::factory()->create();

    $this->actingAs($admin);

    Livewire::test(Stores::class)
        ->call('deleteShop', $shop->id)
        ->assertHasNoErrors();

    $this->assertModelMissing($shop);
});
