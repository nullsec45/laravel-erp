<?php

namespace Modules\Inventory\Filament\Resources\BrandResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\BrandResource;
use Modules\Core\Models\ActivityLog;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'brand_created',
            'Created brand: ' . $this->record->name,
            $this->record
        );
    }
}
