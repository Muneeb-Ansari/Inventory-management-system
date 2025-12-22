<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Product, Purchase, Sale, Location};
use Spatie\Permission\Models\Role;

class DashboardComponent extends Component
{
    public $selectedLocation;
    public $locations;

    public function mount()
    {

        $this->locations = Location::active()->get();
        $this->selectedLocation = $this->locations->first()?->id;
    }

    public function getTotalProducts()
    {
        return Product::active()->count();
    }

    public function getTodayPurchases()
    {
        return Purchase::whereDate('purchase_date', today())
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->count();
    }

    public function getTodayPurchaseAmount()
    {
        return Purchase::whereDate('purchase_date', today())
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->sum('total_amount');
    }

    public function getTodaySales()
    {
        return Sale::whereDate('sale_date', today())
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->count();
    }

    public function getTodaySaleAmount()
    {
        return Sale::whereDate('sale_date', today())
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->sum('total_amount');
    }

    public function getLowStockItems()
    {
        if (!$this->selectedLocation) {
            return 0;
        }

        return Product::active()
            ->whereHas('stockMovements', function ($q) {
                $q->where('location_id', $this->selectedLocation)
                    ->whereRaw('balance < products.minimum_stock');
            })
            ->count();
    }

    public function getRecentPurchases()
    {
        return Purchase::with('location')
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->orderBy('purchase_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getRecentSales()
    {
        return Sale::with('location')
            ->where('status', 'completed')
            ->when($this->selectedLocation, fn($q) => $q->where('location_id', $this->selectedLocation))
            ->orderBy('sale_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getLowStockProducts()
    {
        if (!$this->selectedLocation) {
            return collect();
        }

        $products = Product::active()
            ->select('products.*')
            ->get()
            ->map(function ($product) {
                $currentStock = $product->getCurrentStock($this->selectedLocation);
                $product->current_stock = $currentStock;
                return $product;
            })
            ->filter(function ($product) {
                return $product->current_stock < $product->minimum_stock;
            })
            ->take(10);

        return $products;
    }


    public function render()
    {
        return view('livewire.dashboard-component',[
            'totalProducts' => self::getTotalProducts(),
            'todayPurchases' => self::getTodayPurchases(),
            'todayPurchaseAmount' => self::getTodayPurchaseAmount(),
            'todaySales' => self::getTodaySales(),
            'todaySaleAmount' => self::getTodaySaleAmount(),
            'lowStockItems' => self::getLowStockItems(),
            'recentPurchases' => self::getRecentPurchases(),
            'recentSales' => self::getRecentSales(),
            'lowStockProducts' => self::getLowStockProducts(),
        ])->layout('layouts.app');
    }
}
