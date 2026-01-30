<?php

namespace Modules\Purchasing\Resources;

use Modules\Purchasing\Resources\PurchaseOrderResource\Pages;
use Modules\Purchasing\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Purchase Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('po_number')
                            ->label('PO Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated')
                            ->helperText('Automatically generated on save'),

                        Forms\Components\Select::make('vendor_id')
                            ->label('Vendor')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('email')
                                    ->email(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
                            ]),

                        Forms\Components\DatePicker::make('order_date')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'partial' => 'Partially Received',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('payment_terms')
                            ->maxLength(100)
                            ->placeholder('e.g., Net 30'),
                    ])
                    ->columns(2),

                Section::make('Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if ($state) {
                                            $product = \Modules\Inventory\Models\Product::find($state);
                                            if ($product) {
                                                $set('description', $product->description);
                                                $set('unit_price', $product->cost_price ?? 0);
                                            }
                                        }
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('description')
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateItemTotal($set, $get)),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateItemTotal($set, $get)),

                                Forms\Components\TextInput::make('discount_rate')
                                    ->label('Discount %')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateItemTotal($set, $get)),

                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('Tax %')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateItemTotal($set, $get)),

                                Forms\Components\Placeholder::make('total_price')
                                    ->label('Total')
                                    ->content(fn (Get $get): string => '$' . number_format($get('total_price') ?? 0, 2)),
                            ])
                            ->columns(9)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->addActionLabel('Add Item')
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateOrderTotals($set, $get))
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action->after(fn (Set $set, Get $get) => self::calculateOrderTotals($set, $get))
                            ),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateOrderTotals($set, $get)),

                        Forms\Components\Placeholder::make('subtotal_display')
                            ->label('Subtotal')
                            ->content(fn (Get $get): string => '$' . number_format($get('subtotal') ?? 0, 2)),

                        Forms\Components\Placeholder::make('tax_amount_display')
                            ->label('Tax Amount')
                            ->content(fn (Get $get): string => '$' . number_format($get('tax_amount') ?? 0, 2)),

                        Forms\Components\Placeholder::make('discount_amount_display')
                            ->label('Discount Amount')
                            ->content(fn (Get $get): string => '$' . number_format($get('discount_amount') ?? 0, 2)),

                        Forms\Components\Placeholder::make('total_amount_display')
                            ->label('Total Amount')
                            ->content(fn (Get $get): string => '$' . number_format($get('total_amount') ?? 0, 2))
                            ->extraAttributes(['class' => 'font-bold text-lg']),

                        Forms\Components\Hidden::make('subtotal'),
                        Forms\Components\Hidden::make('tax_amount'),
                        Forms\Components\Hidden::make('discount_amount'),
                        Forms\Components\Hidden::make('total_amount'),
                        Forms\Components\Hidden::make('outstanding_amount'),
                    ])
                    ->columns(3),
            ]);
    }

    protected static function calculateItemTotal(Set $set, Get $get): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitPrice = floatval($get('unit_price') ?? 0);
        $discountRate = floatval($get('discount_rate') ?? 0);
        $taxRate = floatval($get('tax_rate') ?? 0);

        $subtotal = $quantity * $unitPrice;
        $discount = $subtotal * ($discountRate / 100);
        $taxable = $subtotal - $discount;
        $tax = $taxable * ($taxRate / 100);
        $total = $taxable + $tax;

        $set('total_price', number_format($total, 2, '.', ''));
    }

    protected static function calculateOrderTotals(Set $set, Get $get): void
    {
        $items = collect($get('items') ?? []);
        $shippingCost = floatval($get('shipping_cost') ?? 0);

        $itemsSubtotal = $items->sum(function ($item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            return $quantity * $unitPrice;
        });

        $totalDiscount = $items->sum(function ($item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $discountRate = floatval($item['discount_rate'] ?? 0);
            $subtotal = $quantity * $unitPrice;
            return $subtotal * ($discountRate / 100);
        });

        $totalTax = $items->sum(function ($item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $discountRate = floatval($item['discount_rate'] ?? 0);
            $taxRate = floatval($item['tax_rate'] ?? 0);
            $subtotal = $quantity * $unitPrice;
            $discount = $subtotal * ($discountRate / 100);
            $taxable = $subtotal - $discount;
            return $taxable * ($taxRate / 100);
        });

        $total = $itemsSubtotal - $totalDiscount + $totalTax + $shippingCost;

        $set('subtotal', number_format($itemsSubtotal, 2, '.', ''));
        $set('tax_amount', number_format($totalTax, 2, '.', ''));
        $set('discount_amount', number_format($totalDiscount, 2, '.', ''));
        $set('total_amount', number_format($total, 2, '.', ''));
        $set('outstanding_amount', number_format($total, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->label('PO #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Outstanding')
                    ->money('USD')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'info',
                        'partial' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'partial' => 'Partial',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'pending')
                    ->action(function (PurchaseOrder $record) {
                        $record->update(['status' => 'approved']);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('po_number', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['vendor', 'user', 'items.product']);
    }
}
