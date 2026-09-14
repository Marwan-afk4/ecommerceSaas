<?php

namespace App\Actions;

use App\Enums\ShopApplicationStatus;
use App\Models\ShopApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class SubmitShopApplication
{
    /**
     * @param  array{
     *     name: string,
     *     tagline: ?string,
     *     description: ?string,
     *     public_owner_name: string,
     *     public_email: ?string,
     *     public_phone: ?string,
     *     logo: UploadedFile,
     *     admin_name: string,
     *     admin_email: string,
     *     admin_password: string,
     * }  $data
     */
    public function handle(array $data): ShopApplication
    {
        if (User::query()->where('email', $data['admin_email'])->exists()) {
            throw ValidationException::withMessages([
                'admin_email' => 'That email is already registered.',
            ]);
        }

        $pendingExists = ShopApplication::query()
            ->pending()
            ->where('admin_email', $data['admin_email'])
            ->exists();

        if ($pendingExists) {
            throw ValidationException::withMessages([
                'admin_email' => 'A store request with this email is already waiting for approval.',
            ]);
        }

        return ShopApplication::query()->create([
            'name' => $data['name'],
            'tagline' => $data['tagline'],
            'description' => $data['description'],
            'public_owner_name' => $data['public_owner_name'],
            'public_email' => $data['public_email'],
            'public_phone' => $data['public_phone'],
            'logo_path' => $data['logo']->store('shops/logos', 'public'),
            'admin_name' => $data['admin_name'],
            'admin_email' => $data['admin_email'],
            'admin_password' => $data['admin_password'],
            'status' => ShopApplicationStatus::Pending,
        ]);
    }
}
