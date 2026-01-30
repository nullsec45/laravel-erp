<?php

namespace Modules\Sales\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\PaymentResource;
use Modules\Core\Models\ActivityLog;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'payment_created',
            'Created payment: ' . $this->record->payment_number . ' for $' . number_format($this->record->amount, 2),
            $this->record
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
