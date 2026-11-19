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

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['faqs'] ??= $this->record->publicFaqs();
        $data['faq_title'] ??= 'Questions we get asked';

        return $data;
    }
}
