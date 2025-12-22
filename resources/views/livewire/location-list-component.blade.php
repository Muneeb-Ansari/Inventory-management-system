<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Locations</h1>
            @can('create', App\Policies\InventoryPolicy::class)
                <a href="{{ route('locations.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    + New Location
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Name, Code, Address..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800">
                    🔄 Reset Filters
                </button>
            </div>
        </div>

        <!-- Locations Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Name</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Code</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Address</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Status</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50">
                            @if($editingId === $location->id)
                                <!-- Edit Mode -->
                                <td class="border px-4 py-3">
                                    <input type="text" wire:model="editName" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                    @error('editName') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="border px-4 py-3">
                                    <input type="text" wire:model="editCode" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                    @error('editCode') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="border px-4 py-3">
                                    <input type="text" wire:model="editAddress" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <select wire:model="editIsActive" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <button wire:click="update" class="text-green-600 hover:text-green-800 mr-2">✓ Save</button>
                                    <button wire:click="cancelEdit" class="text-red-600 hover:text-red-800">✗ Cancel</button>
                                </td>
                            @else
                                <!-- View Mode -->
                                <td class="border px-4 py-3 text-sm font-medium">{{ $location->name }}</td>
                                <td class="border px-4 py-3 text-sm">
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $location->code }}</span>
                                </td>
                                <td class="border px-4 py-3 text-sm">{{ $location->address ?? 'N/A' }}</td>
                                <td class="border px-4 py-3 text-center">
                                    @if($location->is_active)
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Inactive</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        @can('update', App\Policies\InventoryPolicy::class)
                                            <button wire:click="edit({{ $location->id }})"
                                                    class="text-blue-600 hover:text-blue-800"
                                                    title="Edit">
                                                ✏️
                                            </button>
                                            <button wire:click="toggleStatus({{ $location->id }})"
                                                    class="text-yellow-600 hover:text-yellow-800"
                                                    title="Toggle Status">
                                                🔄
                                            </button>
                                        @endcan
                                        @can('delete', App\Policies\InventoryPolicy::class)
                                            <button wire:click="deleteLocation({{ $location->id }})"
                                                    wire:confirm="Are you sure you want to delete this location?"
                                                    class="text-red-600 hover:text-red-800"
                                                    title="Delete">
                                                🗑️
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📍</div>
                                <p>No locations found</p>
                                @can('create', App\Policies\InventoryPolicy::class)
                                    <a href="{{ route('locations.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                        Create your first location
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $locations->links() }}
        </div>
    </div>
</div>
