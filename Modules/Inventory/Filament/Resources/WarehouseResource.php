<?php

namespace Modules\Inventory\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Filament\Resources\WarehouseResource\Pages;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 5;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Warehouse Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->label('Warehouse Name'),
                        
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->label('Warehouse Code')
                            ->helperText('Unique identifier for this warehouse'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->maxLength(500)
                            ->label('Address'),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100)
                            ->label('City'),
                        
                        Forms\Components\TextInput::make('state')
                            ->maxLength(100)
                            ->label('State/Province'),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100)
                            ->default('Indonesia')
                            ->label('Country'),
                        
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20)
                            ->label('Postal Code'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->label('Phone'),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(100)
                            ->label('Email'),
                        
                        Forms\Components\TextInput::make('manager_name')
                            ->maxLength(100)
                            ->label('Manager Name'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Description'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Code')
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Warehouse Name'),
                
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->label('City'),
                
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->label('State')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('manager_name')
                    ->searchable()
                    ->label('Manager')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Phone')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('stockLevels_count')
                    ->counts('stockLevels')
                    ->label('Products Stored')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
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
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
                
                Tables\Filters\Filter::make('city')
                    ->form([
                        Forms\Components\TextInput::make('city')
                            ->label('City'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['city'],
                            fn ($q) => $q->where('city', 'like', '%' . $data['city'] . '%')
                        );
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
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'view' => Pages\ViewWarehouse::route('/{record}'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
