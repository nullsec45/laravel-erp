<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\HR\Models\HR;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core & Security
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            MasterDataHRSeeder::class,
            HRSeeder::class,


            WarehouseSeeder::class,
            InventoryMasterDataSeeder::class,
            InventorySeeder::class,

            CustomersSeeder::class,
            PurchasingSeeder::class,
            LogisticSeeder::class,
            SystemSeeder::class,

            // Production (if exists)
            // ProductionSeeder::class,
        ]);

        $this->command->info('✅ All seeders completed successfully!');
    }
}
