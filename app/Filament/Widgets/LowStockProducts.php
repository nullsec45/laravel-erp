<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Inventory\Models\Product;
use Illuminate\Support\Facades\DB;

class LowStockProducts extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->select(
                        'products.id',
                        'products.sku',
                        'products.name',
                        'products.minimum_stock',
                        'products.category_id',
                        'products.unit_id',
                        'products.is_active',
                        DB::raw('SUM(stock_levels.quantity) as total_stock')
                    )
                    ->join('stock_levels', 'products.id', '=', 'stock_levels.product_id')
                    ->whereRaw('stock_levels.quantity <= products.minimum_stock')
                    ->groupBy(
                        'products.id',
                        'products.sku',
                        'products.name',
                        'products.minimum_stock',
                        'products.category_id',
                        'products.unit_id',
                        'products.is_active'
                    )
                    ->orderByRaw('SUM(stock_levels.quantity) / NULLIF(products.minimum_stock, 0)')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('stockLevels.quantity')
                    ->label('Current Stock')
                    ->getStateUsing(fn ($record) => $record->stockLevels->sum('quantity'))
                    ->badge()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('minimum_stock')
                    ->label('Min Level')
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit'),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->heading('Low Stock Alert');
    }
}
