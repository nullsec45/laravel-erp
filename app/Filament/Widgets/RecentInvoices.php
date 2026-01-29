<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Sales\Models\Invoice;
use Illuminate\Support\Facades\DB;

class RecentInvoices extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('status', '!=', 'paid')
                    ->orderBy('due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('salesOrder.customer.name')
                    ->label('Customer')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('IDR')
                    ->getStateUsing(fn ($record) => $record->total - $record->paid_amount)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date < now() ? 'danger' : 'success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'sent',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'cancelled',
                    ]),
            ])
            ->heading('Outstanding Invoices');
    }
}
