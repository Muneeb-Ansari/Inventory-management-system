<div class="container mx-auto">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6"  wire:key="product-form-{{ $formKey }}">
        <h1 class="text-3xl font-bold mb-6">Create Product</h1>

        <form wire:submit.prevent="save">
            <div class="space-y-4">
                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Product Name *
                    </label>
                    <input type="text"
                           wire:model="name"
                           placeholder="e.g., FLYWHEEL ISUZU NPR"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Product Code *
                    </label>
                    <input type="text"
                           wire:model="code"
                           placeholder="e.g., FLYW-ISUZU-001"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    @error('code')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Unique identifier for this product</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea wire:model="description"
                              rows="3"
                              placeholder="Product details, specifications, etc."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                    @error('description')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Unit of Measurement *
                    </label>
                    <select wire:model="unit" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        @foreach($units as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unit')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Minimum Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Stock Level *
                    </label>
                    <input type="number"
                           step="0.01"
                           wire:model="minimumStock"
                           placeholder="0.00"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    @error('minimumStock')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Alert will be shown when stock falls below this level</p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                    <input type="checkbox"
                           wire:model="isActive"
                           id="isActive"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="isActive" class="ml-2 text-sm text-gray-700">
                        <span class="font-semibold">Active Product</span>
                        <span class="block text-xs text-gray-600">Product can be used in purchases and sales</span>
                    </label>
                </div>
            </div>

            <!-- Information Box -->
            <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="text-sm text-yellow-800">
                    <strong>Note:</strong> Once a product has transactions, it cannot be deleted. You can only mark it as discontinued to preserve historical data.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('products.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <span wire:loading.remove>Create Product</span>
                    <span wire:loading>Creating...</span>
                </button>
            </div>
        </form>
    </div>
</div>
