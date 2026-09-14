<?php

namespace App\Actions\Catalog;

use App\Models\Category;
use App\Models\Shop;

class EnsureDefaultCategory
{
    public function handle(Shop $shop): Category
    {
        $existing = $shop->categories()->orderBy('id')->first();

        if ($existing instanceof Category) {
            return $existing;
        }

        return $shop->categories()->create([
            'shop_id' => $shop->id,
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);
    }
}
