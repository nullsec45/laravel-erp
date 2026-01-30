<?php

namespace Modules\Sales\Filament\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\CustomerResource;
use Modules\Core\Models\ActivityLog;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'created',
            $this->record,
            "Created customer: {$this->record->name}",
            [
                'customer_code' => $this->record->customer_code,
                'name' => $this->record->name,
                'email' => $this->record->email,
            ]
        );
    }
}
