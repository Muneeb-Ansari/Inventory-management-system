<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Control Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .discontinued {
            background-color: #ffe6e6;
        }
        .negative {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Control Register</h1>
        <p>Location: {{ $location }}</p>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Sr #</th>
                <th rowspan="2">Product Name</th>
                <th colspan="3">Opening Stock</th>
                <th colspan="3">Purchase</th>
                <th colspan="3">Consumption</th>
                <th colspan="3">Closing Balance</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOpeningAmount = 0;
                $totalPurchaseAmount = 0;
                $totalSaleAmount = 0;
                $totalClosingAmount = 0;
            @endphp

            @foreach($data as $index => $item)
                @php
                    $totalOpeningAmount += $item['rate'];
                    $totalPurchaseAmount += $item['amount'];
                    $totalSaleAmount += $item['sale_amount'];
                    $totalClosingAmount += $item['closing_amount'];
                @endphp
                <tr class="{{ $item['is_discontinued'] ? 'discontinued' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        {{ $item['product_name'] }}
                        @if($item['is_discontinued'])
                            <small>(D)</small>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item['opening_qty'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['opening_rate'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['opening_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['purchase_qty'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['purchase_rate'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['purchase_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['sale_qty'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['sale_rate'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['sale_amount'], 2) }}</td>
                    <td class="text-right {{ $item['closing_qty'] < 0 ? 'negative' : '' }}">
                        {{ number_format($item['closing_qty'], 2) }}
                    </td>
                    <td class="text-right">{{ number_format($item['closing_rate'], 2) }}</td>
                    <td class="text-right {{ $item['closing_amount'] < 0 ? 'negative' : '' }}">
                        {{ number_format($item['closing_amount'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="4" class="text-right">Total:</td>
                <td class="text-right">{{ number_format($totalOpeningAmount, 2) }}</td>
                <td colspan="2"></td>
                <td class="text-right">{{ number_format($totalPurchaseAmount, 2) }}</td>
                <td colspan="2"></td>
                <td class="text-right">{{ number_format($totalSaleAmount, 2) }}</td>
                <td colspan="2"></td>
                <td class="text-right">{{ number_format($totalClosingAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
