<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Product;
use App\Models\OpeningStock;
use App\Models\StockMovement;
use Carbon\Carbon;
class OpeningStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         // Get first 3 locations and first 15 products
         $locations = Location::where('is_active', true)->take(3)->get();
         $products = Product::where('is_active', true)
             ->where('is_discontinued', false)
             ->take(15)
             ->get();
 
         $openingDate = Carbon::now()->startOfMonth();
 
         $count = 0;
 
         foreach ($locations as $location) {
             foreach ($products as $product) {
                 // Random opening stock between 10 and 200
                 $quantity = rand(10, 200);
                 
                 // Random rate between 50 and 5000
                 $rate = rand(50, 5000);
                 
                 $amount = $quantity * $rate;
 
                 // Create opening stock
                 $openingStock = OpeningStock::create([
                     'location_id' => $location->id,
                     'product_id' => $product->id,
                     'date' => $openingDate,
                     'quantity' => $quantity,
                     'rate' => $rate,
                     'amount' => $amount,
                 ]);
 
                 // Create stock movement
                 StockMovement::create([
                     'location_id' => $location->id,
                     'product_id' => $product->id,
                     'movement_date' => $openingDate,
                     'movement_type' => 'opening',
                     'movementable_type' => OpeningStock::class,
                     'movementable_id' => $openingStock->id,
                     'quantity_in' => $quantity,
                     'quantity_out' => 0,
                     'rate' => $rate,
                     'balance' => $quantity,
                 ]);
 
                 $count++;
             }
         }
 
         $this->command->info("Opening stocks seeded successfully! Total: {$count}");
     
    }
}
