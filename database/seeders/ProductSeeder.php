<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = [
            // Auto Parts
            [
                'name' => 'FLYWHEEL ISUZU NPR',
                'code' => 'FLYW-ISUZU-001',
                'description' => 'Original flywheel for ISUZU NPR trucks, heavy-duty construction',
                'unit' => 'pcs',
                'minimum_stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'BRAKE PAD SET TOYOTA',
                'code' => 'BRKPAD-TOY-001',
                'description' => 'Ceramic brake pads for Toyota vehicles, premium quality',
                'unit' => 'set',
                'minimum_stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'ENGINE OIL 5W-30',
                'code' => 'OIL-5W30-001',
                'description' => 'Synthetic engine oil, 5W-30 grade, 4 liter bottle',
                'unit' => 'ltr',
                'minimum_stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'AIR FILTER HONDA CIVIC',
                'code' => 'AIRFLT-HND-001',
                'description' => 'High-performance air filter for Honda Civic 2016-2022',
                'unit' => 'pcs',
                'minimum_stock' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'SPARK PLUG NGK',
                'code' => 'SPRK-NGK-001',
                'description' => 'NGK iridium spark plugs, long-life performance',
                'unit' => 'pcs',
                'minimum_stock' => 50,
                'is_active' => true,
            ],

            // Laboratory Equipment
            [
                'name' => 'TEST TUBE GLASS',
                'code' => 'TSTUBE-GLS-001',
                'description' => 'Borosilicate glass test tubes, 20ml capacity',
                'unit' => 'pcs',
                'minimum_stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'BEAKER 500ML',
                'code' => 'BEKR-500-001',
                'description' => 'Laboratory grade beaker, 500ml capacity',
                'unit' => 'pcs',
                'minimum_stock' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'PIPETTE 10ML',
                'code' => 'PIPET-10-001',
                'description' => 'Graduated pipette, 10ml, Class A',
                'unit' => 'pcs',
                'minimum_stock' => 25,
                'is_active' => true,
            ],

            // Fuels & Chemicals
            [
                'name' => 'DIESEL FUEL',
                'code' => 'FUEL-DSL-001',
                'description' => 'High-speed diesel fuel for industrial use',
                'unit' => 'ltr',
                'minimum_stock' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'PETROL 92 OCTANE',
                'code' => 'FUEL-PTR-092',
                'description' => 'Unleaded petrol, 92 octane rating',
                'unit' => 'ltr',
                'minimum_stock' => 300,
                'is_active' => true,
            ],
            [
                'name' => 'HYDRAULIC OIL',
                'code' => 'OIL-HYD-001',
                'description' => 'ISO VG 46 hydraulic oil for machinery',
                'unit' => 'ltr',
                'minimum_stock' => 200,
                'is_active' => true,
            ],

            // Hardware & Tools
            [
                'name' => 'BOLT M12x50',
                'code' => 'BLT-M12-50',
                'description' => 'High tensile steel bolt, M12x50mm',
                'unit' => 'pcs',
                'minimum_stock' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'NUT M12',
                'code' => 'NUT-M12-001',
                'description' => 'Hex nut M12, galvanized',
                'unit' => 'pcs',
                'minimum_stock' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'WASHER M12',
                'code' => 'WSHR-M12-001',
                'description' => 'Flat washer M12, stainless steel',
                'unit' => 'pcs',
                'minimum_stock' => 1000,
                'is_active' => true,
            ],
            [
                'name' => 'DRILL BIT 10MM',
                'code' => 'DRBIT-10-001',
                'description' => 'HSS drill bit, 10mm diameter',
                'unit' => 'pcs',
                'minimum_stock' => 20,
                'is_active' => true,
            ],

            // Electrical Components
            [
                'name' => 'COPPER WIRE 2.5MM',
                'code' => 'WIRE-CU-25',
                'description' => 'Single core copper wire, 2.5mm thickness',
                'unit' => 'mtr',
                'minimum_stock' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'LED BULB 15W',
                'code' => 'LED-15W-001',
                'description' => 'Energy-saving LED bulb, 15 watts, cool white',
                'unit' => 'pcs',
                'minimum_stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'CIRCUIT BREAKER 20A',
                'code' => 'CBR-20A-001',
                'description' => 'Miniature circuit breaker, 20 amp rating',
                'unit' => 'pcs',
                'minimum_stock' => 30,
                'is_active' => true,
            ],

            // Building Materials
            [
                'name' => 'CEMENT 50KG BAG',
                'code' => 'CMT-50KG-001',
                'description' => 'Portland cement, 50kg bag',
                'unit' => 'bag',
                'minimum_stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'SAND FINE',
                'code' => 'SND-FN-001',
                'description' => 'Fine sand for construction',
                'unit' => 'kg',
                'minimum_stock' => 2000,
                'is_active' => true,
            ],
            [
                'name' => 'PAINT WHITE 5L',
                'code' => 'PNT-WHT-5L',
                'description' => 'Emulsion paint, white color, 5 liter can',
                'unit' => 'ltr',
                'minimum_stock' => 20,
                'is_active' => true,
            ],

            // Office Supplies
            [
                'name' => 'A4 PAPER REAM',
                'code' => 'PPR-A4-001',
                'description' => 'A4 size paper, 80gsm, 500 sheets per ream',
                'unit' => 'ream',
                'minimum_stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'MARKER PEN BLACK',
                'code' => 'MRK-BLK-001',
                'description' => 'Permanent marker pen, black color',
                'unit' => 'pcs',
                'minimum_stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'STAPLER HEAVY DUTY',
                'code' => 'STPL-HD-001',
                'description' => 'Heavy-duty stapler, 50 sheet capacity',
                'unit' => 'pcs',
                'minimum_stock' => 10,
                'is_active' => true,
            ],

            // Discontinued Products (for testing)
            [
                'name' => 'OLD MODEL CARBURETOR',
                'code' => 'CARB-OLD-001',
                'description' => 'Discontinued carburetor model',
                'unit' => 'pcs',
                'minimum_stock' => 0,
                'is_active' => false,
                'is_discontinued' => true,
                'discontinued_at' => now()->subMonths(6),
            ],
            [
                'name' => 'LEGACY FILTER KIT',
                'code' => 'FILT-LEG-001',
                'description' => 'Old filter kit, no longer in production',
                'unit' => 'set',
                'minimum_stock' => 0,
                'is_active' => false,
                'is_discontinued' => true,
                'discontinued_at' => now()->subYear(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Products seeded successfully! Total: ' . count($products));
    }
}
