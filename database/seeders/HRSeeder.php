<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\HR\Models\{Department, Position, Employee};
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data referensi
        $itDept = Department::where('code', 'it-infrastructure')->first();
        $logDept = Department::where('code', 'logistic-delivery')->first();
        $salesDept = Department::where('code', 'sales-marketing')->first();

        $itManagerPos = Position::where('name', 'IT Manager')->first();
        $courierPos = Position::where('name', 'Field Courier')->first();
        $salesPos = Position::where('name', 'Sales Executive')->first();

        $employees = [
            [
                'employee_id' => 'EMP-2024-001',
                'first_name' => 'Ahmad',
                'last_name' => 'Hidayat',
                'email' => 'ahmad.h@example.com',
                'phone' => '081234567891',
                'date_of_birth' => '1990-05-15',
                'hire_date' => '2023-01-10',
                'position_id' => $itManagerPos?->id,
                'department_id' => $itDept?->id,
                'salary' => 15000000,
                'status' => 'active',
                'address' => 'Jl. Kebon Jeruk No. 10, Jakarta Barat',
                'role' => 'super_admin'
            ],
            [
                'employee_id' => 'EMP-2024-002',
                'first_name' => 'Siti',
                'last_name' => 'Aminah',
                'email' => 'siti.a@example.com',
                'phone' => '081234567892',
                'date_of_birth' => '1995-08-20',
                'hire_date' => '2023-03-15',
                'position_id' => $salesPos?->id,
                'department_id' => $salesDept?->id,
                'salary' => 8000000,
                'status' => 'active',
                'address' => 'Jl. Tebet Raya No. 45, Jakarta Selatan',
                'role' => 'sales_staff'
            ],
            [
                'employee_id' => 'EMP-2024-003',
                'first_name' => 'Budi',
                'last_name' => 'Santoso',
                'email' => 'budi.s@example.com',
                'phone' => '081234567893',
                'date_of_birth' => '1992-11-02',
                'hire_date' => '2023-06-01',
                'position_id' => $courierPos?->id,
                'department_id' => $logDept?->id,
                'salary' => 5500000,
                'status' => 'active',
                'address' => 'Jl. Kalideres No. 12, Jakarta Barat',
                'role' => 'logistic_staff'
            ],
        ];

        foreach ($employees as $data) {
            // 1. Create atau Update Employee
            $employee = Employee::updateOrCreate(
                ['employee_id' => $data['employee_id']],
                collect($data)->except('role')->toArray()
            );

            // 2. Otomatis buatkan User Account agar bisa login ke ERP
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // 3. Assign Role Spatie sesuai kebutuhan modul
            $user->syncRoles([$data['role']]);
        }

        $this->command->info('✓ ' . count($employees) . ' Employees & User Accounts created successfully!');
    }
}
