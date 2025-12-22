<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6">Inventory Control Register</h1>

        <!-- Filter Form -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <select wire:model="locationId" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" wire:model="startDate" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" wire:model="endDate" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <div class="flex items-end">
                <button wire:click="generateReport"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Generate Report
                </button>
            </div>
        </div>

        @if($showReport)
            <!-- Search and Export -->
            <div class="flex justify-between items-center mb-4">
                <div class="w-1/3">
                    <input type="text"
                           wire:model.live.debounce.300ms="searchProduct"
                           placeholder="Search products..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div class="flex gap-2">
                    <button wire:click="downloadPDF"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Download PDF
                    </button>
                    <button wire:click="downloadExcel"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Download Excel
                    </button>
                </div>
            </div>

            <!-- Report Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Sr #</th>
                            <th class="border px-4 py-2">Product Name</th>
                            <th class="border px-4 py-2" colspan="3">Opening Stock</th>
                            <th class="border px-4 py-2" colspan="3">Purchase</th>
                            <th class="border px-4 py-2" colspan="3">Consumption</th>
                            <th class="border px-4 py-2" colspan="3">Closing Balance</th>
                        </tr>
                        <tr>
                            <th class="border px-4 py-2"></th>
                            <th class="border px-4 py-2"></th>
                            <th class="border px-4 py-2">Qty</th>
                            <th class="border px-4 py-2">Rate</th>
                            <th class="border px-4 py-2">Amount</th>
                            <th class="border px-4 py-2">Qty</th>
                            <th class="border px-4 py-2">Rate</th>
                            <th class="border px-4 py-2">Amount</th>
                            <th class="border px-4 py-2">Qty</th>
                            <th class="border px-4 py-2">Rate</th>
                            <th class="border px-4 py-2">Amount</th>
                            <th class="border px-4 py-2">Qty</th>
                            <th class="border px-4 py-2">Rate</th>
                            <th class="border px-4 py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedData as $index => $item)
                            <tr class="{{ $item['is_discontinued'] ? 'bg-red-50' : '' }}">
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2">
                                    {{ $item['product_name'] }}
                                    @if($item['is_discontinued'])
                                        <span class="text-xs text-red-600">(Discontinued)</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['opening_qty'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['opening_rate'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['opening_amount'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['purchase_qty'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['purchase_rate'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['purchase_amount'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['sale_qty'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['sale_rate'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['sale_amount'], 2) }}</td>
                                <td class="border px-4 py-2 text-right {{ $item['closing_qty'] < 0 ? 'text-red-600 font-bold' : '' }}">
                                    {{ number_format($item['closing_qty'], 2) }}
                                </td>
                                <td class="border px-4 py-2 text-right">{{ number_format($item['closing_rate'], 2) }}</td>
                                <td class="border px-4 py-2 text-right {{ $item['closing_amount'] < 0 ? 'text-red-600 font-bold' : '' }}">
                                    {{ number_format($item['closing_amount'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="border px-4 py-8 text-center text-gray-500">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $paginatedData->links() }}
            </div>
        @endif
    </div>
</div>
