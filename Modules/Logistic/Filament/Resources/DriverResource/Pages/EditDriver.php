<?php

namespace Modules\Logistic\Filament\Resources\DriverResource\Pages;

use Modules\Logistic\Filament\Resources\DriverResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
