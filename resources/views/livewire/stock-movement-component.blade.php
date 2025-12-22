<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Stock Movement Register</h1>
                <p class="text-gray-600 text-sm mt-1">Complete transaction history with running balance</p>
            </div>
            <button wire:click="downloadExcel" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                📥 Download Excel
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                <select wire:model.live="locationId" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select wire:model.live="productId" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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

            <!-- Movement Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="movementType" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Types</option>
                    <option value="opening">Opening</option>
                    <option value="purchase">Purchase</option>
                    <option value="sale">Sale</option>
                    <option value="adjustment">Adjustment</option>
                </select>
            </div>
        </div>

        <!-- Reset Button -->
        <div class="mb-4">
            <button wire:click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800">
                🔄 Reset Filters
            </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Total Inward</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($summary['total_in'], 2) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-600">Total Outward</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($summary['total_out'], 2) }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Net Movement</p>
                <p class="text-2xl font-bold {{ $summary['net_movement'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ number_format($summary['net_movement'], 2) }}
                </p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded text-sm text-blue-800">
            <strong>ℹ️ Note:</strong> Stock movements show all inventory transactions including opening stock, purchases, sales, and adjustments. The balance column shows running stock after each transaction.
        </div>

        <!-- Movements Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Date</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Product</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Location</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Type</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Reference</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Inward</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Outward</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Rate</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-3 text-sm">
                                {{ $movement->movement_date->format('d M Y') }}
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                <div class="font-medium">{{ $movement->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $movement->product->code }}</div>
                            </td>
                            <td class="border px-4 py-3 text-sm">{{ $movement->location->name }}</td>
                            <td class="border px-4 py-3 text-center">
                                @if($movement->movement_type === 'opening')
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-semibold">Opening</span>
                                @elseif($movement->movement_type === 'purchase')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Purchase</span>
                                @elseif($movement->movement_type === 'sale')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Sale</span>
                                @else
                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-semibold">Adjustment</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                @if($movement->movementable)
                                    @if($movement->movement_type === 'purchase')
                                        <a href="{{ route('purchases.show', $movement->movementable_id) }}" 
                                           class="text-blue-600 hover:underline">
                                            {{ $movement->movementable->purchase_no ?? 'N/A' }}
                                        </a>
                                    @elseif($movement->movement_type === 'sale')
                                        <a href="{{ route('sales.show', $movement->movementable_id) }}" 
                                           class="text-blue-600 hover:underline">
                                            {{ $movement->movementable->sale_no ?? 'N/A' }}
                                        </a>
                                    @else
                                        <span class="text-gray-600">{{ ucfirst($movement->movement_type) }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm text-right">
                                @if($movement->quantity_in > 0)
                                    <span class="font-semibold text-green-600">{{ number_format($movement->quantity_in, 2) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm text-right">
                                @if($movement->quantity_out > 0)
                                    <span class="font-semibold text-red-600">{{ number_format($movement->quantity_out, 2) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm text-right">{{ number_format($movement->rate, 2) }}</td>
                            <td class="border px-4 py-3 text-sm text-right">
                                <span class="font-bold {{ $movement->balance < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ number_format($movement->balance, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📊</div>
                                <p>No stock movements found for selected filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($movements->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="border px-4 py-3 text-right font-bold">Totals:</td>
                        <td class="border px-4 py-3 text-right font-bold text-green-600">
                            {{ number_format($summary['total_in'], 2) }}
                        </td>
                        <td class="border px-4 py-3 text-right font-bold text-red-600">
                            {{ number_format($summary['total_out'], 2) }}
                        </td>
                        <td class="border px-4 py-3"></td>
                        <td class="border px-4 py-3 text-right font-bold text-blue-600">
                            Net: {{ number_format($summary['net_movement'], 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $movements->links() }}
        </div>

        <!-- Legend -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-semibold mb-2">Legend:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded font-semibold">Opening</span>
                    <span>Initial stock</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">Purchase</span>
                    <span>Stock inward</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-semibold">Sale</span>
                    <span>Stock outward</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded font-semibold">Adjustment</span>
                    <span>Manual changes</span>
                </div>
            </div>
        </div>
    </div>
</div>