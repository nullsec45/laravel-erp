<?php

namespace Modules\Inventory\Filament\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\BrandResource;
use Modules\Core\Models\ActivityLog;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

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
            'brand_updated',
            'Updated brand: ' . $this->record->name,
            $this->record
        );
    }
}
