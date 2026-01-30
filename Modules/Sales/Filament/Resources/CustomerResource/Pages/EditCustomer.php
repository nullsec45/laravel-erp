<?php

namespace Modules\Sales\Filament\Resources\CustomerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\CustomerResource;
use Modules\Core\Models\ActivityLog;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

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
            'updated',
            $this->record,
            "Updated customer: {$this->record->name}",
            [
                'customer_code' => $this->record->customer_code,
                'name' => $this->record->name,
            ]
        );
    }
}
