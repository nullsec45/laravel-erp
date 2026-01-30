<?php

namespace Modules\Sales\Filament\Resources\SalesOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\SalesOrderResource;
use Modules\Core\Models\ActivityLog;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

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
            'sales_order_updated',
            'Updated sales order: ' . $this->record->order_number,
            $this->record
        );
    }
}
