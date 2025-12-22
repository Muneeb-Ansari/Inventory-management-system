<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            RolesAndPermissionsSeeder::class,
            LocationSeeder::class,
            ProductSeeder::class,
            OpeningStockSeeder::class,
            PurchaseSeeder::class,
            SaleSeeder::class,
            MassDataSeeder::class,
            FastMassDataSeeder::class,
        ]);
    }
}
