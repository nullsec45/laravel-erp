<?php

namespace Modules\Core\Filament\Resources\CoreResource\Pages;

use Modules\Core\Filament\Resources\CoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCore extends EditRecord
{
    protected static string $resource = CoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}