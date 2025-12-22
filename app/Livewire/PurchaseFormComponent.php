<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Location, Product};
use App\Repositories\InventoryRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Policies\InventoryPolicy;


class PurchaseFormComponent extends Component
{
    use AuthorizesRequests;

    public $locationId;
    public $purchaseDate;
    public $supplierName;
    public $remarks;
    public $items = [];

    public $locations;
    public $products;

    protected $rules = [
        'locationId' => 'required|exists:locations,id',
        'purchaseDate' => 'required|date',
        'supplierName' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.rate' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->authorize('createPurchase', InventoryPolicy::class);

        $this->locations = Location::active()->get();
        $this->products = Product::active()->notDiscontinued()->get();
        $this->locationId = $this->locations->first()?->id;
        $this->purchaseDate = now()->format('Y-m-d');
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => '',
            'rate' => '',
            'amount' => 0,
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
                $this->items[$index]['amount'] =
                    $this->items[$index]['quantity'] * $this->items[$index]['rate'];
            }
        }
    }

    public function savePurchase(InventoryRepository $repository)
    {
        $this->validate();

        try {
            $purchaseNo = 'PUR-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $purchase = $repository->createPurchase([
                'purchase_no' => $purchaseNo,
                'location_id' => $this->locationId,
                'purchase_date' => $this->purchaseDate,
                'supplier_name' => $this->supplierName,
                'remarks' => $this->remarks,
                'items' => $this->items,
            ]);
            
            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Purchase created successfully!'
            );
            
            return redirect()->route('purchases.index', $purchase->id);

        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Error while creating purchase '
            );
        }
    }

    public function getTotalAmountProperty()
    {
        return collect($this->items)->sum('amount');
    }

    public function render()
    {
        return view('livewire.purchase-form-component')->layout('layouts.app');
    }
}
