<?php

namespace App\Livewire\Shop;

use App\Actions\Catalog\EnsureDefaultCategory;
use App\Enums\ProductStatus;
use App\Livewire\Concerns\InteractsWithCurrentShop;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Products')]
class Products extends Component
{
    use InteractsWithCurrentShop;
    use WithPagination;

    public string $product_name = '';

    public string $product_description = '';

    public ?int $category_id = null;

    public ?string $statusMessage = null;

    public function createProduct(EnsureDefaultCategory $ensureDefaultCategory): void
    {
        $shop = $this->currentShop();
        $this->authorize('create', Product::class);

        if (! $this->category_id) {
            $this->category_id = null;
        }
        $validated = $this->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_description' => ['nullable', 'string', 'max:5000'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('shop_id', $shop->id),
            ],
        ]);

        $category = isset($validated['category_id'])
            ? Category::query()->whereBelongsTo($shop)->whereKey($validated['category_id'])->first()
            : null;

        $category ??= $ensureDefaultCategory->handle($shop);

        Product::query()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => $validated['product_name'],
            'description' => $validated['product_description'] ?: null,
            'status' => ProductStatus::Active,
        ]);

        $this->reset(['product_name', 'product_description', 'category_id']);
        $this->resetPage();
        $this->statusMessage = 'Product added.';
    }

    public function deleteProduct(int $productId): void
    {
        $product = $this->shopRecord(Product::class, $productId);
        $this->authorize('delete', $product);
        $product->delete();
    }

    public function render()
    {
        $shop = $this->currentShop();

        return view('livewire.shop.products', [
            'shop' => $shop,
            'categories' => Category::query()
                ->whereBelongsTo($shop)
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
            'products' => Product::query()
                ->whereBelongsTo($shop)
                ->with('category')
                ->withCount('variants')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }
}
