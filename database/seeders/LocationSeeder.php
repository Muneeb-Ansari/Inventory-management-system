<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $locations = [
            [
                'name' => 'Muzaffar Garh Factory',
                'code' => 'MGH-FAC-01',
                'address' => 'Industrial Area, Muzaffar Garh, Punjab, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Karachi Main Warehouse',
                'code' => 'KHI-WH-01',
                'address' => 'Port Qasim, Karachi, Sindh, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Lahore Distribution Center',
                'code' => 'LHE-DC-01',
                'address' => 'Sundar Industrial Estate, Lahore, Punjab, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Islamabad Retail Store',
                'code' => 'ISB-RT-01',
                'address' => 'I-9 Industrial Area, Islamabad, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Faisalabad Branch',
                'code' => 'FSD-BR-01',
                'address' => 'Jhang Road, Faisalabad, Punjab, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Multan Regional Office',
                'code' => 'MLT-RO-01',
                'address' => 'Bosan Road, Multan, Punjab, Pakistan',
                'is_active' => true,
            ],
            [
                'name' => 'Peshawar Depot',
                'code' => 'PSH-DP-01',
                'address' => 'Ring Road, Peshawar, KPK, Pakistan',
                'is_active' => false,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        $this->command->info('Locations seeded successfully!');
    }
}
