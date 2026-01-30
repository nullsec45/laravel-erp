<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Purchasing\Models\Vendor;

class PurchasingSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Telkomsel (PT Telekomunikasi Selular)',
                'contact_person' => 'Budi Setiawan',
                'email' => 'corporate@telkomsel.co.id',
                'phone' => '021-1234567',
                'mobile' => '081100001111',
                'website' => 'https://telkomsel.com',
                'tax_id' => '01.234.567.8-091.000',
                'payment_terms' => 'Net 30',
                'credit_limit' => 500000000.00,
                'address' => 'Telkomsel Smart Office, Jl. Jend. Gatot Subroto',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12710',
                'is_active' => true,
            ],
            [
                'name' => 'Huawei Tech Investment',
                'contact_person' => 'Chen Wei',
                'email' => 'sales@huawei.com',
                'phone' => '021-5556667',
                'mobile' => '081288889999',
                'website' => 'https://huawei.com',
                'tax_id' => '02.444.555.6-001.000',
                'payment_terms' => 'Net 45',
                'credit_limit' => 1000000000.00,
                'address' => 'Wisma Mulia Lt. 35, Jl. Jend. Gatot Subroto',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12710',
                'is_active' => true,
            ],
            [
                'name' => 'Giesecke+Devrient (eSIM Provider)',
                'contact_person' => 'Sarah Schmidt',
                'email' => 'support@gi-de.com',
                'phone' => '+49-89-4119-0',
                'mobile' => null,
                'website' => 'https://www.gi-de.com',
                'tax_id' => 'DE-123456789',
                'payment_terms' => 'Net 60',
                'credit_limit' => 250000000.00,
                'address' => 'Prinzregentenstrasse 159',
                'city' => 'Munich',
                'state' => 'Bavaria',
                'country' => 'Germany',
                'postal_code' => '81677',
                'is_active' => true,
            ],
            [
                'name' => 'Indosat Ooredoo Hutchison',
                'contact_person' => 'Andi Wijaya',
                'email' => 'business@ioh.co.id',
                'phone' => '021-30003000',
                'mobile' => '085511112222',
                'website' => 'https://ioh.co.id',
                'tax_id' => '01.000.111.2-092.000',
                'payment_terms' => 'Net 30',
                'credit_limit' => 300000000.00,
                'address' => 'Jl. Medan Merdeka Barat No. 21',
                'city' => 'Jakarta Pusat',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '10110',
                'is_active' => true,
            ],
            [
                'name' => 'ZTE Indonesia',
                'contact_person' => 'Li Ming',
                'email' => 'id.sales@zte.com.cn',
                'phone' => '021-29944000',
                'mobile' => null,
                'website' => 'https://zte.com.cn',
                'tax_id' => '02.999.888.7-002.000',
                'payment_terms' => 'Net 15',
                'credit_limit' => 750000000.00,
                'address' => 'The East Tower, Mega Kuningan',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12950',
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::updateOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );
        }

        $this->command->info('✓ Purchasing module seeded');
    }
}
