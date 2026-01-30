<?php

namespace Modules\Inventory\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\CategoryResource;
use Modules\Core\Models\ActivityLog;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function afterCreate(): void
    {
        ActivityLog::log(
            'category_created',
            'Created category: ' . $this->record->name,
            $this->record
        );
    }
}
