<div class="container mx-auto">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6">Create Location</h1>

        <form wire:submit.prevent="save">
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Location Name *
                    </label>
                    <input type="text"
                           wire:model="name"
                           placeholder="e.g., Muzaffar Garh Factory"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Location Code *
                    </label>
                    <input type="text"
                           wire:model="code"
                           placeholder="e.g., MGH-FAC-01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    @error('code')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Unique identifier for this location</p>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                    </label>
                    <textarea wire:model="address"
                              rows="3"
                              placeholder="Enter complete address..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                    @error('address')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox"
                           wire:model="isActive"
                           id="isActive"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="isActive" class="ml-2 text-sm text-gray-700">
                        Active (Location can be used in transactions)
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('locations.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <span wire:loading.remove>Create Location</span>
                    <span wire:loading>Creating...</span>
                </button>
            </div>
        </form>
    </div>
</div>
