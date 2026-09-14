<?php

use App\Enums\ShopApplicationStatus;
use App\Enums\ShopRole;
use App\Livewire\Login;
use App\Livewire\SuperAdmin\StoreApplications;
use App\Models\Shop;
use App\Models\ShopApplication;
use App\Models\User;
use Livewire\Livewire;

it('redirects guests from store requests to the superadmin login', function () {
    $this->get(route('superadmin.applications'))->assertRedirect(route('superadmin.login'));
});

it('forbids shop owners from opening store requests', function () {
    $shop = Shop::factory()->create();
    $owner = User::factory()->create();
    $shop->addMember($owner, ShopRole::Owner);

    $this->actingAs($owner)
        ->get(route('superadmin.applications'))
        ->assertForbidden();
});

it('lists pending store requests for a platform admin', function () {
    $admin = User::factory()->platformAdmin()->create();
    ShopApplication::factory()->pending()->create(['name' => 'Pending Store']);
    ShopApplication::factory()->accepted()->create(['name' => 'Accepted Store']);

    $this->actingAs($admin);

    Livewire::test(StoreApplications::class)
        ->assertSee('Pending Store', false)
        ->assertDontSee('Accepted Store', false)
        ->set('status', ShopApplicationStatus::Accepted->value)
        ->assertSee('Accepted Store', false);
});

it('lets a platform admin accept a store request and create the shop', function () {
    $admin = User::factory()->platformAdmin()->create();
    $application = ShopApplication::factory()->pending()->create([
        'name' => 'Acme Phones',
        'admin_name' => 'Ada Owner',
        'admin_email' => 'ada@acme.test',
        'admin_password' => 'password',
    ]);

    $this->actingAs($admin);

    Livewire::test(StoreApplications::class)
        ->call('accept', $application->id)
        ->assertHasNoErrors()
        ->assertRedirect(route('superadmin.applications', ['status' => 'pending']));

    $application->refresh();
    $shop = Shop::query()->where('name', 'Acme Phones')->first();
    $owner = User::query()->where('email', 'ada@acme.test')->first();

    expect($application->status)->toBe(ShopApplicationStatus::Accepted)
        ->and($application->admin_password)->toBeNull()
        ->and($application->shop_id)->toBe($shop?->id)
        ->and($shop)->not->toBeNull()
        ->and($owner)->not->toBeNull()
        ->and($owner->isOwnerOf($shop))->toBeTrue();

    Livewire::test(Login::class)
        ->set('email', 'ada@acme.test')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('shop.dashboard'));
});

it('lets a platform admin reject a store request without creating a shop', function () {
    $admin = User::factory()->platformAdmin()->create();
    $application = ShopApplication::factory()->pending()->create([
        'name' => 'Rejected Phones',
        'admin_email' => 'reject@acme.test',
    ]);

    $this->actingAs($admin);

    Livewire::test(StoreApplications::class)
        ->call('reject', $application->id)
        ->assertHasNoErrors()
        ->assertRedirect(route('superadmin.applications', ['status' => 'pending']));

    $application->refresh();

    expect($application->status)->toBe(ShopApplicationStatus::Rejected)
        ->and($application->admin_password)->toBeNull();

    $this->assertDatabaseMissing('shops', ['name' => 'Rejected Phones']);
    $this->assertDatabaseMissing('users', ['email' => 'reject@acme.test']);

    Livewire::test(Login::class)
        ->set('email', 'reject@acme.test')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email']);
});

it('does not accept a store request that was already reviewed', function () {
    $admin = User::factory()->platformAdmin()->create();
    $application = ShopApplication::factory()->pending()->create([
        'name' => 'Acme Phones',
        'admin_email' => 'ada@acme.test',
    ]);

    $this->actingAs($admin);

    Livewire::test(StoreApplications::class)
        ->call('accept', $application->id)
        ->assertHasNoErrors()
        ->assertRedirect(route('superadmin.applications', ['status' => 'pending']));

    Livewire::test(StoreApplications::class)
        ->call('accept', $application->id)
        ->assertHasErrors(['status' => 'This store request has already been reviewed.']);

    expect(Shop::query()->where('name', 'Acme Phones')->count())->toBe(1);
});

it('escapes untrusted store request copy', function () {
    $admin = User::factory()->platformAdmin()->create();
    ShopApplication::factory()->pending()->create([
        'name' => "<script>alert('xss')</script>",
        'tagline' => "<script>alert('tag')</script>",
        'description' => "<script>alert('bio')</script>",
        'public_owner_name' => "<script>alert('public')</script>",
        'admin_name' => "<script>alert('admin')</script>",
        'admin_email' => 'safe@acme.test',
    ]);

    $this->actingAs($admin)
        ->get(route('superadmin.applications'))
        ->assertOk()
        ->assertSee('&lt;script&gt;', false)
        ->assertDontSee("<script>alert('xss')</script>", false)
        ->assertDontSee("<script>alert('tag')</script>", false)
        ->assertDontSee("<script>alert('bio')</script>", false)
        ->assertDontSee("<script>alert('public')</script>", false)
        ->assertDontSee("<script>alert('admin')</script>", false);
});
