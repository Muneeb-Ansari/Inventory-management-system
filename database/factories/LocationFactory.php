<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $types = ['Factory', 'Warehouse', 'Store', 'Branch', 'Depot', 'Center', 'Outlet', 'Hub'];
        $cities = [
            'Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 
            'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala',
            'Hyderabad', 'Bahawalpur', 'Sargodha', 'Sukkur', 'Larkana',
            'Muzaffargarh', 'Rahim Yar Khan', 'Sahiwal', 'Okara', 'Wah Cantt'
        ];

        $city = $this->faker->randomElement($cities);
        $type = $this->faker->randomElement($types);
        $number = $this->faker->numberBetween(1, 99);

        return [
            'name' => "{$city} {$type} {$number}",
            'code' => strtoupper(substr($city, 0, 3)) . '-' . strtoupper(substr($type, 0, 2)) . '-' . str_pad($number, 2, '0', STR_PAD_LEFT),
            'address' => $this->faker->address(),
            'is_active' => $this->faker->randomElement([true, true, true, false]), // 75% active
        ];
    }
}