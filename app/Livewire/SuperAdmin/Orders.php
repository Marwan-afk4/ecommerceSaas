<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Orders')]
class Orders extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function render()
    {
        $this->authorize('viewAny', Order::class);

        return view('livewire.super-admin.orders', [
            'orders' => Order::query()
                ->with(['shop', 'items'])
                ->when(
                    $this->status !== '' && OrderStatus::tryFrom($this->status),
                    fn ($query) => $query->where('status', $this->status),
                )
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15),
            'statuses' => OrderStatus::cases(),
        ]);
    }
}
