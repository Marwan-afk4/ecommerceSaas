<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrderRequest
{
    /**
     * @param  array{
     *     customer_name: string,
     *     customer_email: string,
     *     customer_phone: ?string,
     *     product_id: int,
     *     product_variant_id?: int|null,
     *     quantity: int,
     *     notes?: ?string,
     * }  $data
     */
    public function handle(Shop $shop, array $data): Order
    {
        abort_unless($shop->is_active, 404);

        $product = Product::query()
            ->whereBelongsTo($shop)
            ->where('status', ProductStatus::Active)
            ->with(['variants' => fn ($query) => $query->orderByDesc('is_default')->orderBy('id')])
            ->whereKey($data['product_id'])
            ->first();

        abort_unless($product instanceof Product, 404);

        $variant = $this->resolveVariant($product, $data['product_variant_id'] ?? null);

        return DB::transaction(function () use ($shop, $data, $product, $variant): Order {
            $order = Order::query()->create([
                'shop_id' => $shop->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?: null,
                'status' => OrderStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->create([
                'shop_id' => $shop->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'name' => $this->itemName($product, $variant),
                'quantity' => $data['quantity'],
                'unit_price' => $variant?->price ?? '0.00',
            ]);

            return $order->load('items');
        });
    }

    private function resolveVariant(Product $product, mixed $variantId): ?ProductVariant
    {
        if ($product->variants->isEmpty()) {
            return null;
        }

        $variant = $product->variants->first(
            fn (ProductVariant $candidate): bool => $candidate->id === (int) $variantId,
        );

        if ($variant instanceof ProductVariant) {
            return $variant;
        }

        throw ValidationException::withMessages([
            'product_variant_id' => 'Select a variant for this product.',
        ]);
    }

    private function itemName(Product $product, ?ProductVariant $variant): string
    {
        if (! $variant instanceof ProductVariant) {
            return $product->name;
        }

        return $product->name.' ('.$variant->sku.')';
    }
}
