<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Models\{Department, Position};
use Illuminate\Support\Str;

class MasterDataHRSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Departemen
        $departments = [
            'it'        => ['name' => 'IT & Infrastructure', 'desc' => 'Mengelola sistem ERP dan infrastruktur Mifi.'],
            'inventory' => ['name' => 'Inventory & Warehouse', 'desc' => 'Stok modem, SIM Card, dan fisik barang.'],
            'logistic'  => ['name' => 'Logistic & Delivery', 'desc' => 'Pengiriman modem ke customer/bandara.'],
            'sales'     => ['name' => 'Sales & Marketing', 'desc' => 'Penjualan B2B dan Agent.'],
            'cs'        => ['name' => 'Customer Service', 'desc' => 'Troubleshooting penyewa luar negeri.'],
            'hr'        => ['name' => 'Human Resources', 'desc' => 'Administrasi karyawan.'],
        ];

        $deptIds = [];
        foreach ($departments as $key => $dept) {
            $createdDept = Department::updateOrCreate(
                ['code' => Str::slug($dept['name'])], // Contoh: 'it-infrastructure'
                [
                    'name' => $dept['name'],
                    'description' => $dept['desc']
                ]
            );
            // Simpan ID ke dalam array untuk digunakan di bawah
            $deptIds[$key] = $createdDept->id;
        }

        // 2. Definisi Posisi menggunakan ID yang baru saja dibuat
        $positions = [
            // IT
            ['name' => 'IT Manager', 'department_id' => $deptIds['it']],
            ['name' => 'Software Engineer', 'department_id' => $deptIds['it']],

            // Inventory
            ['name' => 'Warehouse Supervisor', 'department_id' => $deptIds['inventory']],
            ['name' => 'Inventory Clerk', 'department_id' => $deptIds['inventory']],

            // Logistic
            ['name' => 'Logistic Coordinator', 'department_id' => $deptIds['logistic']],
            ['name' => 'Field Courier', 'department_id' => $deptIds['logistic']],
            ['name' => 'Fleet Driver', 'department_id' => $deptIds['logistic']],

            // Sales
            ['name' => 'Account Manager', 'department_id' => $deptIds['sales']],
            ['name' => 'Sales Executive', 'department_id' => $deptIds['sales']],

            // Customer Service
            ['name' => 'Technical Support Specialist', 'department_id' => $deptIds['cs']],
        ];

        foreach ($positions as $pos) {
            Position::updateOrCreate(
                ['name' => $pos['name']],
                [
                    'department_id' => $pos['department_id'],
                ]
            );
        }

        $this->command->info('✓ HR Departments and Positions seeded successfully!');
    }
}
