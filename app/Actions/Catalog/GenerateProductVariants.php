<?php

namespace App\Actions\Catalog;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GenerateProductVariants
{
    /**
     * @param  array<int|string, list<int|string>>  $selectedValueIdsByAttributeId
     * @return Collection<int, ProductVariant>
     */
    public function handle(Product $product, array $selectedValueIdsByAttributeId = []): Collection
    {
        return DB::transaction(function () use ($product, $selectedValueIdsByAttributeId): Collection {
            $product = Product::query()
                ->with([
                    'shop',
                    'category.variantAttributes.values',
                    'variants.attributeValues',
                ])
                ->lockForUpdate()
                ->findOrFail($product->id);

            $attributes = $product->category->variantAttributes;

            if ($attributes->isEmpty()) {
                return collect([$this->ensureDefaultVariant($product)]);
            }

            $valueGroups = $this->selectedValueGroups($attributes, $selectedValueIdsByAttributeId);

            if ($valueGroups === []) {
                throw ValidationException::withMessages([
                    'attributes' => 'Select values for at least one attribute.',
                ]);
            }

            $existingFingerprints = $product->variants
                ->map(fn (ProductVariant $variant): string => $this->fingerprint($variant->attributeValues))
                ->all();

            $created = collect();

            foreach ($this->cartesian($valueGroups) as $index => $valueSet) {
                $fingerprint = $this->fingerprint($valueSet);

                if (in_array($fingerprint, $existingFingerprints, true)) {
                    continue;
                }

                $variant = $product->variants()->create([
                    'shop_id' => $product->shop_id,
                    'sku' => $this->uniqueSku($product, $valueSet),
                    'price' => '0.00',
                    'stock' => 0,
                    'is_default' => $product->variants()->doesntExist() && $index === 0,
                ]);

                $variant->attributeValues()->attach($valueSet->pluck('id')->all());
                $existingFingerprints[] = $fingerprint;
                $created->push($variant);
            }

            return $created;
        });
    }

    /**
     * @param  Collection<int, VariantAttribute>  $attributes
     * @param  array<int|string, list<int|string>>  $selectedValueIdsByAttributeId
     * @return list<Collection<int, VariantAttributeValue>>
     */
    private function selectedValueGroups(Collection $attributes, array $selectedValueIdsByAttributeId): array
    {
        $groups = [];

        foreach ($attributes as $attribute) {
            $selectedIds = NormalizeSelectedIds::forKey($selectedValueIdsByAttributeId, $attribute->id);

            if ($selectedIds->isEmpty()) {
                if ($attribute->pivot->is_required) {
                    throw ValidationException::withMessages([
                        "attributes.{$attribute->id}" => "Select at least one value for {$attribute->name}.",
                    ]);
                }

                continue;
            }

            $values = VariantAttributeValue::query()
                ->where('variant_attribute_id', $attribute->id)
                ->where('shop_id', $attribute->shop_id)
                ->whereKey($selectedIds->all())
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            if ($values->isEmpty()) {
                throw ValidationException::withMessages([
                    "attributes.{$attribute->id}" => "One or more selected values are invalid for {$attribute->name}.",
                ]);
            }

            $groups[] = $values;
        }

        return $groups;
    }

    /**
     * @param  list<Collection<int, VariantAttributeValue>>  $groups
     * @return Collection<int, Collection<int, VariantAttributeValue>>
     */
    private function cartesian(array $groups): Collection
    {
        $combinations = collect([collect()]);

        foreach ($groups as $group) {
            $combinations = $combinations->flatMap(
                fn (Collection $combination): Collection => $group->map(
                    fn (VariantAttributeValue $value): Collection => collect($combination->all())->push($value),
                ),
            );
        }

        return $combinations;
    }

    /**
     * @param  Collection<int, VariantAttributeValue>  $values
     */
    private function fingerprint(Collection $values): string
    {
        return $values->pluck('id')->sort()->values()->implode('-');
    }

    /**
     * @param  Collection<int, VariantAttributeValue>  $values
     */
    private function uniqueSku(Product $product, Collection $values): string
    {
        $base = Str::upper(Str::slug($product->slug.'-'.$values->pluck('slug')->implode('-')));
        $sku = $base;
        $suffix = 2;

        while (ProductVariant::query()->where('shop_id', $product->shop_id)->where('sku', $sku)->exists()) {
            $sku = $base.'-'.$suffix;
            $suffix++;
        }

        return $sku;
    }

    private function ensureDefaultVariant(Product $product): ProductVariant
    {
        $existing = $product->variants->first();

        if ($existing instanceof ProductVariant) {
            return $existing;
        }

        return $product->variants()->create([
            'shop_id' => $product->shop_id,
            'sku' => $this->uniqueSku($product, collect()),
            'price' => '0.00',
            'stock' => 0,
            'is_default' => true,
        ]);
    }
}
