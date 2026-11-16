<?php

namespace App\Filament\Resources\LegalPagesResource\Pages;

use App\Filament\Resources\LegalPagesResource;
use App\Models\OrganizationSettings;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListLegalPages extends ListRecords
{
    protected static string $resource = LegalPagesResource::class;

    public function mount(): void
    {
        OrganizationSettings::current();
        parent::mount();
    }

    public function getSubheading(): ?string
    {
        return 'Edit the headings, introductions, effective dates and formatted content for /tos and /privacy. Both links appear at the bottom of every public page.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('terms')->label('View terms')->url(fn (): string => route('tos'))->openUrlInNewTab(),
            Action::make('privacy')->label('View privacy')->url(fn (): string => route('privacy'))->openUrlInNewTab(),
        ];
    }
}
