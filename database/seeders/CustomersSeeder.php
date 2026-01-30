<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sales\Models\Customer;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds
     */
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'CORP-001',
                'name' => 'PT Traveloka Indonesia',
                'email' => 'procurement@traveloka.com',
                'phone' => '021-29775800',
                'mobile' => '081211112222',
                'address' => 'Wisma 77 Tower 2, Slipi',
                'city' => 'Jakarta Barat',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '11410',
                'credit_limit' => 100000000,
                'payment_terms' => 30,
                'is_active' => true,
            ],
            [
                'customer_code' => 'AGNT-001',
                'name' => 'Panorama JTB Tours',
                'email' => 'info@panorama-jtb.com',
                'phone' => '021-25505555',
                'mobile' => '081388889999',
                'address' => 'Panorama Building, Jl. Tomang Raya No. 63',
                'city' => 'Jakarta Barat',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '11440',
                'credit_limit' => 50000000,
                'payment_terms' => 15,
                'is_active' => true,
            ],
            [
                'customer_code' => 'CORP-002',
                'name' => 'PT GoTo Gojek Tokopedia',
                'email' => 'finance@goto.com',
                'phone' => '021-50841300',
                'mobile' => '081122223333',
                'address' => 'Pasaraya Blok M Gedung B, Lt. 6',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12160',
                'credit_limit' => 75000000,
                'payment_terms' => 30,
                'is_active' => true,
            ],
            [
                'customer_code' => 'RETL-001',
                'name' => 'Budi Santoso (VIP Retail)',
                'email' => 'budi.s@gmail.com',
                'phone' => null,
                'mobile' => '081299008877',
                'address' => 'Apartemen Medit Gajah Mada',
                'city' => 'Jakarta Pusat',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '10130',
                'credit_limit' => 5000000,
                'payment_terms' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['customer_code' => $customer['customer_code']],
                $customer
            );
        }

        $this->command->info('Sample customers seeded successfully!');
    }
}
