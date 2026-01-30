<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Inventory\Models\{Product, Category, Brand, Unit};

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catMifi = Category::where('slug', 'mobile-wifi')->first();
        $catEsim = Category::where('slug', 'esim-traveling')->first();
        $catSim = Category::where('slug', 'travel-sim-cards')->first();

        $brandJava = Brand::where('slug', 'java-mifi')->first();

        $unitPcs = Unit::where('short_name', 'PCS')->first();
        $unitLic = Unit::where('short_name', 'LIC')->first();

        $products = [
            [
                'sku' => 'MIFI-HUA-E5576',
                'name' => 'Java Mifi Pro - Huawei E5576',
                'description' => '4G LTE Mobile Wi-Fi, support up to 10 users.',
                'category_id' => $catMifi?->id,
                'brand_id' => $brandJava?->id,
                'unit_id' => $unitPcs?->id,
                'barcode' => '888000111001',
                'cost_price' => 450000,
                'selling_price' => 650000,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'reorder_level' => 20,
                'is_active' => true,
                'type' => 'physical',
            ],
            [
                'sku' => 'ESIM-ASIA-7D',
                'name' => 'eSIM Traveling Asia 7 Days Unlimited',
                'description' => 'eSIM for Asia (Singapore, Malaysia, Thailand, etc) - 7 Days.',
                'category_id' => $catEsim?->id,
                'brand_id' => $brandJava?->id,
                'unit_id' => $unitLic?->id,
                'barcode' => 'ESIMASIA07D',
                'cost_price' => 120000,
                'selling_price' => 185000,
                'minimum_stock' => 90,
                'maximum_stock' => 200,
                'reorder_level' => 0,
                'is_active' => true,
                'type' => 'digital',
            ],
            [
                'sku' => 'SIM-JAPAN-15D',
                'name' => 'Travel SIM Card Japan 15 Days',
                'description' => 'Physical SIM Card for Japan - 15 Days 4G Speed.',
                'category_id' => $catSim?->id,
                'brand_id' => $brandJava?->id,
                'unit_id' => $unitPcs?->id,
                'barcode' => 'SIMJPN15D',
                'cost_price' => 250000,
                'selling_price' => 375000,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_level' => 100,
                'is_active' => true,
                'type' => 'physical',
            ],
            [
                'sku' => 'MIFI-ZTE-MF920',
                'name' => 'Java Mifi Basic - ZTE MF920',
                'description' => 'Standard 4G Mobile Wi-Fi for international roaming.',
                'category_id' => $catMifi?->id,
                'brand_id' => $brandJava?->id,
                'unit_id' => $unitPcs?->id,
                'barcode' => '888000111002',
                'cost_price' => 400000,
                'selling_price' => 550000,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'reorder_level' => 30,
                'is_active' => true,
                'type' => 'physical',
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        $this->command->info('✓ Products  seeded successfully!');
    }
}
