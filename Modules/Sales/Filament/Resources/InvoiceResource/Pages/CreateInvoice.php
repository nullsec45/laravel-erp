<?php

namespace Modules\Sales\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\InvoiceResource;
use Modules\Core\Models\ActivityLog;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'invoice_created',
            'Created invoice: ' . $this->record->invoice_number,
            $this->record
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
