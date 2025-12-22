<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use App\Models\User;
class PurchaseSeeder extends Seeder
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
        $user = User::first(); // Use first user (admin)

        $suppliers = [
            'ABC Trading Co.',
            'XYZ Suppliers Ltd.',
            'Global Parts International',
            'Prime Auto Components',
            'Universal Hardware Store',
            'Tech Solutions Pakistan',
            'Metro Wholesale Market',
            'Industrial Equipment Corp',
        ];

        $purchaseCount = 0;
        $itemCount = 0;

        // Generate purchases for last 3 months
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDays(rand(1, 5))) {
            foreach ($locations->random(rand(1, 2)) as $location) {
                // Create 1-3 purchases per iteration
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $purchaseNo = 'PUR-' . $date->format('Ymd') . '-' . str_pad($purchaseCount + 1, 4, '0', STR_PAD_LEFT);

                    $purchase = Purchase::create([
                        'purchase_no' => $purchaseNo,
                        'location_id' => $location->id,
                        'user_id' => $user->id,
                        'purchase_date' => $date->copy(),
                        'supplier_name' => $suppliers[array_rand($suppliers)],
                        'remarks' => rand(0, 1) ? 'Regular purchase order' : null,
                        'total_amount' => 0,
                        'status' => 'completed',
                    ]);

                    // Add 2-8 items per purchase
                    $itemsInPurchase = rand(2, 8);
                    $totalAmount = 0;

                    foreach ($products->random($itemsInPurchase) as $product) {
                        $quantity = rand(5, 100);
                        $rate = rand(100, 10000);
                        $amount = $quantity * $rate;

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'rate' => $rate,
                            'amount' => $amount,
                        ]);

                        // Get previous balance
                        $previousMovement = StockMovement::where('location_id', $location->id)
                            ->where('product_id', $product->id)
                            ->where('movement_date', '<=', $date)
                            ->orderBy('movement_date', 'desc')
                            ->orderBy('id', 'desc')
                            ->first();

                        $previousBalance = $previousMovement ? $previousMovement->balance : 0;
                        $newBalance = $previousBalance + $quantity;

                        // Create stock movement
                        StockMovement::create([
                            'location_id' => $location->id,
                            'product_id' => $product->id,
                            'movement_date' => $date->copy(),
                            'movement_type' => 'purchase',
                            'movementable_type' => Purchase::class,
                            'movementable_id' => $purchase->id,
                            'quantity_in' => $quantity,
                            'quantity_out' => 0,
                            'rate' => $rate,
                            'balance' => $newBalance,
                        ]);

                        $totalAmount += $amount;
                        $itemCount++;
                    }

                    $purchase->update(['total_amount' => $totalAmount]);
                    $purchaseCount++;
                }
            }
        }

        $this->command->info("Purchases seeded successfully!");
        $this->command->info("Total Purchases: {$purchaseCount}");
        $this->command->info("Total Items: {$itemCount}");
    }
}
