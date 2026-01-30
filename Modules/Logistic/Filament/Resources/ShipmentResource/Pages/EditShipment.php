<?php

namespace Modules\Logistic\Filament\Resources\ShipmentResource\Pages;

use Modules\Logistic\Filament\Resources\ShipmentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
