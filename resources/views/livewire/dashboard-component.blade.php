
{{-- <x-app-layout> --}}
    @php
        use App\Models\Inventory;
    @endphp

    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-8">Inventory Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Products -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Products</p>
                        <p class="text-3xl font-bold mt-2">{{ $totalProducts }}</p>
                    </div>
                    <div class="bg-white bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Purchases -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Today's Purchases</p>
                        <p class="text-3xl font-bold mt-2">{{ $todayPurchases }}</p>
                        <p class="text-xs mt-1">{{ number_format($todayPurchaseAmount, 2) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Sales -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Today's Sales</p>
                        <p class="text-3xl font-bold mt-2">{{ $todaySales }}</p>
                        <p class="text-xs mt-1">{{ number_format($todaySaleAmount, 2) }}</p>
                    </div>
                    <div class="bg-white bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Low Stock Items</p>
                        <p class="text-3xl font-bold mt-2">{{ $lowStockItems }}</p>
                    </div>
                    <div class="bg-white bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Purchases -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Purchases</h2>
                    <a href="{{ route('purchases.index') }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Purchase No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPurchases as $purchase)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $purchase->purchase_no }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $purchase->purchase_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-right">{{ number_format($purchase->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No purchases found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Recent Sales</h2>
                    <a href="{{ route('sales.index') }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sale No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $sale->sale_no }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-right">{{ number_format($sale->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No sales found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        @if ($lowStockProducts && count($lowStockProducts) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-red-600">⚠️ Low Stock Alert</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Product</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Code</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Current Stock</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Minimum Stock</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($lowStockProducts as $product)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $product->name }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $product->code }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-red-600 font-semibold">
                                        {{ number_format($product->current_stock, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right">{{ number_format($product->minimum_stock, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ route('purchases.create') }}"
                                            class="text-blue-600 hover:underline text-xs">
                                            Create Purchase
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            @can("create", Inventory::class)
                <a href="{{ route('purchases.create') }}"
                    class="bg-green-600 text-white text-center py-4 rounded-lg hover:bg-green-700 transition">
                    <div class="text-2xl mb-2">📦</div>
                    <div class="font-semibold">Create Purchase</div>
                </a>
            @endcan
            @can("viewAny", Inventory::class)
                <a href="{{ route('sales.create') }}"
                    class="bg-blue-600 text-white text-center py-4 rounded-lg hover:bg-blue-700 transition">
                    <div class="text-2xl mb-2">💰</div>
                    <div class="font-semibold">Create Sale</div>
                </a>
            @endcan
            @can("viewReport", Inventory::class)
                <a href="{{ route('reports.inventory') }}"
                    class="bg-purple-600 text-white text-center py-4 rounded-lg hover:bg-purple-700 transition">
                    <div class="text-2xl mb-2">📊</div>
                    <div class="font-semibold">View Reports</div>
                </a>
            @endcan
            @can("viewAny", Inventory::class)
                <a href="{{ route('products.index') }}"
                    class="bg-orange-600 text-white text-center py-4 rounded-lg hover:bg-orange-700 transition">
                    <div class="text-2xl mb-2">📋</div>
                    <div class="font-semibold">Manage Products</div>
                </a>
            @endcan
            @can("setOpeningStock", Inventory::class)
                <a href="{{ route('stock-movements') }}" class="bg-yellow-600 text-white text-center py-4 rounded-lg hover:bg-yellow-700 transition">
                    <div class="text-2xl mb-2">📋</div>
                    <div class="font-semibold">Stock Movements</div>
                </a>
            @endcan
        </div>
    </div>
{{-- </x-app-layout> --}}
