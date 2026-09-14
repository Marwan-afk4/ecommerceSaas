<?php

namespace App\Actions;

use App\Actions\Catalog\EnsureDefaultCategory;
use App\Enums\ShopRole;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterShop
{
    public function __construct(private EnsureDefaultCategory $ensureDefaultCategory) {}

    /**
     * @param  array{
     *     name: string,
     *     tagline: ?string,
     *     description: ?string,
     *     public_owner_name: string,
     *     public_email: ?string,
     *     public_phone: ?string,
     *     logo: UploadedFile|string,
     *     admin_name: string,
     *     admin_email: string,
     *     admin_password: string,
     * }  $data
     */
    public function handle(array $data): Shop
    {
        return DB::transaction(function () use ($data): Shop {
            $owner = User::query()->create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => $data['admin_password'],
                'is_platform_admin' => false,
            ]);

            $shop = Shop::query()->create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name']),
                'logo_path' => $this->storeLogo($data['logo']),
                'tagline' => $data['tagline'],
                'description' => $data['description'],
                'public_owner_name' => $data['public_owner_name'],
                'public_email' => $data['public_email'],
                'public_phone' => $data['public_phone'],
                'is_active' => true,
            ]);

            $shop->addMember($owner, ShopRole::Owner);
            $this->ensureDefaultCategory->handle($shop);

            return $shop;
        });
    }

    private function storeLogo(UploadedFile|string $logo): string
    {
        if ($logo instanceof UploadedFile) {
            return $logo->store('shops/logos', 'public');
        }

        return $logo;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'shop';
        }

        $slug = $base;
        $suffix = 1;

        while (Shop::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
