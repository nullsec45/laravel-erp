<?php

namespace Modules\Logistic\Filament\Resources\ShipmentResource\Pages;

use Modules\Logistic\Filament\Resources\ShipmentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
