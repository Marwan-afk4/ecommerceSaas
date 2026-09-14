<?php

namespace App\Models;

use App\Models\Concerns\BelongsToShop;
use App\Models\Concerns\GeneratesSlugFromName;
use Database\Factories\VariantAttributeValueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['shop_id', 'variant_attribute_id', 'name', 'slug', 'hex', 'sort_order'])]
class VariantAttributeValue extends Model
{
    /** @use HasFactory<VariantAttributeValueFactory> */
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
     * @return BelongsTo<VariantAttribute, $this>
     */
    public function variantAttribute(): BelongsTo
    {
        return $this->belongsTo(VariantAttribute::class);
    }

    /**
     * @return BelongsToMany<ProductVariant, $this>
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values');
    }
}
