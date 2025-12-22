<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Opening Stock Management</h1>
                <p class="text-gray-600 text-sm mt-1">Set opening stock for products at the beginning of a period</p>
            </div>
            <button wire:click="openModal" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Opening Stock
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                <select wire:model.live="locationId" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                <input type="date" 
                       wire:model.live="date" 
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Product</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="searchProduct" 
                       placeholder="Product name or code..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div class="flex items-end">
                <button wire:click="importFromPreviousMonth" 
                        class="w-full bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700 transition">
                    📥 Import from Previous
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-sm text-blue-800">
                <strong>📌 Note:</strong> Opening stock represents the inventory available at the beginning of the selected date for the chosen location. This will be used as the starting point for all inventory calculations.
            </p>
        </div>

        <!-- Summary -->
        <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Total Opening Stock Value</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($totalAmount, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $openingStocks->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Opening Stocks Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Product Name</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Product Code</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Unit</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Quantity</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Rate</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Amount</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($openingStocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-4 py-3 text-sm font-medium">{{ $stock->product->name }}</td>
                            <td class="border px-4 py-3 text-sm">
                                <span class="bg-gray-100 px-2 py-1 rounded">{{ $stock->product->code }}</span>
                            </td>
                            <td class="border px-4 py-3 text-sm">{{ strtoupper($stock->product->unit) }}</td>
                            <td class="border px-4 py-3 text-sm text-right font-semibold">{{ number_format($stock->quantity, 2) }}</td>
                            <td class="border px-4 py-3 text-sm text-right">{{ number_format($stock->rate, 2) }}</td>
                            <td class="border px-4 py-3 text-sm text-right font-bold text-green-600">{{ number_format($stock->amount, 2) }}</td>
                            <td class="border px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="edit({{ $stock->id }})" 
                                            class="text-blue-600 hover:text-blue-800" 
                                            title="Edit">
                                        ✏️
                                    </button>
                                    <button wire:click="delete({{ $stock->id }})" 
                                            wire:confirm="Are you sure you want to delete this opening stock?"
                                            class="text-red-600 hover:text-red-800" 
                                            title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📊</div>
                                <p class="mb-2">No opening stock set for this location and date</p>
                                <button wire:click="openModal" class="text-blue-600 hover:underline">
                                    Add your first opening stock entry
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($openingStocks->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="border px-4 py-3 text-right font-bold text-lg">Total:</td>
                        <td class="border px-4 py-3 text-right font-bold text-lg text-green-600">
                            {{ number_format($totalAmount, 2) }}
                        </td>
                        <td class="border px-4 py-3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $openingStocks->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">{{ $editingId ? 'Edit' : 'Add' }} Opening Stock</h2>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <!-- Location (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" 
                                   value="{{ $locations->firstWhere('id', $locationId)?->name }}" 
                                   disabled
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        </div>

                        <!-- Date (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="text" 
                                   value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" 
                                   disabled
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">
                        </div>

                        <!-- Product -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                            <select wire:model="selectedProductId" 
                                    class="w-full border border-gray-300 rounded px-3 py-2"
                                    {{ $editingId ? 'disabled' : '' }}>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                @endforeach
                            </select>
                            @error('selectedProductId') 
                                <span class="text-red-600 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" 
                                   step="0.01"
                                   wire:model="quantity" 
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('quantity') 
                                <span class="text-red-600 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rate *</label>
                            <input type="number" 
                                   step="0.01"
                                   wire:model="rate" 
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                            @error('rate') 
                                <span class="text-red-600 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Calculated Amount -->
                        @if($quantity && $rate)
                        <div class="p-3 bg-green-50 rounded">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Amount:</span>
                                <span class="text-lg font-bold text-green-600">
                                    {{ number_format($quantity * $rate, 2) }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" 
                                wire:click="closeModal"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <span wire:loading.remove>{{ $editingId ? 'Update' : 'Save' }}</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>