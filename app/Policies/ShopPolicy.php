<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_platform_admin;
    }

    public function view(User $user, Shop $shop): bool
    {
        return $user->is_platform_admin || $user->belongsToShop($shop);
    }

    public function create(User $user): bool
    {
        return $user->is_platform_admin;
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->is_platform_admin || $user->isOwnerOf($shop);
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $user->is_platform_admin;
    }

    public function restore(User $user, Shop $shop): bool
    {
        return $user->is_platform_admin;
    }

    public function forceDelete(User $user, Shop $shop): bool
    {
        return $user->is_platform_admin;
    }
}
