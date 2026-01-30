<?php

namespace Modules\Inventory\Filament\Resources\UnitResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\UnitResource;
use Modules\Core\Models\ActivityLog;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLog::log(
            'unit_updated',
            'Updated unit: ' . $this->record->name,
            $this->record
        );
    }
}
