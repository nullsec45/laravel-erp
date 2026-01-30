<?php

namespace Modules\Inventory\Filament\Resources\InventoryResource\Pages;

use Modules\Inventory\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;
}