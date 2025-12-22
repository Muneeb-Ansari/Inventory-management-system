<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Purchase, Location, Inventory};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchaseListComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $locationFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $perPage = 20;
    public $locations;

    protected $queryString = [
        'search' => ['except' => ''],
        'locationFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Inventory::class);

        $this->locations = Location::active()->get();
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->locationFilter = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function deletePurchase($purchaseId)
    {
        $this->authorize('delete', Inventory::class);

        try {
            $purchase = Purchase::findOrFail($purchaseId);
            $purchase->delete();

            
             $this->dispatch(
                'toast',
                type: 'success',
                message: 'Purchase deleted successfully!'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Error: Unable to delete purchase',
            );
        }
    }

    public function render()
    {
        $purchases = Purchase::with(['location', 'user', 'items'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('purchase_no', 'like', "%{$this->search}%")
                      ->orWhere('supplier_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->locationFilter, function($query) {
                $query->where('location_id', $this->locationFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        // Calculate totals
        $totalAmount = Purchase::when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('purchase_no', 'like', "%{$this->search}%")
                      ->orWhere('supplier_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->locationFilter, function($query) {
                $query->where('location_id', $this->locationFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->sum('total_amount');

        return view('livewire.purchase-list-component', [
            'purchases' => $purchases,
            'totalAmount' => $totalAmount,
        ])->layout('layouts.app');
    }
}
