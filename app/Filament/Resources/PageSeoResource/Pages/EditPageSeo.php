<?php

namespace App\Filament\Resources\PageSeoResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\CaseStudyResource;
use App\Filament\Resources\PageSeoResource;
use App\Filament\Resources\PortfolioItemResource;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Services\Seo\SeoAuditor;
use App\Services\Seo\SeoDrafter;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPageSeo extends EditRecord
{
    protected static string $resource = PageSeoResource::class;

    public function getHeading(): string
    {
        return 'SEO: '.$this->getRecord()->page_label;
    }

    public function getSubheading(): string
    {
        return $this->getRecord()->selfUrl().' · '.$this->getRecord()->typeLabel();
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Action::make('draft')
                ->label('Draft from page content')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->tooltip('Fills only empty title/description fields from the page\'s own content. Review before saving.')
                ->action(function (SeoDrafter $drafter) {
                    $draft = $drafter->draft($this->getRecord());
                    $filled = [];
                    foreach ($draft as $field => $value) {
                        if (filled($value) && blank($this->data[$field] ?? null)) {
                            $this->data[$field] = $value;
                            $filled[] = $field === 'title' ? 'SEO title' : 'meta description';
                        }
                    }

                    $filled
                        ? Notification::make()->title('Draft added to '.implode(' and ', $filled))->body('Review the text, then Save.')->success()->send()
                        : Notification::make()->title('Nothing to draft')->body('The title and description already have values.')->send();
                }),
            Action::make('rescan')
                ->label('Rescan')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (SeoAuditor $auditor) {
                    $auditor->scan($this->getRecord());
                    Notification::make()->title('Scanned: '.$this->getRecord()->seo_score.' / 100')->success()->send();
                }),
            Action::make('editContent')
                ->label('Edit content')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->visible($record->seoable !== null)
                ->url(fn () => match (true) {
                    $record->seoable instanceof Article => ArticleResource::getUrl('edit', ['record' => $record->seoable]),
                    $record->seoable instanceof CaseStudy => CaseStudyResource::getUrl('edit', ['record' => $record->seoable]),
                    $record->seoable instanceof PortfolioItem => PortfolioItemResource::getUrl('edit', ['record' => $record->seoable]),
                    default => null,
                }),
            Action::make('view')
                ->label('View page')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(url($record->path()))
                ->openUrlInNewTab(),
        ];
    }

    protected function afterSave(): void
    {
        app(SeoAuditor::class)->scan($this->getRecord());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Saved — live on the page now. New score: '.$this->getRecord()->seo_score.' / 100';
    }
}
