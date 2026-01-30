<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Sales
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'view_quotations',
            'create_quotations',
            'edit_quotations',
            'delete_quotations',
            'view_sales_orders',
            'create_sales_orders',
            'edit_sales_orders',
            'delete_sales_orders',
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'view_payments',
            'create_payments',
            'edit_payments',
            'delete_payments',

            // Purchasing
            'view_vendors',
            'create_vendors',
            'edit_vendors',
            'delete_vendors',
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',

            // Inventory
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_brands',
            'create_brands',
            'edit_brands',
            'delete_brands',
            'view_units',
            'create_units',
            'edit_units',
            'delete_units',
            'view_warehouses',
            'create_warehouses',
            'edit_warehouses',
            'delete_warehouses',
            'view_stock_movements',
            'create_stock_movements',

            // HR
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'view_positions',
            'create_positions',
            'edit_positions',
            'delete_positions',

            // Logistic
            'view_vehicles',
            'create_vehicles',
            'edit_vehicles',
            'delete_vehicles',
            'view_delivery_orders',
            'create_delivery_orders',
            'edit_delivery_orders',
            'delete_delivery_orders',
            'view_routes',
            'create_routes',
            'edit_routes',
            'delete_routes',
            'view_drivers',
            'create_drivers',
            'edit_drivers',
            'delete_drivers',
            'view_shipments',
            'create_shipments',
            'edit_shipments',
            'delete_shipments',
            'view_tracking',
            'create_tracking',

            // System & User Management
            'view_company_settings',
            'edit_company_settings',
            'view_activity_logs',
            'manage_users',
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }



        // Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Logistic Manager
        $logisticManager = Role::firstOrCreate(['name' => 'logistic_manager']);
        $logisticManager->syncPermissions(
            Permission::where('name', 'like', '%vehicles%')
                ->orWhere('name', 'like', '%delivery%')
                ->orWhere('name', 'like', '%routes%')
                ->orWhere('name', 'like', '%drivers%')
                ->orWhere('name', 'like', '%shipments%')
                ->orWhere('name', 'like', '%tracking%')
                ->orWhere('name', 'like', 'view_products')
                ->orWhere('name', 'like', 'view_warehouses')
                ->get()
        );

        // Logistic Staff
        $logisticStaff = Role::firstOrCreate(['name' => 'logistic_staff']);
        $logisticStaff->syncPermissions([
            'view_delivery_orders',
            'create_delivery_orders',
            'edit_delivery_orders',
            'view_shipments',
            'create_shipments',
            'edit_shipments',
            'view_tracking',
            'create_tracking',
            'view_vehicles',
            'view_routes',
            'view_drivers'
        ]);

        // Warehouse Staff (Inventory Fokus)
        $warehouseStaff = Role::firstOrCreate(['name' => 'warehouse_staff']);
        $warehouseStaff->syncPermissions(
            Permission::where('name', 'like', '%products%')
                ->orWhere('name', 'like', '%categories%')
                ->orWhere('name', 'like', '%brands%')
                ->orWhere('name', 'like', '%stock%')
                ->orWhere('name', 'like', '%warehouses%')
                ->get()
        );

        // Sales Staff
        $salesStaff = Role::firstOrCreate(['name' => 'sales_staff']);
        $salesStaff->syncPermissions(
            Permission::where('name', 'like', '%customers%')
                ->orWhere('name', 'like', '%quotations%')
                ->orWhere('name', 'like', '%sales_orders%')
                ->orWhere('name', 'like', '%invoices%')
                ->get()
        );

        // HR Staff
        $hrStaff = Role::firstOrCreate(['name' => 'hr_staff']);
        $hrStaff->syncPermissions(
            Permission::where('name', 'like', '%employees%')
                ->orWhere('name', 'like', '%departments%')
                ->orWhere('name', 'like', '%positions%')
                ->get()
        );

        $this->command->info('Roles and Logistics permissions seeded successfully!');
    }
}
