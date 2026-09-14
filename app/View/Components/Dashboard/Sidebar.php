<?php

namespace App\View\Components\Dashboard;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopApplication;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public function __construct(public string $area) {}

    public function currentShop(): ?Shop
    {
        $shopId = Shop::currentId();

        if ($shopId === null) {
            return null;
        }

        return Shop::query()->find($shopId);
    }

    public function pendingOrderCount(): int
    {
        $query = Order::query()->where('status', OrderStatus::Pending);

        if ($this->area === 'shop') {
            $shopId = Shop::currentId();

            if ($shopId === null) {
                return 0;
            }

            $query->where('shop_id', $shopId);
        }

        return $query->count();
    }

    public function pendingApplicationCount(): int
    {
        if ($this->area !== 'superadmin') {
            return 0;
        }

        return ShopApplication::query()->pending()->count();
    }

    public function render(): View
    {
        return view('components.dashboard.sidebar');
    }
}
