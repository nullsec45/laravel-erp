<?php

namespace Modules\Logistic\Filament\Resources\TrackingEventResource\Pages;

use Modules\Logistic\Filament\Resources\TrackingEventResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditTrackingEvent extends EditRecord
{
    protected static string $resource = TrackingEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
