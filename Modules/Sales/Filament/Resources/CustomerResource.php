<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Sales\Models\Customer;
use Modules\Sales\Filament\Resources\CustomerResource\Pages;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'CUST-' . str_pad(Customer::count() + 1, 5, '0', STR_PAD_LEFT))
                            ->label('Customer Code'),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Customer Name'),
                        
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255)
                            ->label('Company Name'),
                        
                        Forms\Components\TextInput::make('tax_number')
                            ->maxLength(255)
                            ->label('Tax Number / VAT'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Phone'),
                        
                        Forms\Components\TextInput::make('mobile')
                            ->tel()
                            ->maxLength(255)
                            ->label('Mobile'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->maxLength(1000)
                            ->label('Street Address'),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255)
                            ->label('City'),
                        
                        Forms\Components\TextInput::make('state')
                            ->maxLength(255)
                            ->label('State / Province'),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(255)
                            ->label('Country'),
                        
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(255)
                            ->label('Postal Code'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Business Terms')
                    ->schema([
                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->label('Credit Limit'),
                        
                        Forms\Components\TextInput::make('payment_terms')
                            ->numeric()
                            ->suffix('days')
                            ->default(30)
                            ->label('Payment Terms'),
                        
                        Forms\Components\TextInput::make('discount_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->label('Default Discount'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Additional Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Notes'),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_code')
                    ->searchable()
                    ->sortable()
                    ->label('Code'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label('Company'),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Phone'),
                
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable()
                    ->label('City'),
                
                Tables\Columns\TextColumn::make('credit_limit')
                    ->money('USD')
                    ->sortable()
                    ->toggleable()
                    ->label('Credit Limit'),
                
                Tables\Columns\TextColumn::make('total_purchases')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Total Purchases'),
                
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->money('USD')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->toggleable()
                    ->label('Outstanding'),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All customers')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                
                Tables\Filters\Filter::make('has_outstanding')
                    ->label('Has Outstanding Balance')
                    ->query(fn ($query) => $query->withOutstanding()),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn ($q) => $q->whereDate('created_at', '<=', $data['created_until']));
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
