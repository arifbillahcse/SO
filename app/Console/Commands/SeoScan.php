<?php

namespace App\Console\Commands;

use App\Models\PageSeo;
use App\Services\Seo\SeoAuditor;
use Illuminate\Console\Command;

class SeoScan extends Command
{
    protected $signature = 'seo:scan {--type= : Only scan one page type (page, service, article, ...)}';

    protected $description = 'Render every public page and recalculate its internal SEO completion score';

    public function handle(SeoAuditor $auditor): int
    {
        PageSeo::syncStaticPages();

        $rows = [];
        PageSeo::with('seoable')
            ->when($this->option('type'), fn ($q, $type) => $q->where('page_type', $type))
            ->orderBy('page_type')->orderBy('page_label')
            ->get()
            ->each(function (PageSeo $seo) use ($auditor, &$rows) {
                if (! $seo->isStatic() && ! $seo->seoable) {
                    return;
                }
                $auditor->scan($seo);
                $rows[] = [$seo->page_label, $seo->path(), $seo->seo_score, $seo->isIndexable() ? 'index' : 'noindex', $seo->seo_checks['rendered'] ? 'yes' : 'NO'];
            });

        $this->table(['Page', 'Path', 'Score', 'Robots', 'Rendered'], $rows);

        return self::SUCCESS;
    }
}
