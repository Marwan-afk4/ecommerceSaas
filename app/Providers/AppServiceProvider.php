<?php

namespace App\Providers;

use App\Http\Middleware\SetCurrentShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Livewire::addPersistentMiddleware([
            SetCurrentShop::class,
        ]);
    }
}
