<?php

namespace App\Filament\Resources\DiagnosticPageSettingsResource\Pages;

use App\Filament\Resources\DiagnosticPageSettingsResource;
use Filament\Resources\Pages\EditRecord;

class EditDiagnosticPageSettings extends EditRecord
{
    protected static string $resource = DiagnosticPageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
