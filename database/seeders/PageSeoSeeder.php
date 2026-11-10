<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    /**
     * One row per public URL — every static route in config/seo.php and every
     * content detail page — so the SEO dashboard always lists the whole site.
     * Never overwrites SEO an editor has already written.
     */
    public function run(): void
    {
        PageSeo::syncStaticPages();

        foreach ([Article::class, CaseStudy::class, PortfolioItem::class] as $class) {
            $class::query()->each(fn ($record) => $record->ensureSeo());
        }
    }
}
