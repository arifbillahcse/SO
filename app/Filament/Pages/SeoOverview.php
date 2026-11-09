<?php

namespace App\Filament\Pages;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class SeoOverview extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | \UnitEnum | null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'SEO Overview';

    protected static string $view = 'filament.pages.seo-overview';

    public array $sections = [];

    public function mount(): void
    {
        $this->sections = [
            $this->summarize('Static Pages', PageSeo::all(), 'title'),
            $this->summarize('Articles', Article::all(), 'meta_title'),
            $this->summarize('Case Studies', CaseStudy::all(), 'meta_title'),
            $this->summarize('Portfolio Items', PortfolioItem::all(), 'meta_title'),
        ];
    }

    private function summarize(string $label, Collection $records, string $titleField): array
    {
        $total = $records->count();

        return [
            'label' => $label,
            'total' => $total,
            'indexable' => $records->where('robots_index', true)->count(),
            'noindex' => $records->where('robots_index', false)->count(),
            'with_title' => $records->filter(fn ($r) => filled($r->{$titleField}))->count(),
            'with_description' => $records->filter(fn ($r) => filled($r->meta_description))->count(),
            'with_keywords' => $records->filter(fn ($r) => filled($r->focus_keywords))->count(),
            'complete' => $records->filter(fn ($r) => filled($r->{$titleField}) && filled($r->meta_description) && filled($r->focus_keywords))->count(),
        ];
    }

    public function getTotals(): array
    {
        return [
            'total' => collect($this->sections)->sum('total'),
            'indexable' => collect($this->sections)->sum('indexable'),
            'noindex' => collect($this->sections)->sum('noindex'),
            'complete' => collect($this->sections)->sum('complete'),
        ];
    }
}
