<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Policies\InventoryPolicy;
// use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interface to Implementation
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        \Gate::define('viewReport', [InventoryPolicy::class, 'viewReport']);
        \Gate::define('downloadReport', [InventoryPolicy::class, 'downloadReport']);
        \Gate::define('createPurchase', [InventoryPolicy::class, 'createPurchase']);
        \Gate::define('createSale', [InventoryPolicy::class, 'createSale']);
        \Gate::define('setOpeningStock', [InventoryPolicy::class, 'setOpeningStock']);
    }
}
