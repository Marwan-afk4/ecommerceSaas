<?php

namespace App\Actions;

use App\Enums\ShopRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddShopAdmin
{
    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     password?: ?string,
     *     role: string,
     * }  $data
     */
    public function handle(Shop $shop, array $data): User
    {
        $role = ShopRole::from($data['role']);

        return DB::transaction(function () use ($shop, $data, $role): User {
            $user = User::query()->where('email', $data['email'])->first();

            if ($user instanceof User) {
                if ($user->is_platform_admin) {
                    throw ValidationException::withMessages([
                        'email' => 'That account cannot be added to a store.',
                    ]);
                }

                $shop->addMember($user, $role);

                return $user;
            }

            if (blank($data['password'] ?? null)) {
                throw ValidationException::withMessages([
                    'password' => 'A password is required for a new admin.',
                ]);
            }

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_platform_admin' => false,
            ]);

            $shop->addMember($user, $role);

            return $user;
        });
    }
}
