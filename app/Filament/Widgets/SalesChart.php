<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Sales\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales Revenue';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get sales data for last 12 months
        $data = SalesOrder::where('created_at', '>=', now()->subMonths(12))
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        // Fill last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            
            $monthData = $data->firstWhere('month', $month);
            $values[] = $monthData ? $monthData->total : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales Revenue',
                    'data' => $values,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
