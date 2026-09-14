<?php

namespace App\Livewire;

use App\Actions\PlaceOrderRequest;
use App\Enums\ProductStatus;
use App\Models\Shop;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ShopStorefront extends Component
{
    public Shop $shop;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public ?int $product_id = null;

    public ?int $product_variant_id = null;

    public int $quantity = 1;

    public ?string $orderSubmitted = null;

    public function mount(Shop $shop): void
    {
        abort_unless($shop->is_active, 404);

        $this->shop = $shop->load([
            'products' => fn ($query) => $query
                ->where('status', ProductStatus::Active)
                ->with(['category', 'variants.attributeValues'])
                ->orderBy('name')
                ->orderBy('id'),
        ]);
    }

    public function updatedProductId(): void
    {
        $this->product_variant_id = null;
    }

    public function requestOrder(PlaceOrderRequest $placeOrder): void
    {
        $validated = $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'product_id' => ['required', 'integer'],
            'product_variant_id' => ['nullable', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $placeOrder->handle($this->shop, $validated);

        $this->reset(['customer_name', 'customer_email', 'customer_phone', 'product_id', 'product_variant_id', 'quantity']);
        $this->quantity = 1;
        $this->orderSubmitted = 'Your order request was sent to the store.';
    }

    public function render()
    {
        $selectedProduct = $this->shop->products->firstWhere('id', $this->product_id);

        return view('livewire.shop-storefront', [
            'selectedProduct' => $selectedProduct,
        ])->title($this->shop->name);
    }
}
