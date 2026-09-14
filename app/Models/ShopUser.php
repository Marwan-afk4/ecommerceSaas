<?php

namespace App\Models;

use App\Enums\ShopRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShopUser extends Pivot
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => ShopRole::class,
        ];
    }
}
