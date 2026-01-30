<?php

namespace Modules\Logistic\Filament\Resources\DriverResource\Pages;

use Modules\Logistic\Filament\Resources\DriverResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
