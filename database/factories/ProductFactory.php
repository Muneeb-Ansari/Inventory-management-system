<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = [
            'Auto Parts' => [
                'names' => ['Flywheel', 'Brake Pad', 'Air Filter', 'Spark Plug', 'Battery', 'Radiator', 'Alternator', 'Starter Motor', 'Fuel Pump', 'Water Pump', 'Timing Belt', 'Fan Belt', 'Clutch Plate', 'Shock Absorber', 'Ball Joint', 'Tie Rod End', 'CV Joint', 'Drive Shaft', 'Exhaust Pipe', 'Muffler', 'Oxygen Sensor', 'Fuel Injector', 'Ignition Coil', 'Wheel Bearing', 'Head Gasket'],
                'brands' => ['Toyota', 'Honda', 'Suzuki', 'Nissan', 'Mitsubishi', 'Isuzu', 'Mazda', 'Daihatsu', 'Hyundai', 'Kia'],
                'unit' => ['pcs', 'set'],
                'min_stock' => [5, 50],
            ],
            'Electronics' => [
                'names' => ['LED Bulb', 'Circuit Breaker', 'Copper Wire', 'Cable', 'Switch', 'Socket', 'Transformer', 'Capacitor', 'Resistor', 'Diode', 'Transistor', 'Relay', 'Fuse', 'MCB', 'RCCB', 'Contactor', 'Timer'],
                'brands' => ['Philips', 'Siemens', 'Schneider', 'ABB', 'Legrand', 'Havells', 'Panasonic', 'Samsung'],
                'unit' => ['pcs', 'mtr'],
                'min_stock' => [20, 100],
            ],
            'Hardware' => [
                'names' => ['Bolt', 'Nut', 'Washer', 'Screw', 'Nail', 'Rivet', 'Pin', 'Anchor', 'Clamp', 'Bracket', 'Hinge', 'Lock', 'Handle', 'Knob'],
                'brands' => ['Generic', 'Stanley', 'Bosch', 'Makita', 'DeWalt', 'Milwaukee'],
                'unit' => ['pcs', 'kg'],
                'min_stock' => [100, 1000],
            ],
            'Chemicals' => [
                'names' => ['Engine Oil', 'Hydraulic Oil', 'Gear Oil', 'Brake Fluid', 'Coolant', 'Grease', 'Diesel', 'Petrol', 'Thinner', 'Paint', 'Varnish', 'Adhesive', 'Sealant', 'Cleaner'],
                'brands' => ['Shell', 'Mobil', 'Castrol', 'Total', 'Caltex', 'PSO'],
                'unit' => ['ltr', 'kg'],
                'min_stock' => [50, 500],
            ],
            'Building Materials' => [
                'names' => ['Cement', 'Sand', 'Gravel', 'Brick', 'Block', 'Tile', 'Marble', 'Granite', 'Steel Bar', 'Wire Mesh', 'Pipe', 'Fitting', 'Valve'],
                'brands' => ['Lucky', 'DG Khan', 'Bestway', 'Maple Leaf', 'Fauji'],
                'unit' => ['bag', 'kg', 'pcs'],
                'min_stock' => [50, 500],
            ],
            'Office Supplies' => [
                'names' => ['Paper', 'Pen', 'Pencil', 'Marker', 'Highlighter', 'Eraser', 'Ruler', 'Stapler', 'Punch', 'Clip', 'Pin', 'Tape', 'Glue', 'Folder', 'File'],
                'brands' => ['Papyrus', 'Dollar', 'Faber Castell', 'Stabilo', '3M'],
                'unit' => ['pcs', 'box', 'ream'],
                'min_stock' => [20, 200],
            ],
        ];

        $category = $this->faker->randomElement(array_keys($categories));
        $catData = $categories[$category];
        
        $name = $this->faker->randomElement($catData['names']);
        $brand = $this->faker->randomElement($catData['brands']);
        $size = $this->faker->randomElement(['', 'Small', 'Medium', 'Large', '10mm', '12mm', '15mm', '20mm', '5W', '10W', '15W', '20W']);

        $fullName = trim("{$name} {$brand} {$size}");

        return [
            'name' => $fullName,
            'code' => strtoupper($this->faker->unique()->bothify('PRD-####-???')),
            'description' => $this->faker->sentence(),
            'unit' => $this->faker->randomElement($catData['unit']),
            'minimum_stock' => $this->faker->numberBetween($catData['min_stock'][0], $catData['min_stock'][1]),
            'is_active' => $this->faker->randomElement([true, true, true, true, false]), // 80% active
            'is_discontinued' => false,
        ];
    }

    public function discontinued()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
                'is_discontinued' => true,
                'discontinued_at' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            ];
        });
    }
}