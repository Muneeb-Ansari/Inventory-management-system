<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Policies\InventoryPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductListComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $statusFilter = '';
    public $discontinuedFilter = '';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'discontinuedFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('view', Inventory::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->discontinuedFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($productId)
    {
        $this->authorize('update', InventoryPolicy::class);

        try {
            $product = Product::findOrFail($productId);
            $product->update(['is_active' => !$product->is_active]);

            session()->flash('success', 'Product status updated!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function discontinueProduct($productId)
    {
        $this->authorize('update', InventoryPolicy::class);

        try {
            $product = Product::findOrFail($productId);
            $product->discontinue();

            session()->flash('success', 'Product marked as discontinued!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteProduct($productId)
    {
        $this->authorize('delete', InventoryPolicy::class);

        try {
            $product = Product::findOrFail($productId);

            // Check if product has transactions
            if ($product->purchaseItems()->count() > 0 || $product->saleItems()->count() > 0) {
                session()->flash('error', 'Cannot delete product with existing transactions! You can discontinue it instead.');
                return;
            }

            $product->delete();
            session()->flash('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, function($query) {
                $query->search($this->search);
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->when($this->discontinuedFilter !== '', function($query) {
                if ($this->discontinuedFilter == '1') {
                    $query->discontinued();
                } else {
                    $query->notDiscontinued();
                }
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.product-list-component', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
