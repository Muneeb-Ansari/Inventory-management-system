<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\{
    InventoryReportComponent,
    PurchaseFormComponent,
    SaleFormComponent,
    ProductListComponent,
    ProductViewComponent,
    ProductFormComponent,
    DashboardComponent,
    PurchaseListComponent,
    PurchaseViewComponent,
    SaleListComponent,
    SaleViewComponent,
    LocationListComponent,
    LocationFormComponent,
    OpeningStockComponent,
    StockMovementComponent
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');

    // Inventory Reports
    Route::get('/reports/inventory', InventoryReportComponent::class)
        ->name('reports.inventory')
        ->middleware('can:viewReport,App\Policies\InventoryPolicy');

    // Purchases
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', PurchaseListComponent::class)->name('index');
        Route::get('/create', PurchaseFormComponent::class)
            ->name('create')
            ->middleware('can:createPurchase,App\Policies\InventoryPolicy');
        Route::get('/{id}', PurchaseViewComponent::class)->name('show');
    });

    // // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', SaleListComponent::class)->name('index');
        Route::get('/create', SaleFormComponent::class)
            ->name('create')
            ->middleware('can:createSale,App\Policies\InventoryPolicy');
        Route::get('/{id}', SaleViewComponent::class)->name('show');
    });

    // // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', ProductListComponent::class)->name('index');
        Route::get('/create', ProductFormComponent::class)->name('create');
        Route::get('/{id}', ProductViewComponent::class)->name('show');
    });

    // // Locations
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', LocationListComponent::class)->name('index');
        Route::get('/create', LocationFormComponent::class)->name('create');
    })->middleware('can:create,App\Policies\InventoryPolicy');

    // // Opening Stock
    Route::get('/opening-stock', OpeningStockComponent::class)
        ->name('opening-stock')
        ->middleware('can:setOpeningStock,App\Policies\InventoryPolicy');

    // Stock Movements
    Route::get('/stock-movements', StockMovementComponent::class)
    ->name('stock-movements')
    ->middleware('can:setOpeningStock,App\Policies\InventoryPolicy');
});

require __DIR__ . '/auth.php';
