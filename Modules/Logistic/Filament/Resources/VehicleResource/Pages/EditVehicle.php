<?php

namespace Modules\Logistic\Filament\Resources\VehicleResource\Pages;

use Modules\Logistic\Filament\Resources\VehicleResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
