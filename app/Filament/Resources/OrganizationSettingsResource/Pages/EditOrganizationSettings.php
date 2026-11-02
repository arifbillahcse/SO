<?php

namespace App\Filament\Resources\OrganizationSettingsResource\Pages;

use App\Filament\Resources\OrganizationSettingsResource;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationSettings extends EditRecord
{
    protected static string $resource = OrganizationSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
