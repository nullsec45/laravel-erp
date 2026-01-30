<?php

namespace Modules\Core\Filament\Resources\CompanySettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Modules\Core\Filament\Resources\CompanySettingResource;
use Modules\Core\Models\CompanySetting;

class ManageCompanySettings extends ManageRecords
{
    protected static string $resource = CompanySettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => CompanySetting::count() === 0),
        ];
    }
}
