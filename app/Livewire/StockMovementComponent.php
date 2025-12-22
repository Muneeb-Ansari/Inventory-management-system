<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{StockMovement, Location, Product};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockMovementExport;

class StockMovementComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $locationId;
    public $productId;
    public $dateFrom;
    public $dateTo;
    public $movementType = '';
    public $perPage = 50;

    public $locations;
    public $products;

    protected $queryString = [
        'locationId' => ['except' => ''],
        'productId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'movementType' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('setOpeningStock', \App\Policies\InventoryPolicy::class);
        
        $this->locations = Location::active()->get();
        $this->products = Product::active()->orderBy('name')->get();
        
        $this->locationId = $this->locations->first()?->id;
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingLocationId()
    {
        $this->resetPage();
    }

    public function updatingProductId()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->locationId = $this->locations->first()?->id;
        $this->productId = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->movementType = '';
        $this->resetPage();
    }

    public function downloadExcel()
    {
        $this->authorize('setOpeningStock', Inventory::class);

        $movements = $this->getMovementsQuery()->get();

        return Excel::download(
            new StockMovementExport(
                $movements, 
                $this->locationId, 
                $this->dateFrom, 
                $this->dateTo
            ),
            'stock_movements_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    protected function getMovementsQuery()
    {
        return StockMovement::with(['location', 'product', 'movementable'])
            ->when($this->locationId, fn($q) => $q->where('location_id', $this->locationId))
            ->when($this->productId, fn($q) => $q->where('product_id', $this->productId))
            ->when($this->dateFrom && $this->dateTo, function($q) {
                $q->whereBetween('movement_date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->movementType, fn($q) => $q->where('movement_type', $this->movementType))
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc');
    }

    public function render()
    {
        $movements = $this->getMovementsQuery()->paginate($this->perPage);

        // Summary calculations
        $summary = [
            'total_in' => StockMovement::when($this->locationId, fn($q) => $q->where('location_id', $this->locationId))
                ->when($this->productId, fn($q) => $q->where('product_id', $this->productId))
                ->when($this->dateFrom && $this->dateTo, fn($q) => $q->whereBetween('movement_date', [$this->dateFrom, $this->dateTo]))
                ->when($this->movementType, fn($q) => $q->where('movement_type', $this->movementType))
                ->sum('quantity_in'),
            
            'total_out' => StockMovement::when($this->locationId, fn($q) => $q->where('location_id', $this->locationId))
                ->when($this->productId, fn($q) => $q->where('product_id', $this->productId))
                ->when($this->dateFrom && $this->dateTo, fn($q) => $q->whereBetween('movement_date', [$this->dateFrom, $this->dateTo]))
                ->when($this->movementType, fn($q) => $q->where('movement_type', $this->movementType))
                ->sum('quantity_out'),
        ];

        $summary['net_movement'] = $summary['total_in'] - $summary['total_out'];

        return view('livewire.stock-movement-component', [
            'movements' => $movements,
            'summary' => $summary,
        ])->layout('layouts.app');
    }
}