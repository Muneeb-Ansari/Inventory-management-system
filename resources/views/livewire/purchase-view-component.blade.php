<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6" id="purchase-detail">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">Purchase Details</h1>
                <p class="text-gray-600">Purchase No: <span class="font-semibold">{{ $purchase->purchase_no }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('purchases.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
                <button wire:click="downloadPDF" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    📄 Download PDF
                </button>
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    🖨️ Print
                </button>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @if($purchase->status === 'completed')
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">✓ Completed</span>
            @elseif($purchase->status === 'pending')
                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-semibold">⏳ Pending</span>
            @else
                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold">✗ Cancelled</span>
            @endif
        </div>

        <!-- Purchase Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <h3 class="font-semibold text-gray-700 mb-3">Purchase Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Purchase Date:</span>
                        <span class="font-semibold">{{ $purchase->purchase_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Location:</span>
                        <span class="font-semibold">{{ $purchase->location->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Supplier:</span>
                        <span class="font-semibold">{{ $purchase->supplier_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-3">Additional Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created By:</span>
                        <span class="font-semibold">{{ $purchase->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created At:</span>
                        <span class="font-semibold">{{ $purchase->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-semibold">{{ $purchase->items->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        @if($purchase->remarks)
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-sm font-semibold text-gray-700 mb-1">Remarks:</p>
            <p class="text-gray-700">{{ $purchase->remarks }}</p>
        </div>
        @endif

        <!-- Purchase Items -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-4">Purchase Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-3 text-left text-sm font-semibold">#</th>
                            <th class="border px-4 py-3 text-left text-sm font-semibold">Product</th>
                            <th class="border px-4 py-3 text-left text-sm font-semibold">Product Code</th>
                            <th class="border px-4 py-3 text-right text-sm font-semibold">Quantity</th>
                            <th class="border px-4 py-3 text-right text-sm font-semibold">Rate</th>
                            <th class="border px-4 py-3 text-right text-sm font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-3 text-sm text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-3 text-sm">{{ $item->product->name }}</td>
                                <td class="border px-4 py-3 text-sm">{{ $item->product->code }}</td>
                                <td class="border px-4 py-3 text-sm text-right">{{ number_format($item->quantity, 2) }}</td>
                                <td class="border px-4 py-3 text-sm text-right">{{ number_format($item->rate, 2) }}</td>
                                <td class="border px-4 py-3 text-sm text-right font-semibold">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="border px-4 py-3 text-right font-bold text-lg">Total Amount:</td>
                            <td class="border px-4 py-3 text-right font-bold text-lg text-green-600">
                                {{ number_format($purchase->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Stock Movement Info -->
        <div class="p-4 bg-green-50 rounded-lg">
            <h3 class="font-semibold text-green-800 mb-2">✓ Stock Movement</h3>
            <p class="text-sm text-green-700">
                This purchase has been recorded in stock movements. All items have been added to inventory for {{ $purchase->location->name }}.
            </p>
        </div>
    </div>
    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #purchase-detail, #purchase-detail * {
                visibility: visible;
            }
            #purchase-detail {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button, .no-print {
                display: none !important;
            }
        }
    </style>
</div>

