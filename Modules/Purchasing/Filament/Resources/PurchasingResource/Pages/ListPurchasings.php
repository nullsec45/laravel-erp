<?php

namespace Modules\Purchasing\Filament\Resources\PurchasingResource\Pages;

use Modules\Purchasing\Filament\Resources\PurchasingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchasings extends ListRecords
{
    protected static string $resource = PurchasingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}