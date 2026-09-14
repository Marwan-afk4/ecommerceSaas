<?php

namespace App\Models;

use App\Enums\ShopRole;
use App\Models\Concerns\GeneratesSlugFromName;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slug',
    'logo_path',
    'tagline',
    'description',
    'public_owner_name',
    'public_email',
    'public_phone',
    'is_active',
])]
class Shop extends Model
{
    /** @use HasFactory<ShopFactory> */
    use GeneratesSlugFromName, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<User, $this, ShopUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ShopUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return HasMany<VariantAttribute, $this>
     */
    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class);
    }

    /**
     * @return HasMany<VariantAttributeValue, $this>
     */
    public function variantAttributeValues(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class);
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany<ProductVariant, $this>
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addMember(User $user, ShopRole $role): void
    {
        $this->users()->syncWithoutDetaching([
            $user->id => ['role' => $role->value],
        ]);
    }

    public static function currentId(): ?int
    {
        $shopId = Context::get('shop_id') ?? session('current_shop_id');

        if (! filled($shopId)) {
            return null;
        }

        return (int) $shopId;
    }

    public function logoUrl(): ?string
    {
        if (! filled($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
