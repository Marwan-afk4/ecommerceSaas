<?php

namespace App\Livewire;

use App\Actions\SubmitShopApplication;
use App\Enums\ShopApplicationStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Request your store')]
class RegisterShop extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $tagline = '';

    public string $description = '';

    public string $public_owner_name = '';

    public string $public_email = '';

    public string $public_phone = '';

    public TemporaryUploadedFile|UploadedFile|null $logo = null;

    public string $admin_name = '';

    public string $admin_email = '';

    public string $admin_password = '';

    public string $admin_password_confirmation = '';

    public bool $submitted = false;

    public function updatedAdminName(string $value): void
    {
        if ($this->public_owner_name === '') {
            $this->public_owner_name = $value;
        }
    }

    public function save(SubmitShopApplication $submitShopApplication): void
    {
        $validated = $this->validate();

        /** @var UploadedFile $logo */
        $logo = $validated['logo'];

        $submitShopApplication->handle([
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?: null,
            'description' => $validated['description'] ?: null,
            'public_owner_name' => $validated['public_owner_name'],
            'public_email' => $validated['public_email'] ?: null,
            'public_phone' => $validated['public_phone'] ?: null,
            'logo' => $logo,
            'admin_name' => $validated['admin_name'],
            'admin_email' => $validated['admin_email'],
            'admin_password' => $validated['admin_password'],
        ]);

        $this->submitted = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'public_owner_name' => ['required', 'string', 'max:255'],
            'public_email' => ['nullable', 'email', 'max:255'],
            'public_phone' => ['nullable', 'string', 'max:50'],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                Rule::unique('shop_applications', 'admin_email')->where(
                    fn ($query) => $query->where('status', ShopApplicationStatus::Pending->value),
                ),
            ],
            'admin_password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'admin_email.unique' => 'That email is already registered or waiting for approval.',
        ];
    }

    public function render()
    {
        return view('livewire.register-shop');
    }
}
