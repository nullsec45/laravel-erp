<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\Shipment;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\ShipmentResource\Pages;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Shipment Details')
                ->schema([
                    Forms\Components\TextInput::make('shipment_number')
                        ->default('SHP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))))
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('delivery_order_id')
                        ->relationship('deliveryOrder', 'do_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Resource Assignment')
                ->schema([
                    Forms\Components\Select::make('driver_id')
                        ->relationship('driver', 'id')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->employee->name}")
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('vehicle_id')
                        ->relationship('vehicle', 'plate_number')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('route_id')
                        ->relationship('route', 'name')
                        ->searchable()
                        ->preload(),
                ])->columns(3),

            Forms\Components\Section::make('Schedule')
                ->schema([
                    Forms\Components\DateTimePicker::make('departure_time'),
                    Forms\Components\DateTimePicker::make('arrival_time_estimate'),
                    Forms\Components\DateTimePicker::make('arrival_time_actual'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('shipment_number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('deliveryOrder.do_number')
                ->label('DO No.')
                ->searchable(),
            Tables\Columns\TextColumn::make('driver.employee.name')
                ->label('Driver')
                ->placeholder('No Driver Assigned'),
            Tables\Columns\TextColumn::make('vehicle.plate_number')
                ->label('Vehicle'),
            Tables\Columns\TextColumn::make('departure_time')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('arrival_time_estimate')
                ->label('ETA')
                ->dateTime()
                ->sortable(),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'plate_number')
                    ->label('Filter by Vehicle'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
