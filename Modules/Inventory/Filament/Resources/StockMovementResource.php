<?php

namespace Modules\Inventory\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Filament\Resources\StockMovementResource\Pages;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Stock Movements';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Movement Information')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'in' => 'Stock In',
                                'out' => 'Stock Out',
                                'transfer' => 'Transfer',
                                'adjustment' => 'Adjustment',
                            ])
                            ->required()
                            ->default('in')
                            ->reactive()
                            ->label('Movement Type'),
                        
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Product'),
                        
                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Warehouse'),
                        
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->label('Quantity'),
                        
                        Forms\Components\DateTimePicker::make('movement_date')
                            ->required()
                            ->default(now())
                            ->label('Movement Date'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Reference Information')
                    ->schema([
                        Forms\Components\Select::make('reference_type')
                            ->options([
                                'purchase_order' => 'Purchase Order',
                                'sales_order' => 'Sales Order',
                                'production' => 'Production',
                                'manual' => 'Manual Adjustment',
                            ])
                            ->label('Reference Type'),
                        
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(100)
                            ->label('Reference Number')
                            ->helperText('PO number, SO number, or other reference'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Notes'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('movement_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'in',
                        'danger' => 'out',
                        'warning' => 'transfer',
                        'secondary' => 'adjustment',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Adjustment',
                        default => $state,
                    })
                    ->label('Type'),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('Product'),
                
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->searchable()
                    ->sortable()
                    ->label('Warehouse'),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->label('Quantity')
                    ->formatStateUsing(function ($state, $record) {
                        $prefix = match($record->type) {
                            'in', 'adjustment' => '+',
                            'out' => '-',
                            default => ''
                        };
                        return $prefix . number_format($state, 2);
                    })
                    ->color(fn ($record) => match($record->type) {
                        'in', 'adjustment' => 'success',
                        'out' => 'danger',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('reference_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : 'N/A')
                    ->label('Reference')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->label('Ref #')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Adjustment',
                    ])
                    ->label('Movement Type'),
                
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Warehouse'),
                
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Product'),
                
                Tables\Filters\Filter::make('movement_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('movement_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('movement_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('movement_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }
}
