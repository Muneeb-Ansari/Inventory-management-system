@php
    use App\Model\Inventory;
@endphp
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Purchases</h1>
            @can('createPurchase', App\Policies\InventoryPolicy::class)
                <a href="{{ route('purchases.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    + New Purchase
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Purchase No, Supplier..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <!-- Location Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select wire:model.live="locationFilter" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date"
                       wire:model.live="dateFrom"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date"
                       wire:model.live="dateTo"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Reset Button -->
        <div class="mb-4">
            <button wire:click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800">
                🔄 Reset Filters
            </button>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Purchases</p>
                <p class="text-2xl font-bold text-blue-600">{{ $purchases->total() }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalAmount, 2) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Average Amount</p>
                <p class="text-2xl font-bold text-purple-600">
                    {{ $purchases->total() > 0 ? number_format($totalAmount / $purchases->total(), 2) : '0.00' }}
                </p>
            </div>
        </div>

        <!-- Purchases Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Purchase No</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Date</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Location</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Supplier</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Items</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Total Amount</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Status</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Created By</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-3 text-sm font-medium">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-600 hover:underline">
                                    {{ $purchase->purchase_no }}
                                </a>
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                {{ $purchase->location->name }}
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                {{ $purchase->supplier_name ?? 'N/A' }}
                            </td>
                            <td class="border px-4 py-3 text-center text-sm">
                                <span class="bg-gray-200 px-2 py-1 rounded">
                                    {{ $purchase->items->count() }}
                                </span>
                            </td>
                            <td class="border px-4 py-3 text-right text-sm font-semibold">
                                {{ number_format($purchase->total_amount, 2) }}
                            </td>
                            <td class="border px-4 py-3 text-center text-sm">
                                @if($purchase->status === 'completed')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                @elseif($purchase->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                {{ $purchase->user->name }}
                            </td>
                            <td class="border px-4 py-3 text-center text-sm">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('purchases.show', $purchase->id) }}"
                                       class="text-blue-600 hover:text-blue-800"
                                       title="View">
                                        👁️ View
                                    </a>
                                    @can('delete', Inventory::class)
                                        <button wire:click="deletePurchase({{ $purchase->id }})"
                                                wire:confirm="Are you sure you want to delete this purchase?"
                                                class="text-red-600 hover:text-red-800"
                                                title="Delete">
                                            🗑️ Delete
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📦</div>
                                <p>No purchases found</p>
                                @can('createPurchase', App\Policies\InventoryPolicy::class)
                                    <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                        Create your first purchase
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
            {{ $purchases->links() }}
        </div>
    </div>
</div>
