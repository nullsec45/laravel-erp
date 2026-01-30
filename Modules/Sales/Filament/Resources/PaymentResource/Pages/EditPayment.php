<?php

namespace Modules\Sales\Filament\Resources\PaymentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\PaymentResource;
use Modules\Core\Models\ActivityLog;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

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
            'payment_updated',
            'Updated payment: ' . $this->record->payment_number,
            $this->record
        );
    }
}
