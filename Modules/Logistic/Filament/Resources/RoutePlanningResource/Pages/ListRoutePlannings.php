<?php

namespace Modules\Logistic\Filament\Resources\RoutePlanningResource\Pages;

use Modules\Logistic\Filament\Resources\RoutePlanningResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListRoutePlannings extends ListRecords
{
    protected static string $resource = RoutePlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
