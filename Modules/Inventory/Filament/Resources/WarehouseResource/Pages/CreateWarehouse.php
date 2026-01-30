<?php

namespace Modules\Inventory\Filament\Resources\WarehouseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\WarehouseResource;
use Modules\Core\Models\ActivityLog;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource = WarehouseResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'warehouse_created',
            'Created warehouse: ' . $this->record->name . ' (' . $this->record->code . ')',
            $this->record
        );
    }
}
