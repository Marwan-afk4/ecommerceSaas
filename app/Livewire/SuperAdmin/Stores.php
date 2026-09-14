<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Shop;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Stores')]
class Stores extends Component
{
    use WithPagination;

    public function toggleShop(int $shopId): void
    {
        $shop = Shop::query()->findOrFail($shopId);
        $this->authorize('update', $shop);
        $shop->update(['is_active' => ! $shop->is_active]);
    }

    public function deleteShop(int $shopId): void
    {
        $shop = Shop::query()->findOrFail($shopId);
        $this->authorize('delete', $shop);
        $shop->delete();
    }

    public function render()
    {
        $this->authorize('viewAny', Shop::class);

        return view('livewire.super-admin.stores', [
            'shops' => Shop::query()
                ->withCount(['products', 'users', 'orders'])
                ->orderBy('name')
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
