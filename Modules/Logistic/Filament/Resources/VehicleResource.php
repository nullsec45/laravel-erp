<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\Vehicle;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\VehicleResource\Pages;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vehicle Information')
                ->schema([
                    Forms\Components\TextInput::make('plate_number')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('B 1234 ABC'),
                    Forms\Components\TextInput::make('model')
                        ->required()
                        ->placeholder('Isuzu Giga / Mitsubishi Fuso'),
                    Forms\Components\TextInput::make('capacity_kg')
                        ->numeric()
                        ->default(0)
                        ->suffix('kg'),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('plate_number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('model')
                ->searchable(),
            Tables\Columns\TextColumn::make('capacity_kg')
                ->numeric()
                ->sortable()
                ->suffix(' kg'),
            Tables\Columns\IconColumn::make('is_active')
                ->label('Status')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
