<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Sales\Models\Payment;
use Modules\Sales\Filament\Resources\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 5;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->required()
                            ->default(fn () => 'PAY-' . date('Ymd') . '-' . str_pad(Payment::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->label('Payment Number'),
                        
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Clear invoice when customer changes
                                $set('invoice_id', null);
                            })
                            ->label('Customer'),
                        
                        Forms\Components\Select::make('invoice_id')
                            ->relationship('invoice', 'invoice_number', function ($query, Forms\Get $get) {
                                $customerId = $get('customer_id');
                                if ($customerId) {
                                    return $query->where('customer_id', $customerId)
                                        ->where('status', '!=', 'cancelled');
                                }
                                return $query;
                            })
                            ->searchable()
                            ->preload()
                            ->label('Invoice')
                            ->helperText('Select the invoice this payment is for'),
                        
                        Forms\Components\DatePicker::make('payment_date')
                            ->required()
                            ->default(now())
                            ->label('Payment Date'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->minValue(0.01)
                            ->label('Amount'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'credit_card' => 'Credit Card',
                                'check' => 'Check',
                            ])
                            ->required()
                            ->default('bank_transfer')
                            ->label('Payment Method'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(100)
                            ->label('Reference Number')
                            ->helperText('Transaction ID, Check number, etc.'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Notes'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->searchable()
                    ->sortable()
                    ->label('Payment #'),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Customer'),
                
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->searchable()
                    ->sortable()
                    ->label('Invoice #')
                    ->default('N/A'),
                
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable()
                    ->label('Payment Date'),
                
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable()
                    ->label('Amount'),
                
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->colors([
                        'success' => 'cash',
                        'primary' => 'bank_transfer',
                        'warning' => 'credit_card',
                        'secondary' => 'check',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'check' => 'Check',
                        default => $state,
                    })
                    ->label('Method'),
                
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->label('Reference')
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
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'check' => 'Check',
                    ])
                    ->label('Payment Method'),
                
                Tables\Filters\SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
                
                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('payment_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('payment_date', '<=', $data['until']));
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
