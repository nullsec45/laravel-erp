<?php

namespace Modules\Logistic\Filament\Resources\RoutePlanningResource\Pages;

use Modules\Logistic\Filament\Resources\RoutePlanningResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditRoutePlanning extends EditRecord
{
    protected static string $resource = RoutePlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
