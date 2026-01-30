<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\DeliveryOrder;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\DeliveryOrderResource\Pages;

class DeliveryOrderResource extends Resource
{
    protected static ?string $model = DeliveryOrder::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Information')
                ->schema([
                    Forms\Components\TextInput::make('do_number')
                        ->label('DO Number')
                        ->default('DO-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(2))))
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('sales_order_id')
                        ->relationship('salesOrder', 'order_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('customer_name')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'ready_to_ship' => 'Ready to Ship',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\Textarea::make('shipping_address')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('do_number')
                ->label('DO No.')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('salesOrder.order_number')
                ->label('Sales Order')
                ->searchable(),
            Tables\Columns\TextColumn::make('customer_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'draft' => 'gray',
                    'ready_to_ship' => 'info',
                    'shipped' => 'warning',
                    'delivered' => 'success',
                    'cancelled' => 'danger',
                }),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'ready_to_ship' => 'Ready to Ship',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryOrders::route('/'),
            'create' => Pages\CreateDeliveryOrder::route('/create'),
            'edit' => Pages\EditDeliveryOrder::route('/{record}/edit'),
        ];
    }
}
