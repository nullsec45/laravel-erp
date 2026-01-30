<?php

namespace Modules\Inventory\Filament\Resources\WarehouseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\WarehouseResource;
use Modules\Core\Models\ActivityLog;

class EditWarehouse extends EditRecord
{
    protected static string $resource = WarehouseResource::class;

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
            'warehouse_updated',
            'Updated warehouse: ' . $this->record->name,
            $this->record
        );
    }
}
