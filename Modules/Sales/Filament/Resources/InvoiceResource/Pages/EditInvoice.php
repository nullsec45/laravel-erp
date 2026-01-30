<?php

namespace Modules\Sales\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\InvoiceResource;
use Modules\Core\Models\ActivityLog;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

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
            'invoice_updated',
            'Updated invoice: ' . $this->record->invoice_number,
            $this->record
        );
    }
}
