<?php

namespace Modules\Logistic\Filament\Resources;

use Modules\Logistic\Models\TrackingEvent;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Logistic\Filament\Resources\TrackingEventResource\Pages;

class TrackingEventResource extends Resource
{
    protected static ?string $model = TrackingEvent::class;
    protected static ?string $navigationGroup = 'Logistic';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Details')
                ->schema([
                    Forms\Components\Select::make('shipment_id')
                        ->relationship('shipment', 'shipment_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('status')
                        ->required()
                        ->placeholder('e.g. Arrived at Hub, Out for Delivery'),
                    Forms\Components\DateTimePicker::make('event_time')
                        ->default(now())
                        ->required(),
                    Forms\Components\TextInput::make('location')
                        ->placeholder('e.g. Jakarta Warehouse, Bekasi City'),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('shipment.shipment_number')
                ->label('Shipment No.')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color('info')
                ->searchable(),
            Tables\Columns\TextColumn::make('location')
                ->searchable(),
            Tables\Columns\TextColumn::make('event_time')
                ->dateTime()
                ->sortable(),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('shipment_id')
                    ->relationship('shipment', 'shipment_number')
                    ->label('Filter by Shipment'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('event_time', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrackingEvents::route('/'),
            'create' => Pages\CreateTrackingEvent::route('/create'),
            'edit' => Pages\EditTrackingEvent::route('/{record}/edit'),
        ];
    }
}
