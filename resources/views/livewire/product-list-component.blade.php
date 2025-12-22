<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Products</h1>
            @can('create', App\Policies\InventoryPolicy::class)
                <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    + New Product
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Name or Code..."
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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discontinued</label>
                <select wire:model.live="discontinuedFilter" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Products</option>
                    <option value="0">Active Products</option>
                    <option value="1">Discontinued</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="resetFilters" class="text-sm text-blue-600 hover:text-blue-800">
                    🔄 Reset Filters
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Product Name</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Code</th>
                        <th class="border px-4 py-3 text-left text-sm font-semibold">Unit</th>
                        <th class="border px-4 py-3 text-right text-sm font-semibold">Min Stock</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Status</th>
                        <th class="border px-4 py-3 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 {{ $product->is_discontinued ? 'bg-red-50' : '' }}">
                            <td class="border px-4 py-3 text-sm">
                                <div class="font-medium">{{ $product->name }}</div>
                                @if($product->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm">
                                <span class="bg-gray-100 px-2 py-1 rounded">{{ $product->code }}</span>
                            </td>
                            <td class="border px-4 py-3 text-sm">{{ $product->unit }}</td>
                            <td class="border px-4 py-3 text-sm text-right">{{ number_format($product->minimum_stock, 2) }}</td>
                            <td class="border px-4 py-3 text-center">
                                <div class="space-y-1">
                                    @if($product->is_active)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Active</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">Inactive</span>
                                    @endif

                                    @if($product->is_discontinued)
                                        <br>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Discontinued</span>
                                    @endif
                                </div>
                            </td>
                            <td class="border px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="text-blue-600 hover:text-blue-800"
                                       title="View">
                                        👁️
                                    </a>

                                    @can('update', App\Policies\InventoryPolicy::class)
                                        @if(!$product->is_discontinued)
                                            <button wire:click="toggleStatus({{ $product->id }})"
                                                    class="text-yellow-600 hover:text-yellow-800"
                                                    title="Toggle Status">
                                                🔄
                                            </button>
                                            <button wire:click="discontinueProduct({{ $product->id }})"
                                                    wire:confirm="Are you sure you want to discontinue this product?"
                                                    class="text-orange-600 hover:text-orange-800"
                                                    title="Discontinue">
                                                ⛔
                                            </button>
                                        @endif
                                    @endcan

                                    @can('delete', App\Policies\InventoryPolicy::class)
                                        <button wire:click="deleteProduct({{ $product->id }})"
                                                wire:confirm="Are you sure you want to delete this product?"
                                                class="text-red-600 hover:text-red-800"
                                                title="Delete">
                                            🗑️
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-12 text-center text-gray-500">
                                <div class="text-4xl mb-2">📦</div>
                                <p>No products found</p>
                                @can('create', App\Policies\InventoryPolicy::class)
                                    <a href="{{ route('products.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                        Create your first product
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
            {{ $products->links() }}
        </div>

        <!-- Legend -->
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-semibold mb-2">Legend:</p>
            <div class="flex flex-wrap gap-4 text-xs">
                <div class="flex items-center gap-1">
                    <span>👁️</span> <span>View Details</span>
                </div>
                <div class="flex items-center gap-1">
                    <span>🔄</span> <span>Toggle Active/Inactive</span>
                </div>
                <div class="flex items-center gap-1">
                    <span>⛔</span> <span>Discontinue (keeps history)</span>
                </div>
                <div class="flex items-center gap-1">
                    <span>🗑️</span> <span>Delete (only if no transactions)</span>
                </div>
            </div>
        </div>
    </div>
</div>
