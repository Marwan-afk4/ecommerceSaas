<?php

namespace App\Policies\Concerns;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesShopCatalog
{
    public function viewAny(User $user): bool
    {
        if ($user->is_platform_admin) {
            return true;
        }

        $shopId = Shop::currentId();

        return $shopId !== null && $user->belongsToShop($shopId);
    }

    public function create(User $user): bool
    {
        $shopId = Shop::currentId();

        return $shopId !== null && $user->belongsToShop($shopId);
    }

    public function view(User $user, Model $record): bool
    {
        return $user->is_platform_admin || $user->belongsToShop((int) $record->shop_id);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->view($user, $record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->view($user, $record);
    }

    public function restore(User $user, Model $record): bool
    {
        return $this->view($user, $record);
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $this->view($user, $record);
    }
}
