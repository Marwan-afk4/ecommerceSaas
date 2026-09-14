<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_platform_admin;
    }

    public function view(User $user, User $model): bool
    {
        return $user->is_platform_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_platform_admin;
    }

    public function update(User $user, User $model): bool
    {
        return $user->is_platform_admin;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->is_platform_admin && $user->isNot($model);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->is_platform_admin;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->is_platform_admin && $user->isNot($model);
    }
}
