<?php

namespace App\Livewire\Shop;

use App\Actions\AddShopAdmin;
use App\Enums\ShopRole;
use App\Livewire\Concerns\InteractsWithCurrentShop;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Store admins')]
class Admins extends Component
{
    use InteractsWithCurrentShop;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = ShopRole::Manager->value;

    public function addAdmin(AddShopAdmin $addShopAdmin): void
    {
        $shop = $this->currentShop();
        $this->authorize('update', $shop);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::enum(ShopRole::class)],
        ]);

        $addShopAdmin->handle($shop, $validated);
        $this->reset(['name', 'email', 'password', 'role']);
        $this->role = ShopRole::Manager->value;
    }

    public function render()
    {
        $shop = $this->currentShop();

        return view('livewire.shop.admins', [
            'shop' => $shop,
            'canManageAdmins' => auth()->user()?->can('update', $shop) ?? false,
            'admins' => $shop->users()
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
            'roles' => ShopRole::cases(),
        ]);
    }
}
