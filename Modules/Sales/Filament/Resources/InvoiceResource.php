<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Sales\Models\Invoice;
use Modules\Sales\Filament\Resources\InvoiceResource\Pages;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Information')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->default(fn () => 'INV-' . date('Ymd') . '-' . str_pad(Invoice::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->label('Invoice Number'),
                        
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Customer'),
                        
                        Forms\Components\Select::make('sales_order_id')
                            ->relationship('salesOrder', 'order_number')
                            ->searchable()
                            ->preload()
                            ->label('From Sales Order')
                            ->helperText('Optional: Link to sales order'),
                        
                        Forms\Components\DatePicker::make('invoice_date')
                            ->required()
                            ->default(now())
                            ->label('Invoice Date'),
                        
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30))
                            ->label('Due Date'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft')
                            ->label('Status'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Financial Details')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->label('Subtotal'),
                        
                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->label('Tax Amount'),
                        
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->label('Discount Amount'),
                        
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->label('Total Amount'),
                        
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->label('Paid Amount')
                            ->helperText('Updated automatically when payments are recorded'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Notes'),
                        
                        Forms\Components\Textarea::make('terms_conditions')
                            ->rows(3)
                            ->maxLength(2000)
                            ->default('Payment is due within 30 days. Late payments may incur additional charges.')
                            ->label('Terms & Conditions'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->label('Invoice #'),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer'),
                
                Tables\Columns\TextColumn::make('invoice_date')
                    ->date()
                    ->sortable()
                    ->label('Invoice Date'),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->label('Due Date'),
                
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->label('Total'),
                
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('USD')
                    ->sortable()
                    ->label('Paid'),
                
                Tables\Columns\TextColumn::make('balance')
                    ->money('USD')
                    ->label('Balance')
                    ->getStateUsing(fn ($record) => $record->total - $record->paid_amount)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'sent',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'cancelled',
                    ])
                    ->label('Status'),
                
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
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Status'),
                
                Tables\Filters\SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
                
                Tables\Filters\Filter::make('overdue')
                    ->query(fn ($query) => $query->where('due_date', '<', now())->where('status', '!=', 'paid'))
                    ->label('Overdue Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('record_payment')
                    ->label('Record Payment')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid' && $record->status !== 'cancelled')
                    ->action(function ($record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Coming Soon')
                            ->body('Payment recording will be available soon')
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
