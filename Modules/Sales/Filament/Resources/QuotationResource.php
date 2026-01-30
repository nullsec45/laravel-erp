<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Sales\Models\Quotation;
use Modules\Sales\Filament\Resources\QuotationResource\Pages;
use Filament\Forms\Components\Repeater;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Quotation Details')
                    ->schema([
                        Forms\Components\TextInput::make('quotation_number')
                            ->required()
                            ->default(fn () => 'QT-' . date('Ymd') . '-' . str_pad(Quotation::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->label('Quotation Number'),
                        
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('email')->email(),
                                Forms\Components\TextInput::make('phone'),
                            ])
                            ->label('Customer'),
                        
                        Forms\Components\DatePicker::make('quotation_date')
                            ->required()
                            ->default(now())
                            ->label('Quotation Date'),
                        
                        Forms\Components\DatePicker::make('valid_until')
                            ->required()
                            ->default(now()->addDays(30))
                            ->label('Valid Until'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Status'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Line Items')
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
                            ->addActionLabel('Add Line Item')
                            ->collapsible(),
                    ]),
                
                Forms\Components\Section::make('Totals & Terms')
                    ->schema([
                        Forms\Components\TextInput::make('tax_percentage')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->label('Overall Tax %'),
                        
                        Forms\Components\TextInput::make('discount_percentage')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->label('Overall Discount %'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->maxLength(1000)
                            ->label('Notes'),
                        
                        Forms\Components\Textarea::make('terms_conditions')
                            ->rows(3)
                            ->maxLength(2000)
                            ->default('1. Payment due within 30 days
2. Prices are valid for the period specified
3. All prices exclude shipping unless stated otherwise')
                            ->label('Terms & Conditions'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quotation_number')
                    ->searchable()
                    ->sortable()
                    ->label('Quotation #'),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer'),
                
                Tables\Columns\TextColumn::make('quotation_date')
                    ->date()
                    ->sortable()
                    ->label('Date'),
                
                Tables\Columns\TextColumn::make('valid_until')
                    ->date()
                    ->sortable()
                    ->label('Valid Until'),
                
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->label('Total'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'warning' => 'expired',
                    ])
                    ->label('Status'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                    ])
                    ->label('Status'),
                
                Tables\Filters\SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
                
                Tables\Filters\Filter::make('quotation_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('quotation_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('quotation_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('convert_to_order')
                    ->label('Convert to Order')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'accepted' && !$record->salesOrder)
                    ->action(function ($record) {
                        // This will be implemented to convert quotation to sales order
                        // For now, just show notification
                        \Filament\Notifications\Notification::make()
                            ->title('Coming Soon')
                            ->body('Sales Order conversion will be available soon')
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'view' => Pages\ViewQuotation::route('/{record}'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
