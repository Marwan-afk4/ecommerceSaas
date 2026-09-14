<?php

namespace App\Livewire\Shop;

use App\Actions\Catalog\GenerateProductVariants;
use App\Actions\Catalog\NormalizeSelectedIds;
use App\Enums\ProductStatus;
use App\Livewire\Concerns\InteractsWithCurrentShop;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Product variants')]
class ProductEditor extends Component
{
    use InteractsWithCurrentShop;

    public Product $product;

    public string $name = '';

    public string $description = '';

    public int $category_id;

    public string $status = ProductStatus::Draft->value;

    /**
     * @var array<int|string, list<int|string>>
     */
    public array $selectedValues = [];

    /**
     * @var array<int|string, string>
     */
    public array $variantPrices = [];

    /**
     * @var array<int|string, int|string>
     */
    public array $variantStocks = [];

    public ?string $statusMessage = null;

    public function mount(Product $product): void
    {
        abort_unless($product->shop_id === $this->currentShop()->id, 404);
        $this->authorize('update', $product);

        $this->product = $product;
        $this->name = $product->name;
        $this->description = (string) $product->description;
        $this->category_id = $product->category_id;
        $this->status = $product->status->value;
        $this->hydrateVariants();
    }

    public function saveProduct(): void
    {
        $shop = $this->currentShop();
        $this->authorize('update', $this->product);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('shop_id', $shop->id),
            ],
            'status' => ['required', Rule::enum(ProductStatus::class)],
        ]);

        $this->product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
        ]);

        $this->product->refresh();
        $this->statusMessage = 'Product details saved.';
    }

    public function generateVariants(GenerateProductVariants $generate): void
    {
        $this->authorize('update', $this->product);

        $generate->handle($this->product, $this->selectedValues);
        $this->product->refresh();
        $this->hydrateVariants();
        $this->statusMessage = 'Variants generated.';
    }

    public function saveVariant(int $variantId): void
    {
        $variant = $this->shopRecord(ProductVariant::class, $variantId);
        abort_unless($variant->product_id === $this->product->id, 404);
        $this->authorize('update', $variant);

        $this->validate([
            "variantPrices.{$variantId}" => ['required', 'numeric', 'min:0'],
            "variantStocks.{$variantId}" => ['required', 'integer', 'min:0'],
        ]);

        $variant->update([
            'price' => $this->variantPrices[$variantId],
            'stock' => $this->variantStocks[$variantId],
        ]);

        $this->statusMessage = 'Variant updated.';
    }

    public function render()
    {
        $shop = $this->currentShop();
        $this->product->load([
            'category.variantAttributes.values',
            'variants.attributeValues',
        ]);

        foreach ($this->product->category->variantAttributes as $attribute) {
            $this->selectedValues[$attribute->id] = $this->checkboxMap(
                $attribute,
                NormalizeSelectedIds::forKey($this->selectedValues, $attribute->id),
            );
        }

        return view('livewire.shop.product-editor', [
            'shop' => $shop,
            'categories' => Category::query()
                ->whereBelongsTo($shop)
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
            'attributes' => $this->product->category->variantAttributes,
        ]);
    }

    private function hydrateVariants(): void
    {
        $this->product->load(['variants.attributeValues']);

        foreach ($this->product->variants as $variant) {
            $this->variantPrices[$variant->id] = (string) $variant->price;
            $this->variantStocks[$variant->id] = $variant->stock;
        }
    }

    /**
     * @param  Collection<int, int>  $selectedIds
     * @return array<int, bool>
     */
    private function checkboxMap(VariantAttribute $attribute, Collection $selectedIds): array
    {
        $map = [];

        foreach ($attribute->values as $value) {
            $map[$value->id] = $selectedIds->contains($value->id);
        }

        return $map;
    }
}
