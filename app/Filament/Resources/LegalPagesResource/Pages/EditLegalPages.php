<?php

namespace App\Filament\Resources\LegalPagesResource\Pages;

use App\Filament\Resources\LegalPagesResource;
use App\Models\OrganizationSettings;
use Filament\Resources\Pages\EditRecord;

class EditLegalPages extends EditRecord
{
    protected static string $resource = LegalPagesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (['tos', 'privacy'] as $page) {
            foreach (OrganizationSettings::current()->legalPage($page) as $field => $value) {
                $data["{$page}_{$field}"] = $value;
            }
        }

        return $data;
    }
}
