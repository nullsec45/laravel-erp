<?php

namespace Modules\Logistic\Filament\Resources\TrackingEventResource\Pages;

use Modules\Logistic\Filament\Resources\TrackingEventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListTrackingEvents extends ListRecords
{
    protected static string $resource = TrackingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
