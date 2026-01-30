<?php

namespace Modules\Inventory\Filament\Resources\StockMovementResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\StockMovementResource;
use Modules\Core\Models\ActivityLog;

class EditStockMovement extends EditRecord
{
    protected static string $resource = StockMovementResource::class;

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
            'stock_movement_updated',
            'Updated stock movement for: ' . $this->record->product->name,
            $this->record
        );
    }
}
