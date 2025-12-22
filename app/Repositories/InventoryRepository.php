<?php

namespace App\Repositories;

use App\Models\{Product, Purchase, Sale, StockMovement, OpeningStock};
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdateStockMovementJob;
use App\Exceptions\InsufficientStockException;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getInventoryReport($locationId, $startDate, $endDate, $productId = null)
    {
        $query = Product::query()
            ->select('products.*')
            ->with(['openingStocks' => function($q) use ($locationId, $startDate) {
                $q->where('location_id', $locationId)
                  ->where('date', '<=', $startDate);
            }]);

        if ($productId) {
            $query->where('products.id', $productId);
        }

        return $query->get()->map(function($product) use ($locationId, $startDate, $endDate) {
            return $this->calculateProductInventory($product, $locationId, $startDate, $endDate);
        });
    }

    protected function calculateProductInventory($product, $locationId, $startDate, $endDate)
    {
        // Opening Stock
        $opening = $this->getOpeningBalance($product->id, $locationId, $startDate);

        // Purchases
        $purchases = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->where('purchases.location_id', $locationId)
            ->where('purchases.status', 'completed')
            ->whereBetween('purchases.purchase_date', [$startDate, $endDate])
            ->where('purchase_items.product_id', $product->id)
            ->whereNull('purchases.deleted_at')
            ->select(
                DB::raw('SUM(purchase_items.quantity) as total_qty'),
                DB::raw('AVG(purchase_items.rate) as avg_rate'),
                DB::raw('SUM(purchase_items.amount) as total_amount')
            )
            ->first();

        // Sales
        $sales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.location_id', $locationId)
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sale_items.product_id', $product->id)
            ->whereNull('sales.deleted_at')
            ->select(
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('AVG(sale_items.rate) as avg_rate'),
                DB::raw('SUM(sale_items.amount) as total_amount')
            )
            ->first();

        $purchaseQty = $purchases->total_qty ?? 0;
        $purchaseRate = $purchases->avg_rate ?? 0;
        $purchaseAmount = $purchases->total_amount ?? 0;

        $saleQty = $sales->total_qty ?? 0;
        $saleRate = $sales->avg_rate ?? 0;
        $saleAmount = $sales->total_amount ?? 0;

        $closingQty = $opening['quantity'] + $purchaseQty - $saleQty;
        $closingRate = $closingQty > 0 ?
            (($opening['amount'] + $purchaseAmount) / ($opening['quantity'] + $purchaseQty)) : 0;

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_code' => $product->code,
            'unit' => $product->unit,
            'is_discontinued' => $product->is_discontinued,
            'opening_qty' => round($opening['quantity'], 2),
            'opening_rate' => round($opening['rate'], 2),
            'opening_amount' => round($opening['amount'], 2),
            'purchase_qty' => round($purchaseQty, 2),
            'purchase_rate' => round($purchaseRate, 2),
            'purchase_amount' => round($purchaseAmount, 2),
            'sale_qty' => round($saleQty, 2),
            'sale_rate' => round($saleRate, 2),
            'sale_amount' => round($saleAmount, 2),
            'closing_qty' => round($closingQty, 2),
            'closing_rate' => round($closingRate, 2),
            'closing_amount' => round($closingQty * $closingRate, 2),
        ];
    }

    protected function getOpeningBalance($productId, $locationId, $date)
    {
        $lastMovement = StockMovement::where('location_id', $locationId)
            ->where('product_id', $productId)
            ->where('movement_date', '<', $date)
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMovement) {
            return [
                'quantity' => $lastMovement->balance,
                'rate' => $lastMovement->rate,
                'amount' => $lastMovement->balance * $lastMovement->rate,
            ];
        }

        return ['quantity' => 0, 'rate' => 0, 'amount' => 0];
    }

    public function createPurchase(array $data)
    {
        return DB::transaction(function() use ($data) {
            $purchase = Purchase::create([
                'purchase_no' => $data['purchase_no'],
                'location_id' => $data['location_id'],
                'user_id' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'supplier_name' => $data['supplier_name'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'status' => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                ]);
            }

            $purchase->calculateTotal();

            // Dispatch job to update stock movements
            UpdateStockMovementJob::dispatch($purchase, 'purchase');

            return $purchase;
        });
    }

    public function createSale(array $data)
    {
        return DB::transaction(function() use ($data) {
            // Check stock availability for all items
            foreach ($data['items'] as $item) {
                if (!$this->checkStockAvailability(
                    $data['location_id'],
                    $item['product_id'],
                    $item['quantity']
                )) {
                    throw new InsufficientStockException(
                        "Insufficient stock for product ID: {$item['product_id']}"
                    );
                }
            }

            $sale = Sale::create([
                'sale_no' => $data['sale_no'],
                'location_id' => $data['location_id'],
                'user_id' => auth()->id(),
                'sale_date' => $data['sale_date'],
                'customer_name' => $data['customer_name'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'status' => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                ]);
            }

            $sale->calculateTotal();

            // Dispatch job to update stock movements
            UpdateStockMovementJob::dispatch($sale, 'sale');

            return $sale;
        });
    }

    public function updateStockMovement($type, $model)
    {
        $items = $type === 'purchase' ? $model->items : $model->items;

        foreach ($items as $item) {
            $previousBalance = $this->getCurrentBalance(
                $model->location_id,
                $item->product_id,
                $type === 'purchase' ? $model->purchase_date : $model->sale_date
            );

            $quantityIn = $type === 'purchase' ? $item->quantity : 0;
            $quantityOut = $type === 'sale' ? $item->quantity : 0;
            $newBalance = $previousBalance + $quantityIn - $quantityOut;

            StockMovement::create([
                'location_id' => $model->location_id,
                'product_id' => $item->product_id,
                'movement_date' => $type === 'purchase' ? $model->purchase_date : $model->sale_date,
                'movement_type' => $type,
                'movementable_type' => get_class($model),
                'movementable_id' => $model->id,
                'quantity_in' => $quantityIn,
                'quantity_out' => $quantityOut,
                'rate' => $item->rate,
                'balance' => $newBalance,
            ]);
        }
    }

    protected function getCurrentBalance($locationId, $productId, $beforeDate)
    {
        $lastMovement = StockMovement::where('location_id', $locationId)
            ->where('product_id', $productId)
            ->where('movement_date', '<=', $beforeDate)
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastMovement ? $lastMovement->balance : 0;
    }

    public function checkStockAvailability($locationId, $productId, $quantity)
    {
        $currentStock = $this->getCurrentBalance($locationId, $productId, now());
        return $currentStock >= $quantity;
    }

    public function getProductStockHistory($locationId, $productId, $startDate, $endDate)
    {
        return StockMovement::where('location_id', $locationId)
            ->where('product_id', $productId)
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();
    }

    public function setOpeningStock($locationId, $productId, $date, $quantity, $rate)
    {
        return DB::transaction(function() use ($locationId, $productId, $date, $quantity, $rate) {
            $opening = OpeningStock::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'product_id' => $productId,
                    'date' => $date,
                ],
                [
                    'quantity' => $quantity,
                    'rate' => $rate,
                ]
            );

            // Create stock movement
            StockMovement::create([
                'location_id' => $locationId,
                'product_id' => $productId,
                'movement_date' => $date,
                'movement_type' => 'opening',
                'movementable_type' => OpeningStock::class,
                'movementable_id' => $opening->id,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'rate' => $rate,
                'balance' => $quantity,
            ]);

            return $opening;
        });
    }
}
