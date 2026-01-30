<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Logistic\Models\Vehicle;

class LogisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'plate_number' => 'B 1234 ABC',
                'model' => 'Honda Vario 160 (Courier Motor)',
                'capacity_kg' => 20,
                'is_active' => true,
            ],
            [
                'plate_number' => 'B 5678 DEF',
                'model' => 'Yamaha NMAX (Courier Motor)',
                'capacity_kg' => 25,
                'is_active' => true,
            ],
            [
                'plate_number' => 'B 9012 GHI',
                'model' => 'Daihatsu Gran Max Blind Van (Distribution)',
                'capacity_kg' => 800,
                'is_active' => true,
            ],
            [
                'plate_number' => 'B 3456 JKL',
                'model' => 'Toyota Avanza (Office Operations)',
                'capacity_kg' => 400,
                'is_active' => true,
            ],
            [
                'plate_number' => 'B 7890 MNO',
                'model' => 'Suzuki Carry Pick Up',
                'capacity_kg' => 1000,
                'is_active' => false,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }

        $this->command->info('✓ Logistic Vehicles seeded successfully!');
    }
}
