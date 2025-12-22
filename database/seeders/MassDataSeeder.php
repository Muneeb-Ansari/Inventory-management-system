<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Location, Product, User, Purchase, PurchaseItem, Sale, SaleItem, OpeningStock, StockMovement};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MassDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Mass Data Generation...');
        $this->command->info('⚠️  This will take several minutes. Please wait...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Step 1: Create Locations (100)
        $this->command->info('📍 Creating 100 Locations...');
        Location::factory(100)->create();
        $locations = Location::all();
        $this->command->info('✅ Locations created: ' . $locations->count());

        // Step 2: Create Products (10,000)
        $this->command->info('📦 Creating 10,000 Products...');
        $this->createProductsInChunks(10000);
        $this->command->info('✅ Products created: 10,000');

        // Step 3: Create Users (100)
        $this->command->info('👥 Creating 100 Users...');
        User::factory(100)->create()->each(function($user) {
            $user->assignRole('manager'); // Assign default role
        });
        $users = User::all();
        $this->command->info('✅ Users created: ' . $users->count());

        // Step 4: Create Opening Stocks (20,000)
        $this->command->info('📊 Creating 20,000 Opening Stock entries...');
        $this->createOpeningStocks($locations, 20000);
        $this->command->info('✅ Opening stocks created');

        // Step 5: Create Purchases (30,000)
        $this->command->info('🛒 Creating 30,000 Purchases with items...');
        $this->createPurchases($locations, $users, 30000);
        $this->command->info('✅ Purchases created');

        // Step 6: Create Sales (40,000)
        $this->command->info('💰 Creating 40,000 Sales with items...');
        $this->createSales($locations, $users, 40000);
        $this->command->info('✅ Sales created');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('🎉 Mass Data Generation Complete!');
        $this->command->info('');
        $this->displaySummary();
    }

    protected function createProductsInChunks($total)
    {
        $chunkSize = 1000;
        $chunks = ceil($total / $chunkSize);

        for ($i = 0; $i < $chunks; $i++) {
            Product::factory($chunkSize)->create();
            $this->command->info("   Progress: " . (($i + 1) * $chunkSize) . "/{$total}");
        }
    }

    protected function createOpeningStocks($locations, $total)
    {
        $products = Product::where('is_active', true)->inRandomOrder()->limit(200)->get();
        $startDate = Carbon::now()->startOfMonth();
        $count = 0;

        foreach ($locations->random(min(100, $locations->count())) as $location) {
            foreach ($products->random(min(200, $products->count())) as $product) {
                if ($count >= $total) break 2;

                $quantity = rand(10, 500);
                $rate = rand(50, 10000);

                $opening = OpeningStock::create([
                    'location_id' => $location->id,
                    'product_id' => $product->id,
                    'date' => $startDate,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $quantity * $rate,
                ]);

                StockMovement::create([
                    'location_id' => $location->id,
                    'product_id' => $product->id,
                    'movement_date' => $startDate,
                    'movement_type' => 'opening',
                    'movementable_type' => OpeningStock::class,
                    'movementable_id' => $opening->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'rate' => $rate,
                    'balance' => $quantity,
                ]);

                $count++;
                if ($count % 1000 == 0) {
                    $this->command->info("   Progress: {$count}/{$total}");
                }
            }
        }
    }

    protected function createPurchases($locations, $users, $total)
    {
        $products = Product::where('is_active', true)->get();
        $suppliers = [
            'ABC Trading Co.', 'XYZ Suppliers Ltd.', 'Global Parts International',
            'Prime Auto Components', 'Universal Hardware Store', 'Tech Solutions Pakistan',
            'Metro Wholesale Market', 'Industrial Equipment Corp', 'National Traders',
            'Allied Supplies', 'Continental Trading', 'Eastern Corporation'
        ];

        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 0; $i < $total; $i++) {
            $location = $locations->random();
            $user = $users->random();
            $date = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

            $purchaseNo = 'PUR-' . $date->format('Ymd') . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'purchase_no' => $purchaseNo,
                'location_id' => $location->id,
                'user_id' => $user->id,
                'purchase_date' => $date,
                'supplier_name' => $suppliers[array_rand($suppliers)],
                'remarks' => rand(0, 1) ? 'Bulk order' : null,
                'total_amount' => 0,
                'status' => 'completed',
            ]);

            $itemsCount = rand(2, 10);
            $totalAmount = 0;

            foreach ($products->random($itemsCount) as $product) {
                $quantity = rand(5, 100);
                $rate = rand(100, 15000);
                $amount = $quantity * $rate;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                ]);

                // Update stock movement
                $previousBalance = $this->getBalance($location->id, $product->id, $date);
                $newBalance = $previousBalance + $quantity;

                StockMovement::create([
                    'location_id' => $location->id,
                    'product_id' => $product->id,
                    'movement_date' => $date,
                    'movement_type' => 'purchase',
                    'movementable_type' => Purchase::class,
                    'movementable_id' => $purchase->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'rate' => $rate,
                    'balance' => $newBalance,
                ]);

                $totalAmount += $amount;
            }

            $purchase->update(['total_amount' => $totalAmount]);

            if (($i + 1) % 1000 == 0) {
                $this->command->info("   Progress: " . ($i + 1) . "/{$total}");
            }
        }
    }

    protected function createSales($locations, $users, $total)
    {
        $products = Product::where('is_active', true)->get();
        $customers = [
            'Ahmed Khan', 'Fatima Motors', 'Ali Trading', 'Sara Electronics',
            'Bilal Corporation', 'Ayesha Enterprises', 'Hassan Auto Parts',
            'Zainab Hardware', 'Usman Industries', 'Mariam Supplies',
            'Walk-in Customer', 'Cash Sale', 'Abdul Rehman', 'Zainab Traders',
            'Khan Brothers', 'Malik & Sons', 'Shahid Enterprises'
        ];

        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 0; $i < $total; $i++) {
            $location = $locations->random();
            $user = $users->random();
            $date = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

            $saleNo = 'SAL-' . $date->format('Ymd') . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'sale_no' => $saleNo,
                'location_id' => $location->id,
                'user_id' => $user->id,
                'sale_date' => $date,
                'customer_name' => $customers[array_rand($customers)],
                'remarks' => rand(0, 1) ? 'Regular sale' : null,
                'total_amount' => 0,
                'status' => 'completed',
            ]);

            $itemsCount = rand(1, 8);
            $totalAmount = 0;

            foreach ($products->random($itemsCount) as $product) {
                $currentStock = $this->getBalance($location->id, $product->id, $date);

                if ($currentStock > 0) {
                    $maxSale = max(1, (int)($currentStock * 0.3));
                    $quantity = rand(1, $maxSale);
                    $rate = rand(150, 20000);
                    $amount = $quantity * $rate;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'rate' => $rate,
                        'amount' => $amount,
                    ]);

                    $newBalance = $currentStock - $quantity;

                    StockMovement::create([
                        'location_id' => $location->id,
                        'product_id' => $product->id,
                        'movement_date' => $date,
                        'movement_type' => 'sale',
                        'movementable_type' => Sale::class,
                        'movementable_id' => $sale->id,
                        'quantity_in' => 0,
                        'quantity_out' => $quantity,
                        'rate' => $rate,
                        'balance' => $newBalance,
                    ]);

                    $totalAmount += $amount;
                }
            }

            if ($totalAmount > 0) {
                $sale->update(['total_amount' => $totalAmount]);
            } else {
                $sale->delete();
            }

            if (($i + 1) % 1000 == 0) {
                $this->command->info("   Progress: " . ($i + 1) . "/{$total}");
            }
        }
    }

    protected function getBalance($locationId, $productId, $date)
    {
        $movement = StockMovement::where('location_id', $locationId)
            ->where('product_id', $productId)
            ->where('movement_date', '<=', $date)
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $movement ? $movement->balance : 0;
    }

    protected function displaySummary()
    {
        $this->command->table(
            ['Table', 'Records'],
            [
                ['Locations', Location::count()],
                ['Products', Product::count()],
                ['Opening Stocks', OpeningStock::count()],
                ['Purchases', Purchase::count()],
                ['Purchase Items', PurchaseItem::count()],
                ['Sales', Sale::count()],
                ['Sale Items', SaleItem::count()],
                ['Stock Movements', StockMovement::count()],
                ['TOTAL', Location::count() + Product::count() + User::count() + OpeningStock::count() + Purchase::count() + PurchaseItem::count() + Sale::count() + SaleItem::count() + StockMovement::count()],
            ]
        );
    }
}