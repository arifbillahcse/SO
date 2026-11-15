<?php

namespace App\Filament\Resources\PageSeoResource\Pages;

use App\Filament\Resources\PageSeoResource;
use App\Filament\Resources\PageSeoResource\Widgets\SeoStatsOverview;
use App\Models\PageSeo;
use App\Services\Seo\SeoAuditor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPageSeos extends ListRecords
{
    protected static string $resource = PageSeoResource::class;

    protected ?string $subheading = 'Title, description and search settings for every public page. Scores are an internal checklist from each page\'s rendered HTML — not Google data.';

    public function mount(): void
    {
        PageSeo::syncStaticPages();

        parent::mount();
    }

    protected function getHeaderWidgets(): array
    {
        return [SeoStatsOverview::class];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rescanAll')
                ->label('Rescan all pages')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('Renders every public page and recalculates its SEO score. Takes a few seconds.')
                ->action(function (SeoAuditor $auditor) {
                    $count = 0;
                    PageSeo::with('seoable')->get()->each(function (PageSeo $seo) use ($auditor, &$count) {
                        if ($seo->isStatic() || $seo->seoable) {
                            $auditor->scan($seo);
                            $count++;
                        }
                    });

                    Notification::make()->title("Rescanned {$count} pages")->success()->send();
                }),
            Action::make('sitemap')
                ->label('View sitemap.xml')
                ->icon('heroicon-o-map')
                ->color('gray')
                ->url(route('sitemap'))
                ->openUrlInNewTab(),
        ];
    }
}
