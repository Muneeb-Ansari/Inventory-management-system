<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Location, Product};
use App\Repositories\InventoryRepository;
use App\Exceptions\InsufficientStockException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Policies\InventoryPolicy;

class SaleFormComponent extends Component
{
    use AuthorizesRequests;

    public $locationId;
    public $saleDate;
    public $customerName;
    public $remarks;
    public $items = [];

    public $locations;
    public $products;
    public $stockLevels = [];

    protected $rules = [
        'locationId' => 'required|exists:locations,id',
        'saleDate' => 'required|date',
        'customerName' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.rate' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->authorize('createSale', InventoryPolicy::class);

        $this->locations = Location::active()->get();
        $this->products = Product::active()->notDiscontinued()->get();
        $this->locationId = $this->locations->first()?->id;
        $this->saleDate = now()->format('Y-m-d');
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => '',
            'rate' => '',
            'amount' => 0,
            'available_stock' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        // Auto-calculate amount when quantity or rate changes
        if (str_contains($key, 'quantity') || str_contains($key, 'rate')) {
            $index = (int) explode('.', $key)[0];


            if (isset($this->items[$index]['quantity']) && isset($this->items[$index]['rate'])) {
                $quantity = (float) $this->items[$index]['quantity'];
                $rate     = (float) $this->items[$index]['rate'];

                $this->items[$index]['amount'] = $quantity * $rate;
            }
        }

        // Update available stock when product changes
        if (str_contains($key, 'product_id')) {
            $index = (int) explode('.', $key)[0];
            $productId = $this->items[$index]['product_id'];

            if ($productId && $this->locationId) {
                $product = Product::find($productId);
                $this->items[$index]['available_stock'] = $product->getCurrentStock($this->locationId);
            }
        }
    }

    public function saveSale(InventoryRepository $repository)
    {
        $this->validate();

        try {
            $saleNo = 'SAL-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $sale = $repository->createSale([
                'sale_no' => $saleNo,
                'location_id' => $this->locationId,
                'sale_date' => $this->saleDate,
                'customer_name' => $this->customerName,
                'remarks' => $this->remarks,
                'items' => $this->items,
            ]);

            session()->flash('success', 'Sale created successfully!');
            return redirect()->route('sales.index');

        } catch (InsufficientStockException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating sale: ' . $e->getMessage());
        }
    }

    public function getTotalAmountProperty()
    {
        return collect($this->items)->sum('amount');
    }

    public function render()
    {
        return view('livewire.sale-form-component')->layout('layouts.app');
    }
}
