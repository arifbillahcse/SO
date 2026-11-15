<?php

namespace App\Filament\Concerns;

use App\Models\PageSeo;
use App\Services\Seo\SeoAuditor;

/**
 * For Create/Edit pages of content with SEO: re-score the page after saving,
 * since content changes (body, headings, links) affect its SEO checks.
 */
trait RescansSeo
{
    protected function afterSave(): void
    {
        $this->rescanSeo();
    }

    protected function afterCreate(): void
    {
        $this->rescanSeo();
    }

    private function rescanSeo(): void
    {
        $record = $this->getRecord();
        $seo = match (true) {
            method_exists($record, 'ensureSeo') => $record->ensureSeo()->fresh(),
            method_exists($record, 'pageSeo') => $record->pageSeo()->first(),
            default => null,
        };

        if ($seo instanceof PageSeo) {
            app(SeoAuditor::class)->scan($seo);
        }
    }
}
