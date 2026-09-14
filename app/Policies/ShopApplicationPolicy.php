<?php

namespace App\Policies;

use App\Models\ShopApplication;
use App\Models\User;

class ShopApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_platform_admin;
    }

    public function view(User $user, ShopApplication $shopApplication): bool
    {
        return $user->is_platform_admin;
    }

    public function update(User $user, ShopApplication $shopApplication): bool
    {
        return $user->is_platform_admin;
    }
}
