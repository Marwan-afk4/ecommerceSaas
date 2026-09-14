<?php

namespace App\Actions;

use App\Enums\ShopApplicationStatus;
use App\Models\Shop;
use App\Models\ShopApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewShopApplication
{
    public function __construct(private RegisterShop $registerShop) {}

    public function accept(ShopApplication $application): Shop
    {
        return DB::transaction(function () use ($application): Shop {
            $application = ShopApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            $this->ensurePending($application);

            $shop = $this->registerShop->handle([
                'name' => $application->name,
                'tagline' => $application->tagline,
                'description' => $application->description,
                'public_owner_name' => $application->public_owner_name,
                'public_email' => $application->public_email,
                'public_phone' => $application->public_phone,
                'logo' => $application->logo_path,
                'admin_name' => $application->admin_name,
                'admin_email' => $application->admin_email,
                'admin_password' => $application->admin_password,
            ]);

            $application->update([
                'status' => ShopApplicationStatus::Accepted,
                'shop_id' => $shop->id,
                'admin_password' => null,
                'reviewed_at' => now(),
            ]);

            return $shop;
        });
    }

    public function reject(ShopApplication $application): void
    {
        DB::transaction(function () use ($application): void {
            $application = ShopApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            $this->ensurePending($application);

            $application->update([
                'status' => ShopApplicationStatus::Rejected,
                'admin_password' => null,
                'reviewed_at' => now(),
            ]);
        });
    }

    private function ensurePending(ShopApplication $application): void
    {
        if ($application->status->isPending()) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => 'This store request has already been reviewed.',
        ]);
    }
}
