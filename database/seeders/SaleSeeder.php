<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use App\Models\User;
class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $locations = Location::where('is_active', true)->get();
        $products = Product::where('is_active', true)
            ->where('is_discontinued', false)
            ->get();
        $user = User::first();

        $customers = [
            'Ahmed Khan',
            'Fatima Motors',
            'Ali Trading',
            'Sara Electronics',
            'Bilal Corporation',
            'Ayesha Enterprises',
            'Hassan Auto Parts',
            'Zainab Hardware',
            'Usman Industries',
            'Mariam Supplies',
            'Walk-in Customer',
            'Cash Sale',
        ];

        $saleCount = 0;
        $itemCount = 0;

        // Generate sales for last 3 months
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDays(rand(1, 3))) {
            foreach ($locations->random(rand(1, 3)) as $location) {
                // Create 2-5 sales per iteration
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $saleNo = 'SAL-' . $date->format('Ymd') . '-' . str_pad($saleCount + 1, 4, '0', STR_PAD_LEFT);

                    $sale = Sale::create([
                        'sale_no' => $saleNo,
                        'location_id' => $location->id,
                        'user_id' => $user->id,
                        'sale_date' => $date->copy(),
                        'customer_name' => $customers[array_rand($customers)],
                        'remarks' => rand(0, 1) ? 'Regular sale' : null,
                        'total_amount' => 0,
                        'status' => 'completed',
                    ]);

                    // Add 1-5 items per sale
                    $itemsInSale = rand(1, 5);
                    $totalAmount = 0;

                    foreach ($products->random($itemsInSale) as $product) {
                        // Get current stock
                        $currentStock = StockMovement::where('location_id', $location->id)
                            ->where('product_id', $product->id)
                            ->where('movement_date', '<=', $date)
                            ->orderBy('movement_date', 'desc')
                            ->orderBy('id', 'desc')
                            ->first();

                        $availableStock = $currentStock ? $currentStock->balance : 0;

                        // Only sell if stock is available
                        if ($availableStock > 0) {
                            // Sell between 1 and 50% of available stock
                            $maxSale = max(1, (int)($availableStock * 0.5));
                            $quantity = rand(1, $maxSale);
                            
                            // Sale rate is usually higher than purchase rate
                            $purchaseRate = $currentStock ? $currentStock->rate : 100;
                            $rate = $purchaseRate * rand(120, 180) / 100; // 20-80% markup
                            $amount = $quantity * $rate;

                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $product->id,
                                'quantity' => $quantity,
                                'rate' => $rate,
                                'amount' => $amount,
                            ]);

                            $newBalance = $availableStock - $quantity;

                            // Create stock movement
                            StockMovement::create([
                                'location_id' => $location->id,
                                'product_id' => $product->id,
                                'movement_date' => $date->copy(),
                                'movement_type' => 'sale',
                                'movementable_type' => Sale::class,
                                'movementable_id' => $sale->id,
                                'quantity_in' => 0,
                                'quantity_out' => $quantity,
                                'rate' => $rate,
                                'balance' => $newBalance,
                            ]);

                            $totalAmount += $amount;
                            $itemCount++;
                        }
                    }

                    // Only keep sales that have items
                    if ($totalAmount > 0) {
                        $sale->update(['total_amount' => $totalAmount]);
                        $saleCount++;
                    } else {
                        $sale->delete();
                    }
                }
            }
        }

        $this->command->info("Sales seeded successfully!");
        $this->command->info("Total Sales: {$saleCount}");
        $this->command->info("Total Items: {$itemCount}");
    }
}
