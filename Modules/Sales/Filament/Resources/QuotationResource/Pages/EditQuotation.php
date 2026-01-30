<?php

namespace Modules\Sales\Filament\Resources\QuotationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\QuotationResource;
use Modules\Core\Models\ActivityLog;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

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
            'quotation_updated',
            'Updated quotation: ' . $this->record->quotation_number,
            $this->record
        );
    }
}
