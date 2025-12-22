<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Location, Product, User};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FastMassDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting FAST Mass Data Generation...');
        $this->command->info('⚡ Using batch inserts for maximum speed!');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $startTime = now();

        // Step 1: Locations (100)
        $this->command->info('📍 Creating 100 Locations...');
        Location::factory(100)->create();

        // Step 2: Products (10,000)
        $this->command->info('📦 Creating 10,000 Products...');
        $this->createProductsBatch(10000);

        // Step 3: Users (100)
        $this->command->info('👥 Creating 100 Users...');
        User::factory(100)->create();

        // Step 4: Opening Stocks (20,000) - Batch Insert
        $this->command->info('📊 Creating 20,000 Opening Stocks (batch)...');
        $this->createOpeningStocksBatch(20000);

        // Step 5: Purchases (30,000) - Batch Insert
        $this->command->info('🛒 Creating 30,000 Purchases (batch)...');
        $this->createPurchasesBatch(30000);

        // Step 6: Sales (40,000) - Batch Insert
        $this->command->info('💰 Creating 40,000 Sales (batch)...');
        $this->createSalesBatch(40000);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $endTime = now();
        $duration = $endTime->diffInSeconds($startTime);

        $this->command->info('');
        $this->command->info("🎉 Completed in {$duration} seconds!");
        $this->displaySummary();
    }

    protected function createProductsBatch($total)
    {
        $batchSize = 1000;
        $batches = ceil($total / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            Product::factory($batchSize)->create();
            $this->command->info("   Progress: " . (($i + 1) * $batchSize) . "/{$total}");
        }
    }

    protected function createOpeningStocksBatch($total)
    {
        $locations = Location::pluck('id')->toArray();
        $products = Product::where('is_active', true)->limit(500)->pluck('id')->toArray();
        $date = Carbon::now()->startOfMonth();

        $openingStocks = [];
        $stockMovements = [];

        for ($i = 0; $i < $total; $i++) {
            $locationId = $locations[array_rand($locations)];
            $productId = $products[array_rand($products)];
            $quantity = rand(10, 500);
            $rate = rand(50, 10000);
            $amount = $quantity * $rate;

            $openingStocks[] = [
                'location_id' => $locationId,
                'product_id' => $productId,
                'date' => $date,
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $stockMovements[] = [
                'location_id' => $locationId,
                'product_id' => $productId,
                'movement_date' => $date,
                'movement_type' => 'opening',
                'movementable_type' => 'App\Models\OpeningStock',
                'movementable_id' => $i + 1,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'rate' => $rate,
                'balance' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($openingStocks) >= 1000) {
                DB::table('opening_stocks')->insert($openingStocks);
                DB::table('stock_movements')->insert($stockMovements);
                $openingStocks = [];
                $stockMovements = [];
                $this->command->info("   Progress: " . ($i + 1) . "/{$total}");
            }
        }

        if (!empty($openingStocks)) {
            DB::table('opening_stocks')->insert($openingStocks);
            DB::table('stock_movements')->insert($stockMovements);
        }
    }

    protected function createPurchasesBatch($total)
    {
        $locations = Location::pluck('id')->toArray();
        $products = Product::where('is_active', true)->limit(500)->pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        $suppliers = ['ABC Trading', 'XYZ Suppliers', 'Global Parts', 'Prime Components'];

        $purchases = [];
        $purchaseItems = [];
        $stockMovements = [];

        $startDate = Carbon::now()->subMonths(6)->timestamp;
        $endDate = Carbon::now()->timestamp;

        for ($i = 0; $i < $total; $i++) {
            $purchaseId = $i + 1;
            $locationId = $locations[array_rand($locations)];
            $userId = $users[array_rand($users)];
            $date = Carbon::createFromTimestamp(rand($startDate, $endDate));

            $itemsCount = rand(2, 8);
            $totalAmount = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $productId = $products[array_rand($products)];
                $quantity = rand(5, 100);
                $rate = rand(100, 15000);
                $amount = $quantity * $rate;
                $totalAmount += $amount;

                $purchaseItems[] = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $stockMovements[] = [
                    'location_id' => $locationId,
                    'product_id' => $productId,
                    'movement_date' => $date,
                    'movement_type' => 'purchase',
                    'movementable_type' => 'App\Models\Purchase',
                    'movementable_id' => $purchaseId,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'rate' => $rate,
                    'balance' => rand(100, 1000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $purchases[] = [
                'purchase_no' => 'PUR-' . $date->format('Ymd') . '-' . str_pad($purchaseId, 6, '0', STR_PAD_LEFT),
                'location_id' => $locationId,
                'user_id' => $userId,
                'purchase_date' => $date,
                'supplier_name' => $suppliers[array_rand($suppliers)],
                'remarks' => null,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($purchases) >= 500) {
                DB::table('purchases')->insert($purchases);
                DB::table('purchase_items')->insert($purchaseItems);
                DB::table('stock_movements')->insert($stockMovements);
                $purchases = [];
                $purchaseItems = [];
                $stockMovements = [];
                $this->command->info("   Progress: " . ($i + 1) . "/{$total}");
            }
        }

        if (!empty($purchases)) {
            DB::table('purchases')->insert($purchases);
            DB::table('purchase_items')->insert($purchaseItems);
            DB::table('stock_movements')->insert($stockMovements);
        }
    }

    protected function createSalesBatch($total)
    {
        $locations = Location::pluck('id')->toArray();
        $products = Product::where('is_active', true)->limit(500)->pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        $customers = ['Ahmed Khan', 'Fatima Motors', 'Ali Trading', 'Walk-in Customer'];

        $sales = [];
        $saleItems = [];
        $stockMovements = [];

        $startDate = Carbon::now()->subMonths(6)->timestamp;
        $endDate = Carbon::now()->timestamp;

        for ($i = 0; $i < $total; $i++) {
            $saleId = $i + 1;
            $locationId = $locations[array_rand($locations)];
            $userId = $users[array_rand($users)];
            $date = Carbon::createFromTimestamp(rand($startDate, $endDate));

            $itemsCount = rand(1, 6);
            $totalAmount = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $productId = $products[array_rand($products)];
                $quantity = rand(1, 50);
                $rate = rand(150, 20000);
                $amount = $quantity * $rate;
                $totalAmount += $amount;

                $saleItems[] = [
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $stockMovements[] = [
                    'location_id' => $locationId,
                    'product_id' => $productId,
                    'movement_date' => $date,
                    'movement_type' => 'sale',
                    'movementable_type' => 'App\Models\Sale',
                    'movementable_id' => $saleId,
                    'quantity_in' => 0,
                    'quantity_out' => $quantity,
                    'rate' => $rate,
                    'balance' => rand(50, 800),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $sales[] = [
                'sale_no' => 'SAL-' . $date->format('Ymd') . '-' . str_pad($saleId, 6, '0', STR_PAD_LEFT),
                'location_id' => $locationId,
                'user_id' => $userId,
                'sale_date' => $date,
                'customer_name' => $customers[array_rand($customers)],
                'remarks' => null,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($sales) >= 500) {
                DB::table('sales')->insert($sales);
                DB::table('sale_items')->insert($saleItems);
                DB::table('stock_movements')->insert($stockMovements);
                $sales = [];
                $saleItems = [];
                $stockMovements = [];
                $this->command->info("   Progress: " . ($i + 1) . "/{$total}");
            }
        }

        if (!empty($sales)) {
            DB::table('sales')->insert($sales);
            DB::table('sale_items')->insert($saleItems);
            DB::table('stock_movements')->insert($stockMovements);
        }
    }

    protected function displaySummary()
    {
        $this->command->table(
            ['Table', 'Records'],
            [
                ['Locations', DB::table('locations')->count()],
                ['Products', DB::table('products')->count()],
                ['Users', DB::table('users')->count()],
                ['Opening Stocks', DB::table('opening_stocks')->count()],
                ['Purchases', DB::table('purchases')->count()],
                ['Purchase Items', DB::table('purchase_items')->count()],
                ['Sales', DB::table('sales')->count()],
                ['Sale Items', DB::table('sale_items')->count()],
                ['Stock Movements', DB::table('stock_movements')->count()],
            ]
        );
    }
}