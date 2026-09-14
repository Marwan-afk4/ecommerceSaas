<?php

namespace App\Livewire\Shop;

use App\Enums\OrderStatus;
use App\Livewire\Concerns\InteractsWithCurrentShop;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Order requests')]
class Orders extends Component
{
    use InteractsWithCurrentShop;
    use WithPagination;

    #[Url]
    public string $status = '';

    public function accept(int $orderId): void
    {
        $this->transition($orderId, OrderStatus::Accepted, [OrderStatus::Pending]);
    }

    public function reject(int $orderId): void
    {
        $this->transition($orderId, OrderStatus::Rejected, [OrderStatus::Pending, OrderStatus::Accepted]);
    }

    public function fulfill(int $orderId): void
    {
        $this->transition($orderId, OrderStatus::Fulfilled, [OrderStatus::Accepted]);
    }

    public function render()
    {
        $shop = $this->currentShop();
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->whereBelongsTo($shop)
            ->with('items')
            ->when(
                $this->status !== '' && OrderStatus::tryFrom($this->status),
                fn ($query) => $query->where('status', $this->status),
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.shop.orders', [
            'shop' => $shop,
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    /**
     * @param  list<OrderStatus>  $allowedFrom
     */
    private function transition(int $orderId, OrderStatus $to, array $allowedFrom): void
    {
        $order = $this->shopRecord(Order::class, $orderId);
        $this->authorize('update', $order);

        if (! in_array($order->status, $allowedFrom, true)) {
            $this->addError('status', 'This order cannot move to '.$to->label().' from '.$order->status->label().'.');

            return;
        }

        $order->update(['status' => $to]);
    }
}
