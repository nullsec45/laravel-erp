<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\Driver;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\DriverResource\Pages;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Driver Information')
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->relationship('employee', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('license_number')
                        ->required()
                        ->label('SIM Number'),
                    Forms\Components\Select::make('license_type')
                        ->options([
                            'A' => 'SIM A',
                            'B1' => 'SIM B1',
                            'B2' => 'SIM B2',
                            'C' => 'SIM C',
                        ])
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'available' => 'Available',
                            'on_duty' => 'On Duty',
                            'off' => 'Off',
                        ])
                        ->default('available')
                        ->required(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('employee.name')
                ->label('Driver Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('license_number')
                ->label('SIM No.')
                ->searchable(),
            Tables\Columns\TextColumn::make('license_type')
                ->badge(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'available' => 'success',
                    'on_duty' => 'warning',
                    'off' => 'danger',
                }),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'on_duty' => 'On Duty',
                        'off' => 'Off',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
