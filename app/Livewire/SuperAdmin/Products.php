<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Products')]
class Products extends Component
{
    use WithPagination;

    public function deleteProduct(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        $this->authorize('delete', $product);
        $product->delete();
    }

    public function render()
    {
        $this->authorize('viewAny', Product::class);

        return view('livewire.super-admin.products', [
            'products' => Product::query()
                ->with(['shop', 'category'])
                ->orderByDesc('id')
                ->paginate(15),
        ]);
    }
}
