<?php

namespace Modules\Logistic\Filament\Resources\VehicleResource\Pages;

use Modules\Logistic\Filament\Resources\VehicleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
