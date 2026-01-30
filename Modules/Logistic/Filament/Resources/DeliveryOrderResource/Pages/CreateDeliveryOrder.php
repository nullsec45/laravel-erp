<?php

namespace Modules\Logistic\Filament\Resources\DeliveryOrderResource\Pages;

use Modules\Logistic\Filament\Resources\DeliveryOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeliveryOrder extends CreateRecord
{
    protected static string $resource = DeliveryOrderResource::class;
}