<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        $roles = Role::where('name', '!=', 'super_admin')->get();

        foreach ($roles as $role) {
            for ($i = 1; $i <= 3; $i++) {
                $email = str_replace(' ', '_', strtolower($role->name)) . $i . '@erp.com';

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => Str::title(str_replace('_', ' ', $role->name)) . " User " . $i,
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                );

                $user->assignRole($role->name);
            }
        }

        $this->command->info('User Seeder berhasil dijalankan:');
        $this->command->info('- 1 Super Admin (admin@example.com)');
        $this->command->info('- ' . ($roles->count() * 3) . ' Staff Users (3 user per role)');
    }
}
