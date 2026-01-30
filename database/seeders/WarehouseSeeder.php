<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds - Create default warehouse
     */
    public function run(): void
    {
        $warehouses = [
            [
                'code' => 'WH-Main-Kuningan',
                'name' => 'Main Warehouse',
                'address' => 'Jl. Mega Kuningan No. 123',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12345',
                'phone' => '+628123456789',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'WH-Branch-Jakarta',
                'name' => 'Branch Warehouse Jakarta',
                'address' => 'Jalan Bambu Duri 2',
                'city' => 'Jakarta Timur',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '40123',
                'phone' => '+62 8123456789',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }

        $this->command->info('Warehouses seeded successfully!');
    }
}
