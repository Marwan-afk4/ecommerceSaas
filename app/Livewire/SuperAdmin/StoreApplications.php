<?php

namespace App\Livewire\SuperAdmin;

use App\Actions\ReviewShopApplication;
use App\Enums\ShopApplicationStatus;
use App\Models\ShopApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.dashboard')]
#[Title('Store requests')]
class StoreApplications extends Component
{
    use WithPagination;

    #[Url]
    public string $status = 'pending';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function accept(int $applicationId, ReviewShopApplication $review): void
    {
        $application = ShopApplication::query()->findOrFail($applicationId);
        $this->authorize('update', $application);
        $review->accept($application);
        $this->redirect(route('superadmin.applications', array_filter(['status' => $this->status])), navigate: true);
    }

    public function reject(int $applicationId, ReviewShopApplication $review): void
    {
        $application = ShopApplication::query()->findOrFail($applicationId);
        $this->authorize('update', $application);
        $review->reject($application);
        $this->redirect(route('superadmin.applications', array_filter(['status' => $this->status])), navigate: true);
    }

    public function render()
    {
        $this->authorize('viewAny', ShopApplication::class);

        return view('livewire.super-admin.store-applications', [
            'applications' => ShopApplication::query()
                ->with('shop')
                ->when(
                    $this->status !== '' && ShopApplicationStatus::tryFrom($this->status),
                    fn ($query) => $query->where('status', $this->status),
                )
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15),
            'statuses' => ShopApplicationStatus::cases(),
        ]);
    }
}
