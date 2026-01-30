<?php

namespace Modules\HR\Filament\Resources\PositionResource\Pages;

use Modules\HR\Filament\Resources\PositionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePosition extends CreateRecord
{
    protected static string $resource = PositionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
