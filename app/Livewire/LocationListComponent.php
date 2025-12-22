<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LocationListComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 20;

    // For inline editing
    public $editingId = null;
    public $editName = '';
    public $editCode = '';
    public $editAddress = '';
    public $editIsActive = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('viewAny', InventoryPolicy::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function edit($locationId)
    {
        $this->authorize('update', \App\Policies\InventoryPolicy::class);

        $location = Location::findOrFail($locationId);
        $this->editingId = $location->id;
        $this->editName = $location->name;
        $this->editCode = $location->code;
        $this->editAddress = $location->address;
        $this->editIsActive = $location->is_active;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->reset(['editName', 'editCode', 'editAddress', 'editIsActive']);
    }

    public function update()
    {
        $this->authorize('update', \App\Policies\InventoryPolicy::class);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editCode' => 'required|string|max:255|unique:locations,code,' . $this->editingId,
            'editAddress' => 'nullable|string',
            'editIsActive' => 'boolean',
        ]);

        try {
            $location = Location::findOrFail($this->editingId);
            $location->update([
                'name' => $this->editName,
                'code' => $this->editCode,
                'address' => $this->editAddress,
                'is_active' => $this->editIsActive,
            ]);

            session()->flash('success', 'Location updated successfully!');
            $this->cancelEdit();
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating location: ' . $e->getMessage());
        }
    }

    public function toggleStatus($locationId)
    {
        $this->authorize('update', \App\Policies\InventoryPolicy::class);

        try {
            $location = Location::findOrFail($locationId);
            $location->update(['is_active' => !$location->is_active]);

            session()->flash('success', 'Location status updated!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function deleteLocation($locationId)
    {
        $this->authorize('delete', \App\Policies\InventoryPolicy::class);

        try {
            $location = Location::findOrFail($locationId);

            // Check if location has transactions
            if ($location->purchases()->count() > 0 || $location->sales()->count() > 0) {
                session()->flash('error', 'Cannot delete location with existing transactions!');
                return;
            }

            $location->delete();
            session()->flash('success', 'Location deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting location: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $locations = Location::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('code', 'like', "%{$this->search}%")
                      ->orWhere('address', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.location-list-component', [
            'locations' => $locations,
        ])->layout('layouts.app');
    }
}
