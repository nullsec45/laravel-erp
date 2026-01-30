<?php

namespace Modules\Inventory\Filament\Resources\UnitResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\UnitResource;
use Modules\Core\Models\ActivityLog;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'unit_created',
            'Created unit: ' . $this->record->name . ' (' . $this->record->short_name . ')',
            $this->record
        );
    }
}
