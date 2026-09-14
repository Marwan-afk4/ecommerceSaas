<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopApplication;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Superadmin')]
class Home extends Component
{
    public function render()
    {
        $this->authorize('viewAny', Shop::class);

        return view('livewire.super-admin.home', [
            'shopCount' => Shop::query()->count(),
            'userCount' => User::query()->count(),
            'productCount' => Product::query()->count(),
            'pendingOrderCount' => Order::query()->pending()->count(),
            'pendingApplicationCount' => ShopApplication::query()->pending()->count(),
            'recentShops' => Shop::query()
                ->withCount(['products', 'users'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'recentOrders' => Order::query()
                ->with('shop')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'recentApplications' => ShopApplication::query()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
