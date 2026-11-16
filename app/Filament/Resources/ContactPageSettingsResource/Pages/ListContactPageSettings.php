<?php

namespace App\Filament\Resources\ContactPageSettingsResource\Pages;

use App\Filament\Resources\ContactPageSettingsResource;
use App\Filament\Resources\PageSeoResource;
use App\Models\ContactPageSettings;
use App\Models\PageSeo;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListContactPageSettings extends ListRecords
{
    protected static string $resource = ContactPageSettingsResource::class;

    public function mount(): void
    {
        ContactPageSettings::current();
        parent::mount();
    }

    public function getSubheading(): ?string
    {
        return 'Edit the contact-page content here. Search settings are available through Edit SEO.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('View contact page')->url(fn (): string => route('contact'))->openUrlInNewTab(),
            Action::make('seo')->label('Edit SEO')->url(fn (): string => PageSeoResource::getUrl('edit', ['record' => PageSeo::forRoute('contact')])),
        ];
    }
}
