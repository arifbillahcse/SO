<?php

namespace App\Filament\Resources\ContactPageSettingsResource\Pages;

use App\Filament\Resources\ContactPageSettingsResource;
use Filament\Resources\Pages\EditRecord;

class EditContactPageSettings extends EditRecord
{
    protected static string $resource = ContactPageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
