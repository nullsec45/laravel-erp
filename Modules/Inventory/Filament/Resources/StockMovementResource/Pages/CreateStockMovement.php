<?php

namespace Modules\Inventory\Filament\Resources\StockMovementResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockMovementResource;
use Modules\Core\Models\ActivityLog;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'stock_movement_created',
            'Created stock movement: ' . $this->record->type . ' - ' . $this->record->product->name . ' (' . $this->record->quantity . ')',
            $this->record
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
