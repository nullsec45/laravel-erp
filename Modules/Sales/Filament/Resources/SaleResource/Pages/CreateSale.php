<?php

namespace Modules\Sales\Filament\Resources\SaleResource\Pages;

use Modules\Sales\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
}