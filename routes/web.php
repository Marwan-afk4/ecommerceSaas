<?php

use App\Http\Middleware\EnsureShopMember;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\SetCurrentShop;
use App\Http\Middleware\SetShopContext;
use App\Livewire\HomePage;
use App\Livewire\Login;
use App\Livewire\RegisterShop;
use App\Livewire\Shop\Admins as ShopAdmins;
use App\Livewire\Shop\Categories as ShopCategories;
use App\Livewire\Shop\Home as ShopHome;
use App\Livewire\Shop\Orders as ShopOrders;
use App\Livewire\Shop\ProductEditor as ShopProductEditor;
use App\Livewire\Shop\Products as ShopProducts;
use App\Livewire\ShopStorefront;
use App\Livewire\SuperAdmin\Home as SuperAdminHome;
use App\Livewire\SuperAdmin\Orders as SuperAdminOrders;
use App\Livewire\SuperAdmin\Products as SuperAdminProducts;
use App\Livewire\SuperAdmin\StoreApplications as SuperAdminStoreApplications;
use App\Livewire\SuperAdmin\Stores as SuperAdminStores;
use App\Livewire\SuperAdmin\Users as SuperAdminUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/s/{shop:slug}', ShopStorefront::class)
    ->middleware(SetShopContext::class)
    ->name('shops.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/start', RegisterShop::class)
        ->middleware('throttle:10,1')
        ->name('shops.create');
    Route::get('/login', Login::class)->name('login');
    Route::get('/superadmin/login', Login::class)->name('superadmin.login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware(['auth', EnsureShopMember::class, SetCurrentShop::class])->group(function (): void {
    Route::get('/admin', ShopHome::class)->name('shop.dashboard');
    Route::get('/admin/products', ShopProducts::class)->name('shop.products');
    Route::get('/admin/products/{product}', ShopProductEditor::class)->name('shop.products.edit');
    Route::get('/admin/categories', ShopCategories::class)->name('shop.categories');
    Route::get('/admin/admins', ShopAdmins::class)->name('shop.admins');
    Route::get('/admin/orders', ShopOrders::class)->name('shop.orders');
});

Route::middleware(['auth', EnsureSuperAdmin::class])->group(function (): void {
    Route::get('/superadmin', SuperAdminHome::class)->name('superadmin.dashboard');
    Route::get('/superadmin/shops', SuperAdminStores::class)->name('superadmin.shops');
    Route::get('/superadmin/applications', SuperAdminStoreApplications::class)->name('superadmin.applications');
    Route::get('/superadmin/users', SuperAdminUsers::class)->name('superadmin.users');
    Route::get('/superadmin/products', SuperAdminProducts::class)->name('superadmin.products');
    Route::get('/superadmin/orders', SuperAdminOrders::class)->name('superadmin.orders');
});
