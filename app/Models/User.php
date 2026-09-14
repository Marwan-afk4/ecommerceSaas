<?php

namespace App\Models;

use App\Enums\ShopRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'is_platform_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Shop, $this, ShopUser>
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class)
            ->using(ShopUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function belongsToShop(Shop|int $shop): bool
    {
        $shopId = $shop instanceof Shop ? $shop->getKey() : $shop;

        if ($this->relationLoaded('shops')) {
            return $this->shops->contains(fn (Shop $memberShop): bool => $memberShop->getKey() === $shopId);
        }

        return $this->shops()->whereKey($shopId)->exists();
    }

    public function roleInShop(Shop|int $shop): ?ShopRole
    {
        $shopId = $shop instanceof Shop ? $shop->getKey() : $shop;

        if ($this->relationLoaded('shops')) {
            $membership = $this->shops->first(fn (Shop $memberShop): bool => $memberShop->getKey() === $shopId);

            return $membership?->pivot->role;
        }

        $membership = $this->shops()->whereKey($shopId)->first();

        return $membership?->pivot->role;
    }

    public function isOwnerOf(Shop|int $shop): bool
    {
        return $this->roleInShop($shop) === ShopRole::Owner;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function activeShops(): Collection
    {
        return $this->shops()->active()->orderBy('name')->orderBy('id')->get();
    }
}
