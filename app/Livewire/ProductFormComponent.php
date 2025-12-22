<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductFormComponent extends Component
{
    use AuthorizesRequests;

    public $name;
    public $code;
    public $description;
    public $unit = 'pcs';
    public $minimumStock = 0;
    public $isActive = true;
    public $formKey = 0;

    public $units = [
        'pcs' => 'Pieces',
        'kg' => 'Kilogram',
        'ltr' => 'Liter',
        'mtr' => 'Meter',
        'box' => 'Box',
        'doz' => 'Dozen',
        'set' => 'Set',
        'unit' => 'Unit',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:products,code',
        'description' => 'nullable|string',
        'unit' => 'required|string',
        'minimumStock' => 'required|numeric|min:0',
        'isActive' => 'boolean',
    ];

    public function mount()
    {
        $this->authorize('create', Inventory::class);
    }

    private function resetForm()
    {
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->unit = 'pcs';
        $this->minimumStock = 0;
        $this->isActive = true;
    }

    public function save()
    {
        $this->validate();
        try {
            Product::create([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'unit' => $this->unit,
                'minimum_stock' => $this->minimumStock,
                'is_active' => $this->isActive,
            ]);

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Product created successfully!'
            );

            // Reset all form fields
            $this->resetForm();
            $this->resetValidation();
            
            // Force re-render
            $this->formKey = rand();

        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Error: Unable to save product',
            );
        }
    }

    public function render()
    {
        return view('livewire.product-form-component')->layout('layouts.app');
    }
}
