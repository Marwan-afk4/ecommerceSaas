<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\InteractsWithCurrentShop;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Shop admin')]
class Home extends Component
{
    use InteractsWithCurrentShop;

    public function render()
    {
        $shop = $this->currentShop();

        return view('livewire.shop.home', [
            'shop' => $shop,
            'productCount' => Product::query()->whereBelongsTo($shop)->count(),
            'categoryCount' => Category::query()->whereBelongsTo($shop)->count(),
            'pendingOrderCount' => Order::query()->whereBelongsTo($shop)->pending()->count(),
            'adminCount' => $shop->users()->count(),
            'recentOrders' => Order::query()
                ->whereBelongsTo($shop)
                ->with('items')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'recentProducts' => Product::query()
                ->whereBelongsTo($shop)
                ->with('category')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
