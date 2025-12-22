<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Location, Product, OpeningStock};
use App\Repositories\InventoryRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OpeningStockComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $locationId;
    public $date;
    public $searchProduct = '';
    public $perPage = 20;

    // For adding/editing
    public $showModal = false;
    public $editingId = null;
    public $selectedProductId;
    public $quantity;
    public $rate;

    public $locations;
    public $products;

    protected $rules = [
        'locationId' => 'required|exists:locations,id',
        'date' => 'required|date',
        'selectedProductId' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0',
        'rate' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->authorize('setOpeningStock', InventoryPolicy::class);
        
        $this->locations = Location::active()->get();
        $this->products = Product::active()->notDiscontinued()->orderBy('name')->get();
        
        $this->locationId = $this->locations->first()?->id;
        $this->date = now()->startOfMonth()->format('Y-m-d');
    }

    public function updatedLocationId()
    {
        $this->resetPage();
    }

    public function updatedDate()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    public function resetModal()
    {
        $this->editingId = null;
        $this->selectedProductId = null;
        $this->quantity = '';
        $this->rate = '';
        $this->resetValidation();
    }

    public function edit($openingStockId)
    {
        $openingStock = OpeningStock::findOrFail($openingStockId);
        
        $this->editingId = $openingStock->id;
        $this->selectedProductId = $openingStock->product_id;
        $this->quantity = $openingStock->quantity;
        $this->rate = $openingStock->rate;
        
        $this->showModal = true;
    }

    public function save(InventoryRepository $repository)
    {
        $this->validate();

        try {
            // Check if opening stock already exists for this product/location/date
            $existing = OpeningStock::where('location_id', $this->locationId)
                ->where('product_id', $this->selectedProductId)
                ->where('date', $this->date)
                ->when($this->editingId, function($q) {
                    $q->where('id', '!=', $this->editingId);
                })
                ->first();

            if ($existing) {
                session()->flash('error', 'Opening stock already exists for this product on this date!');
                return;
            }

            $repository->setOpeningStock(
                $this->locationId,
                $this->selectedProductId,
                $this->date,
                $this->quantity,
                $this->rate
            );

            session()->flash('success', 'Opening stock saved successfully!');
            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving opening stock: ' . $e->getMessage());
        }
    }

    public function delete($openingStockId)
    {
        $this->authorize('delete', \App\Policies\InventoryPolicy::class);

        try {
            $openingStock = OpeningStock::findOrFail($openingStockId);
            
            // Delete related stock movement
            $openingStock->stockMovements()->delete();
            $openingStock->delete();

            session()->flash('success', 'Opening stock deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting opening stock: ' . $e->getMessage());
        }
    }

    public function importFromPreviousMonth()
    {
        $this->authorize('setOpeningStock', \App\Policies\InventoryPolicy::class);

        try {
            // Get previous month's closing as this month's opening
            $previousMonth = \Carbon\Carbon::parse($this->date)->subMonth()->endOfMonth();
            
            // This would require getting closing balance from stock movements
            // For now, show message
            session()->flash('info', 'Import from previous month functionality coming soon!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $openingStocks = OpeningStock::with(['product', 'location'])
            ->where('location_id', $this->locationId)
            ->where('date', $this->date)
            ->when($this->searchProduct, function($query) {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', "%{$this->searchProduct}%")
                      ->orWhere('code', 'like', "%{$this->searchProduct}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $totalAmount = OpeningStock::where('location_id', $this->locationId)
            ->where('date', $this->date)
            ->sum('amount');

        return view('livewire.opening-stock-component', [
            'openingStocks' => $openingStocks,
            'totalAmount' => $totalAmount,
        ])->layout('layouts.app');
    }
}