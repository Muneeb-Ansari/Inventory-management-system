<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6">Create Purchase</h1>

        <form wire:submit.prevent="savePurchase">
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <select wire:model="locationId" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('locationId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date *</label>
                    <input type="date" wire:model="purchaseDate" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('purchaseDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Name</label>
                    <input type="text" wire:model="supplierName" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('supplierName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <input type="text" wire:model="remarks" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Purchase Items</h2>
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
                                <th class="border px-4 py-2">Quantity *</th>
                                <th class="border px-4 py-2">Rate *</th>
                                <th class="border px-4 py-2">Amount</th>
                                <th class="border px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2">
                                        <select wire:model="items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("items.{$index}.product_id") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity"
                                               class="w-full border border-gray-300 rounded px-2 py-1">
                                        @error("items.{$index}.quantity") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.rate"
                                               class="w-full border border-gray-300 rounded px-2 py-1">
                                        @error("items.{$index}.rate") <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="border px-4 py-2 text-right">
                                        {{ number_format($item['amount'], 2) }}
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        @if(count($items) > 1)
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                ✕
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="border px-4 py-2 text-right font-bold">Total Amount:</td>
                                <td class="border px-4 py-2 text-right font-bold">
                                    {{ number_format($this->totalAmount, 2) }}
                                </td>
                                <td class="border px-4 py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('purchases.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Purchase
                </button>
            </div>
        </form>
    </div>
</div>
