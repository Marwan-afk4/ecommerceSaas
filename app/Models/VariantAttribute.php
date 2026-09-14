<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use App\Models\Concerns\GeneratesSlugFromName;
use Database\Factories\VariantAttributeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['shop_id', 'name', 'slug', 'sort_order'])]
class VariantAttribute extends Model
{
    /** @use HasFactory<VariantAttributeFactory> */
    use BelongsToShop, GeneratesSlugFromName, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<VariantAttributeValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_variant_attribute')
            ->withPivot(['is_required', 'sort_order']);
    }
}
