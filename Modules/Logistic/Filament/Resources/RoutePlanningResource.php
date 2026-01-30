<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\Route;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\RoutePlanningResource\Pages;

class RoutePlanningResource extends Resource
{
    protected static ?string $model = Route::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Route Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->placeholder('e.g. Jakarta - Bandung Express'),
                    Forms\Components\TextInput::make('zone_code')
                        ->required()
                        ->placeholder('e.g. JKT-BDG-01'),
                    Forms\Components\TextInput::make('origin_city')
                        ->required(),
                    Forms\Components\TextInput::make('destination_city')
                        ->required(),
                    Forms\Components\TextInput::make('distance_km')
                        ->numeric()
                        ->suffix('km')
                        ->label('Distance'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('zone_code')
                ->label('Zone')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('origin_city')
                ->label('From')
                ->searchable(),
            Tables\Columns\TextColumn::make('destination_city')
                ->label('To')
                ->searchable(),
            Tables\Columns\TextColumn::make('distance_km')
                ->label('Distance')
                ->suffix(' km')
                ->sortable(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutePlannings::route('/'),
            'create' => Pages\CreateRoutePlanning::route('/create'),
            'edit' => Pages\EditRoutePlanning::route('/{record}/edit'),
        ];
    }
}
