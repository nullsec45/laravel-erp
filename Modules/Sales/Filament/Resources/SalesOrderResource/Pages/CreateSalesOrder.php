<?php

namespace Modules\Sales\Filament\Resources\SalesOrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\SalesOrderResource;
use Modules\Core\Models\ActivityLog;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'sales_order_created',
            'Created sales order: ' . $this->record->order_number,
            $this->record
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
