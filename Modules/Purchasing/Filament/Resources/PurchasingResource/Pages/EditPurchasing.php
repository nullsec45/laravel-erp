<?php

namespace Modules\Purchasing\Filament\Resources\PurchasingResource\Pages;

use Modules\Purchasing\Filament\Resources\PurchasingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchasing extends EditRecord
{
    protected static string $resource = PurchasingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}