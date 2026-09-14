<?php

namespace App\Livewire\Shop;

use App\Actions\Catalog\NormalizeSelectedIds;
use App\Livewire\Concerns\InteractsWithCurrentShop;
use App\Models\Category;
use App\Models\VariantAttribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Categories')]
class Categories extends Component
{
    use InteractsWithCurrentShop;

    public string $category_name = '';

    public string $category_description = '';

    public string $attribute_name = '';

    public string $attribute_values = '';

    /**
     * @var array<int|string, list<int|string>>
     */
    public array $categoryAttributeIds = [];

    public ?string $statusMessage = null;

    public function createCategory(): void
    {
        $shop = $this->currentShop();
        $this->authorize('create', Category::class);

        $validated = $this->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'category_description' => ['nullable', 'string', 'max:5000'],
        ]);

        $shop->categories()->create([
            'shop_id' => $shop->id,
            'name' => $validated['category_name'],
            'description' => $validated['category_description'] ?: null,
            'is_active' => true,
        ]);

        $this->reset(['category_name', 'category_description']);
        $this->statusMessage = 'Category added.';
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = $this->shopRecord(Category::class, $categoryId);
        $this->authorize('delete', $category);

        if ($category->products()->exists()) {
            $this->addError('category_name', 'Move or delete products in this category first.');

            return;
        }

        $category->delete();
    }

    public function createAttribute(): void
    {
        $shop = $this->currentShop();
        $this->authorize('create', VariantAttribute::class);

        $validated = $this->validate([
            'attribute_name' => ['required', 'string', 'max:255'],
            'attribute_values' => ['required', 'string', 'max:2000'],
        ]);

        $values = collect(explode(',', $validated['attribute_values']))
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->unique()
            ->values();

        if ($values->isEmpty()) {
            $this->addError('attribute_values', 'Add at least one value, separated by commas.');

            return;
        }

        $attribute = $shop->variantAttributes()->create([
            'shop_id' => $shop->id,
            'name' => $validated['attribute_name'],
            'sort_order' => $shop->variantAttributes()->count(),
        ]);

        foreach ($values as $index => $name) {
            $attribute->values()->create([
                'shop_id' => $shop->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $index,
            ]);
        }

        $this->reset(['attribute_name', 'attribute_values']);
        $this->statusMessage = 'Variant type added.';
    }

    public function saveCategoryAttributes(int $categoryId): void
    {
        $shop = $this->currentShop();
        $category = $this->shopRecord(Category::class, $categoryId);
        $this->authorize('update', $category);

        $attributeIds = NormalizeSelectedIds::forKey($this->categoryAttributeIds, $categoryId);

        $validIds = VariantAttribute::query()
            ->whereBelongsTo($shop)
            ->whereKey($attributeIds->all())
            ->pluck('id');

        $sync = [];

        foreach ($validIds as $index => $attributeId) {
            $sync[$attributeId] = [
                'is_required' => true,
                'sort_order' => $index,
            ];
        }

        $category->variantAttributes()->sync($sync);
        $this->statusMessage = 'Category attributes saved.';
    }

    /**
     * @param  Collection<int, VariantAttribute>  $attributes
     * @param  Collection<int, int>  $selectedIds
     * @return array<int, bool>
     */
    private function checkboxMap(Collection $attributes, Collection $selectedIds): array
    {
        $map = [];

        foreach ($attributes as $attribute) {
            $map[$attribute->id] = $selectedIds->contains($attribute->id);
        }

        return $map;
    }

    public function render()
    {
        $shop = $this->currentShop();
        $categories = Category::query()
            ->whereBelongsTo($shop)
            ->with(['variantAttributes', 'products'])
            ->withCount('products')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $attributes = VariantAttribute::query()
            ->whereBelongsTo($shop)
            ->with('values')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($categories as $category) {
            $selectedIds = NormalizeSelectedIds::forKey($this->categoryAttributeIds, $category->id);

            if ($selectedIds->isEmpty() && ! array_key_exists($category->id, $this->categoryAttributeIds)) {
                $selectedIds = $category->variantAttributes->pluck('id');
            }

            $this->categoryAttributeIds[$category->id] = $this->checkboxMap($attributes, $selectedIds);
        }

        return view('livewire.shop.categories', [
            'shop' => $shop,
            'categories' => $categories,
            'attributes' => $attributes,
        ]);
    }
}
