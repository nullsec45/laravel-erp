<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Unit;

class InventoryMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds
     */
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Mobile Wifi', 'slug' => 'mobile-wifi', 'description' => 'Mobile Wifi devices'],
            ['name' => 'Global Wifi', 'slug' => 'global-wifi', 'description' => 'Global Wifi devices'],
            ['name' => 'eSim Traveling', 'slug' => 'esim-traveling', 'description' => 'eSim Traveling products'],
            ['name' => 'Travel Sim Cards', 'slug' => 'travel-sim-cards', 'description' => 'Travel Sim Cards'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Brands
        $brands = [
            ['name' => 'Java Mifi', 'slug' => 'java-mifi', 'description' => 'Java Mifi'],

        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }

        // Units
        $units = [
            ['name' => 'Pieces', 'short_name' => 'PCS'],
            ['name' => 'License', 'short_name' => 'LIC'],
            ['name' => 'Days', 'short_name' => 'DAY'],
            ['name' => 'Package', 'short_name' => 'PKG'],
            ['name' => 'Box', 'short_name' => 'BOX'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['short_name' => $unit['short_name']],
                $unit
            );
        }

        $this->command->info('Inventory master data seeded successfully!');
    }
}
