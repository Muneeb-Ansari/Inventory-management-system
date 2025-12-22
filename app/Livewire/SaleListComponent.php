<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Sale, Location};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SaleListComponent extends Component
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

    public function deleteSale($saleId)
    {
        // $this->authorize('delete', \App\Policies\InventoryPolicy::class);

        try {
            $sale = Sale::findOrFail($saleId);
            $sale->delete();

            session()->flash('success', 'Sale deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sales = Sale::with(['location', 'user', 'items'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('sale_no', 'like', "%{$this->search}%")
                      ->orWhere('customer_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->locationFilter, function($query) {
                $query->where('location_id', $this->locationFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('sale_date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        // Calculate totals
        $totalAmount = Sale::when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('sale_no', 'like', "%{$this->search}%")
                      ->orWhere('customer_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->locationFilter, function($query) {
                $query->where('location_id', $this->locationFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('sale_date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->sum('total_amount');

        return view('livewire.sale-list-component', [
            'sales' => $sales,
            'totalAmount' => $totalAmount,
        ])->layout('layouts.app');
    }
}
