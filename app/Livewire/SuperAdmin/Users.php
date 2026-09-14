<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Users')]
class Users extends Component
{
    use WithPagination;

    public function deleteUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->authorize('delete', $user);
        $user->delete();
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        return view('livewire.super-admin.users', [
            'users' => User::query()
                ->with(['shops' => fn ($query) => $query->orderBy('name')->orderBy('id')])
                ->orderByDesc('is_platform_admin')
                ->orderBy('name')
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
