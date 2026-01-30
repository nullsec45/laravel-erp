<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanySetting;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'company_name'        => 'Java Mifi (PT Internasional Jejaring Indonesia)',
            'company_email'       => 'support@javamifi.com',
            'company_phone'       => '021-50880000',
            'company_address'     => 'Menara Caraka Lt. 2, Mega Kuningan, Jakarta Selatan',
            'company_logo'        => 'logos/javamifi-logo.png',
            'company_favicon'     => 'logos/favicon.ico',
            'currency'            => 'IDR',
            'timezone'            => 'Asia/Jakarta',
            'date_format'         => 'd/m/Y',
            'time_format'         => 'H:i',
            'fiscal_year_start'   => 'January',
            'language'            => 'id',
            'theme'               => 'light',
            'tax_number'          => '01.234.567.8-092.000',
            'registration_number' => 'AHU-0012345.AH.01.01.2024',
        ];


        CompanySetting::updateOrCreate(
            ['company_email' => $settings['company_email']],
            $settings
        );

        $this->command->info('✓ Company Settings seeded successfully for Java Mifi!');
    }
}
