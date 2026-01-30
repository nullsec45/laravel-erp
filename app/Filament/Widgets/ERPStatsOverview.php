<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\Invoice;
use Modules\Inventory\Models\Product;
use Modules\Finance\Models\Account;
use Modules\HR\Models\Employee;
use Illuminate\Support\Facades\DB;

class ERPStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate total sales (current month)
        $totalSales = SalesOrder::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Calculate outstanding invoices
        $outstandingInvoices = Invoice::where('status', '!=', 'paid')
            ->sum(DB::raw('total - paid_amount'));

        // Calculate total stock value
        $stockValue = Product::join('stock_levels', 'products.id', '=', 'stock_levels.product_id')
            ->sum(DB::raw('products.selling_price * stock_levels.quantity'));



        // Count active employees
        $employeeCount = Employee::where('status', 'active')->count();

        // Count low stock products
        $lowStockCount = Product::join('stock_levels', 'products.id', '=', 'stock_levels.product_id')
            ->whereRaw('stock_levels.quantity <= products.minimum_stock')
            ->distinct('products.id')
            ->count('products.id');

        return [
            Stat::make('Total Sales (This Month)', 'Rp ' . number_format($totalSales, 0, ',', '.'))
                ->description('Revenue from sales orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Outstanding Invoices', 'Rp ' . number_format($outstandingInvoices, 0, ',', '.'))
                ->description('Unpaid customer invoices')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Inventory Value', 'Rp ' . number_format($stockValue, 0, ',', '.'))
                ->description('Total stock value')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),



            Stat::make('Active Employees', $employeeCount)
                ->description('Total active staff')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Low Stock Items', $lowStockCount)
                ->description('Products below minimum level')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
