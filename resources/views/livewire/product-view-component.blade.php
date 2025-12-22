<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
                <p class="text-gray-600">
                    Product Code: <span class="font-semibold bg-gray-100 px-2 py-1 rounded">{{ $product->code }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex gap-2 mb-6">
            @if($product->is_active)
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">✓ Active</span>
            @else
                <span class="bg-gray-100 text-gray-800 px-4 py-2 rounded-full text-sm font-semibold">Inactive</span>
            @endif

            @if($product->is_discontinued)
                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold">⛔ Discontinued</span>
                <span class="text-xs text-gray-500 self-center">
                    since {{ $product->discontinued_at->format('d M Y') }}
                </span>
            @endif
        </div>

        <!-- Product Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Product Details</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Unit:</span>
                        <span class="font-semibold">{{ strtoupper($product->unit) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Minimum Stock:</span>
                        <span class="font-semibold">{{ number_format($product->minimum_stock, 2) }}</span>
                    </div>
                    @if($product->description)
                        <div class="pt-2 border-t">
                            <p class="text-sm text-gray-600">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Transaction Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Purchased:</span>
                        <span class="font-semibold text-green-600">{{ number_format($this->totalPurchased, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Sold:</span>
                        <span class="font-semibold text-blue-600">{{ number_format($this->totalSold, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stock by Location -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-4">Current Stock</h3>
            <div class="flex items-center gap-4 mb-4">
                <label class="text-sm font-medium text-gray-700">Select Location:</label>
                <select wire:model.live="selectedLocation" class="border border-gray-300 rounded px-3 py-2">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Current Stock at Selected Location</p>
                        <p class="text-4xl font-bold {{ $this->currentStock < $product->minimum_stock ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($this->currentStock, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ strtoupper($product->unit) }}</p>
                    </div>
                    @if($this->currentStock < $product->minimum_stock)
                        <div class="text-right">
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">⚠️ Low Stock</span>
                            <p class="text-xs text-red-600 mt-1">Below minimum level</p>
                        </div>
                    @else
                        <div class="text-right">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">✓ Sufficient</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200 mb-4">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'purchases'"
                            :class="activeTab === 'purchases' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Recent Purchases
                    </button>
                    <button @click="activeTab = 'sales'"
                            :class="activeTab === 'sales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Recent Sales
                    </button>
                </nav>
            </div>

            <div x-data="{ activeTab: 'purchases' }">
                <!-- Recent Purchases -->
                <div x-show="activeTab === 'purchases'" class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left text-sm">Purchase No</th>
                                <th class="border px-4 py-2 text-left text-sm">Date</th>
                                <th class="border px-4 py-2 text-left text-sm">Location</th>
                                <th class="border px-4 py-2 text-right text-sm">Quantity</th>
                                <th class="border px-4 py-2 text-right text-sm">Rate</th>
                                <th class="border px-4 py-2 text-right text-sm">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->recentPurchases as $item)
                                <tr>
                                    <td class="border px-4 py-2 text-sm">
                                        <a href="{{ route('purchases.show', $item->purchase->id) }}" class="text-blue-600 hover:underline">
                                            {{ $item->purchase->purchase_no }}
                                        </a>
                                    </td>
                                    <td class="border px-4 py-2 text-sm">{{ $item->purchase->purchase_date->format('d M Y') }}</td>
                                    <td class="border px-4 py-2 text-sm">{{ $item->purchase->location->name }}</td>
                                    <td class="border px-4 py-2 text-sm text-right">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="border px-4 py-2 text-sm text-right">{{ number_format($item->rate, 2) }}</td>
                                    <td class="border px-4 py-2 text-sm text-right font-semibold">{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-4 py-8 text-center text-gray-500">No purchases found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Recent Sales -->
                <div x-show="activeTab === 'sales'" class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left text-sm">Sale No</th>
                                <th class="border px-4 py-2 text-left text-sm">Date</th>
                                <th class="border px-4 py-2 text-left text-sm">Location</th>
                                <th class="border px-4 py-2 text-right text-sm">Quantity</th>
                                <th class="border px-4 py-2 text-right text-sm">Rate</th>
                                <th class="border px-4 py-2 text-right text-sm">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->recentSales as $item)
                                <tr>
                                    <td class="border px-4 py-2 text-sm">
                                        <a href="{{ route('sales.show', $item->sale->id) }}" class="text-blue-600 hover:underline">
                                            {{ $item->sale->sale_no }}
                                        </a>
                                    </td>
                                    <td class="border px-4 py-2 text-sm">{{ $item->sale->sale_date->format('d M Y') }}</td>
                                    <td class="border px-4 py-2 text-sm">{{ $item->sale->location->name }}</td>
                                    <td class="border px-4 py-2 text-sm text-right">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="border px-4 py-2 text-sm text-right">{{ number_format($item->rate, 2) }}</td>
                                    <td class="border px-4 py-2 text-sm text-right font-semibold">{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-4 py-8 text-center text-gray-500">No sales found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

