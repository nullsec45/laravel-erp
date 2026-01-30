<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Filament\Resources\SalesOrderResource\Pages;
use Filament\Forms\Components\Repeater;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->default(fn () => 'SO-' . date('Ymd') . '-' . str_pad(SalesOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->label('Order Number'),
                        
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Customer'),
                        
                        Forms\Components\Select::make('quotation_id')
                            ->relationship('quotation', 'quotation_number')
                            ->searchable()
                            ->preload()
                            ->label('From Quotation')
                            ->helperText('Optional: Link to original quotation'),
                        
                        Forms\Components\DatePicker::make('order_date')
                            ->required()
                            ->default(now())
                            ->label('Order Date'),
                        
                        Forms\Components\DatePicker::make('delivery_date')
                            ->label('Expected Delivery Date'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending')
                            ->label('Status'),
                        
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'partial' => 'Partially Paid',
                                'paid' => 'Paid',
                            ])
                            ->required()
                            ->default('unpaid')
                            ->label('Payment Status'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = \Modules\Inventory\Models\Product::find($state);
                                            if ($product) {
                                                $set('description', $product->name);
                                                $set('unit_price', $product->selling_price);
                                            }
                                        }
                                    })
                                    ->label('Product'),
                                
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->rows(2)
                                    ->label('Description'),
                                
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->label('Quantity'),
                                
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->label('Unit Price'),
                                
                                Forms\Components\TextInput::make('discount_percentage')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->label('Discount %'),
                                
                                Forms\Components\TextInput::make('tax_percentage')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->label('Tax %'),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Order Item')
                            ->collapsible(),
                    ]),
                
                Forms\Components\Section::make('Shipping & Additional Info')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_cost')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->label('Shipping Cost'),
                        
                        Forms\Components\Textarea::make('shipping_address')
                            ->rows(3)
                            ->label('Shipping Address'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
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
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->label('Order #'),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer'),
                
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable()
                    ->label('Order Date'),
                
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->sortable()
                    ->label('Delivery Date')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->label('Total'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'confirmed',
                        'primary' => 'processing',
                        'info' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->label('Status'),
                
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ])
                    ->label('Payment'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Status'),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partial' => 'Partially Paid',
                        'paid' => 'Paid',
                    ])
                    ->label('Payment Status'),
                
                Tables\Filters\SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('convert_to_invoice')
                    ->label('Create Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => !$record->invoice && $record->status !== 'cancelled')
                    ->action(function ($record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Coming Soon')
                            ->body('Invoice generation will be available soon')
                            ->info()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'view' => Pages\ViewSalesOrder::route('/{record}'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
