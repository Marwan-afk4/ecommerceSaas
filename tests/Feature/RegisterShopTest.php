<?php

use App\Enums\ShopApplicationStatus;
use App\Livewire\RegisterShop;
use App\Models\ShopApplication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('saves a store request without creating a shop or owner', function () {
    Storage::fake('public');

    Livewire::test(RegisterShop::class)
        ->set('name', 'Acme Phones')
        ->set('tagline', 'Better calls')
        ->set('description', 'We sell phones.')
        ->set('public_owner_name', 'Ada Owner')
        ->set('public_email', 'hello@acme.test')
        ->set('public_phone', '555-0100')
        ->set('logo', UploadedFile::fake()->image('store.jpg'))
        ->set('admin_name', 'Ada Owner')
        ->set('admin_email', 'ada@acme.test')
        ->set('admin_password', 'password')
        ->set('admin_password_confirmation', 'password')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('submitted', true)
        ->assertSee('Request sent', false)
        ->assertSee('Superadmin will accept or reject Acme Phones', false);

    $this->assertDatabaseHas('shop_applications', [
        'name' => 'Acme Phones',
        'admin_email' => 'ada@acme.test',
        'status' => ShopApplicationStatus::Pending->value,
    ]);
    $this->assertDatabaseMissing('shops', ['name' => 'Acme Phones']);
    $this->assertDatabaseMissing('users', ['email' => 'ada@acme.test']);
    $this->assertGuest();

    $application = ShopApplication::query()->where('admin_email', 'ada@acme.test')->first();

    expect($application)->not->toBeNull();
    Storage::disk('public')->assertExists($application->logo_path);
});

it('rejects an empty store signup', function () {
    Livewire::test(RegisterShop::class)
        ->call('save')
        ->assertHasErrors(['name', 'logo', 'public_owner_name', 'admin_name', 'admin_email', 'admin_password']);
});

it('rejects a store request when the admin email already has an account', function () {
    Storage::fake('public');
    User::factory()->create(['email' => 'ada@acme.test']);

    Livewire::test(RegisterShop::class)
        ->set('name', 'Acme Phones')
        ->set('public_owner_name', 'Ada Owner')
        ->set('logo', UploadedFile::fake()->image('store.jpg'))
        ->set('admin_name', 'Ada Owner')
        ->set('admin_email', 'ada@acme.test')
        ->set('admin_password', 'password')
        ->set('admin_password_confirmation', 'password')
        ->call('save')
        ->assertHasErrors(['admin_email' => 'That email is already registered or waiting for approval.']);

    $this->assertDatabaseMissing('shop_applications', ['admin_email' => 'ada@acme.test']);
});

it('rejects a second pending store request with the same admin email', function () {
    Storage::fake('public');
    ShopApplication::factory()->pending()->create(['admin_email' => 'ada@acme.test']);

    Livewire::test(RegisterShop::class)
        ->set('name', 'Acme Phones')
        ->set('public_owner_name', 'Ada Owner')
        ->set('logo', UploadedFile::fake()->image('store.jpg'))
        ->set('admin_name', 'Ada Owner')
        ->set('admin_email', 'ada@acme.test')
        ->set('admin_password', 'password')
        ->set('admin_password_confirmation', 'password')
        ->call('save')
        ->assertHasErrors(['admin_email' => 'That email is already registered or waiting for approval.']);

    expect(ShopApplication::query()->where('admin_email', 'ada@acme.test')->count())->toBe(1);
});
