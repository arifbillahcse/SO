<?php

namespace App\Filament\Resources\PageSeoResource\Widgets;

use App\Models\PageSeo;
use App\Services\Seo\SearchPerformance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SeoStatsOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = ['default' => 2, 'md' => 3, 'xl' => 5];

    protected function getStats(): array
    {
        $pages = PageSeo::with('seoable')->get()
            ->filter(fn (PageSeo $seo) => $seo->isStatic() || $seo->seoable);

        $total = $pages->count();
        $static = $pages->filter->isStatic()->count();
        $indexable = $pages->filter->isIndexable()->count();
        $scanned = $pages->whereNotNull('seo_score');
        $complete = $pages->filter(fn (PageSeo $seo) => $seo->seo_score >= PageSeo::SCORE_GOOD && ! $seo->missingFields())->count();
        $missing = fn (string $field) => $pages->filter(fn (PageSeo $seo) => blank($seo->{$field}))->count();
        $search = app(SearchPerformance::class);
        $totals = $search->siteTotals();

        return [
            Stat::make('Public pages', $total)
                ->description($static.' static pages · '.($total - $static).' content items')
                ->icon('heroicon-o-document-text'),
            Stat::make('Indexable pages', $indexable)
                ->description(($total - $indexable).' noindex (thin, unpublished or set to noindex)')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            Stat::make('Verified indexed in Google', '—')
                ->description('Not checked: needs Search Console. Use URL Inspection meanwhile.')
                ->icon('heroicon-o-globe-alt')
                ->color('gray'),
            Stat::make('Complete SEO', $complete)
                ->description('Score 80+ with title, description and keyword')
                ->icon('heroicon-o-trophy')
                ->color('success'),
            Stat::make('Incomplete SEO', $total - $complete)
                ->description($pages->whereNull('seo_score')->count().' not scanned yet')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($total - $complete > 0 ? 'warning' : 'success'),
            Stat::make('Missing SEO titles', $missing('title'))
                ->icon('heroicon-o-h1')
                ->color($missing('title') ? 'danger' : 'success'),
            Stat::make('Missing descriptions', $missing('meta_description'))
                ->icon('heroicon-o-bars-3-bottom-left')
                ->color($missing('meta_description') ? 'danger' : 'success'),
            Stat::make('Missing focus keywords', $missing('focus_keyword'))
                ->icon('heroicon-o-key')
                ->color($missing('focus_keyword') ? 'warning' : 'success'),
            Stat::make('Average SEO score', $scanned->count() ? round($scanned->avg('seo_score')).' / 100' : '—')
                ->description('Internal checklist, not a Google metric')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('Google clicks, 28 days', $totals ? number_format($totals['clicks']) : '—')
                ->description($totals
                    ? number_format($totals['impressions']).' impressions · CTR '.round($totals['ctr'] * 100, 1).'% · avg position '.number_format($totals['position'], 1)
                    : 'Search Console not connected: data unavailable')
                ->icon('heroicon-o-signal')
                ->color($search->isConnected() ? 'success' : 'gray'),
        ];
    }
}
