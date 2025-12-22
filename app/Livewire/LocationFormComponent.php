<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location, Inventory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LocationFormComponent extends Component
{
    use AuthorizesRequests;

    public $name;
    public $code;
    public $address;
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:locations,code',
        'address' => 'nullable|string',
        'isActive' => 'boolean',
    ];

    public function mount()
    {
        $this->authorize('create', Inventory::class);
    }

    public function save()
    {
        $this->validate();

        try {
            Location::create([
                'name' => $this->name,
                'code' => $this->code,
                'address' => $this->address,
                'is_active' => $this->isActive,
            ]);

            session()->flash('success', 'Location created successfully!');
            return redirect()->route('locations.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating location: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.location-form-component')->layout('layouts.app');
    }
}
