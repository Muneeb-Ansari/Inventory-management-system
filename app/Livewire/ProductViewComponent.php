<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Product, Location};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductViewComponent extends Component
{
    use AuthorizesRequests;

    public $productId;
    public $product;
    public $selectedLocation;
    public $locations;

    public function mount($id)
    {
        $this->authorize('view', InventoryPolicy::class);

        $this->productId = $id;
        $this->product = Product::with(['purchaseItems.purchase', 'saleItems.sale'])
            ->findOrFail($id);

        $this->locations = Location::active()->get();
        $this->selectedLocation = $this->locations->first()?->id;
    }

    public function getCurrentStockProperty()
    {
        if (!$this->selectedLocation) {
            return 0;
        }
        return $this->product->getCurrentStock($this->selectedLocation);
    }

    public function getRecentPurchasesProperty()
    {
        return $this->product->purchaseItems()
            ->with('purchase.location')
            ->whereHas('purchase', function ($q) {
                $q->where('status', 'completed');
            })
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getRecentSalesProperty()
    {
        return $this->product->saleItems()
            ->with('sale.location')
            ->whereHas('sale', function ($q) {
                $q->where('status', 'completed');
            })
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getTotalPurchasedProperty()
    {
        return $this->product->purchaseItems()
            ->whereHas('purchase', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('quantity');
    }

    public function getTotalSoldProperty()
    {
        return $this->product->saleItems()
            ->whereHas('sale', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('quantity');
    }

    public function render()
    {
        return view('livewire.product-view-component')->layout('layouts.app');
    }
}
