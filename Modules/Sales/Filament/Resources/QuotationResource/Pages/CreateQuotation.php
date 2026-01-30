<?php

namespace Modules\Sales\Filament\Resources\QuotationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\QuotationResource;
use Modules\Core\Models\ActivityLog;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'quotation_created',
            'Created quotation: ' . $this->record->quotation_number,
            $this->record
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
