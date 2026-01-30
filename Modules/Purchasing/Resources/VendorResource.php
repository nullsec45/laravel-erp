<?php

namespace Modules\Purchasing\Resources;

use Modules\Purchasing\Resources\VendorResource\Pages;
use Modules\Purchasing\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Vendor Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated')
                            ->helperText('Automatically generated on save'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('mobile')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(100)
                            ->placeholder('https://'),

                        Forms\Components\TextInput::make('tax_id')
                            ->label('Tax ID / VAT Number')
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Section::make('Financial Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_terms')
                            ->maxLength(100)
                            ->placeholder('e.g., Net 30, Net 60'),

                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('state')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('country')
                            ->maxLength(100)
                            ->default('USA'),

                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20),
                    ])
                    ->columns(4),

                Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Vendor $record): ?string => $record->contact_person),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_purchases')
                    ->label('Total Purchases')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->label('Outstanding')
                    ->money('USD')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

                SelectFilter::make('city')
                    ->options(function () {
                        return Vendor::query()
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city', 'city')
                            ->toArray();
                    })
                    ->searchable(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes();
    }
}
