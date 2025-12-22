<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6">Create Sale / Issue</h1>

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="saveSale">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <select wire:model.live="locationId" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('locationId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sale Date *</label>
                    <input type="date" wire:model="saleDate" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('saleDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                    <input type="text" wire:model="customerName" placeholder="Enter customer name" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('customerName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <input type="text" wire:model="remarks" placeholder="Any remarks" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Sale Items</h2>
                    <button type="button" wire:click="addItem" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        + Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Product *</th>
                                <th class="border px-4 py-2">Available Stock</th>
                                <th class="border px-4 py-2">Quantity *</th>
                                <th class="border px-4 py-2">Rate *</th>
                                <th class="border px-4 py-2">Amount</th>
                                <th class="border px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr class="{{ isset($item['available_stock']) && $item['available_stock'] < ($item['quantity'] ?? 0) ? 'bg-red-50' : '' }}">
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>

                                    <!-- Product Selection -->
                                    <td class="border px-4 py-2">
                                        <select wire:model.live="items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                            @endforeach
                                        </select>
                                        @error("items.{$index}.product_id")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- Available Stock -->
                                    <td class="border px-4 py-2 text-center">
                                        @if(isset($item['available_stock']))
                                            <span class="font-semibold {{ $item['available_stock'] <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ number_format($item['available_stock'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Quantity -->
                                    <td class="border px-4 py-2">
                                        <input type="number"
                                               step="0.01"
                                               wire:model.live="items.{{ $index }}.quantity"
                                               placeholder="0.00"
                                               class="w-full border border-gray-300 rounded px-2 py-1 {{ isset($item['available_stock']) && $item['available_stock'] < ($item['quantity'] ?? 0) ? 'border-red-500' : '' }}">
                                        @error("items.{$index}.quantity")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                        @if(isset($item['available_stock']) && isset($item['quantity']) && $item['available_stock'] < $item['quantity'])
                                            <span class="text-red-600 text-xs">Insufficient stock!</span>
                                        @endif
                                    </td>

                                    <!-- Rate -->
                                    <td class="border px-4 py-2">
                                        <input type="number"
                                               step="0.01"
                                               wire:model.live="items.{{ $index }}.rate"
                                               placeholder="0.00"
                                               class="w-full border border-gray-300 rounded px-2 py-1">
                                        @error("items.{$index}.rate")
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- Amount -->
                                    <td class="border px-4 py-2 text-right font-semibold">
                                        {{ number_format($item['amount'] ?? 0, 2) }}
                                    </td>

                                    <!-- Action -->
                                    <td class="border px-4 py-2 text-center">
                                        @if(count($items) > 1)
                                            <button type="button"
                                                    wire:click="removeItem({{ $index }})"
                                                    class="text-red-600 hover:text-red-800 font-bold text-xl">
                                                ✕
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="border px-4 py-2 text-right font-bold text-lg">Total Amount:</td>
                                <td class="border px-4 py-2 text-right font-bold text-lg text-blue-600">
                                    {{ number_format($this->totalAmount, 2) }}
                                </td>
                                <td class="border px-4 py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Warning for low stock -->
                @php
                    $hasLowStock = collect($items)->contains(function($item) {
                        return isset($item['available_stock']) && isset($item['quantity']) && $item['available_stock'] < $item['quantity'];
                    });
                @endphp

                @if($hasLowStock)
                    <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <strong>⚠️ Warning:</strong> Some items have insufficient stock. Please adjust quantities or remove items.
                    </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('sales.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition {{ $hasLowStock ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $hasLowStock ? 'disabled' : '' }}>
                    <span wire:loading.remove>Save Sale</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Sales Summary (Optional) -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Quick Stats</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Today's Sales</p>
                <p class="text-2xl font-bold text-blue-600">{{ $todaySalesCount ?? 0 }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Amount Today</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($todaySalesAmount ?? 0, 2) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Items Sold Today</p>
                <p class="text-2xl font-bold text-purple-600">{{ $todayItemsCount ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
