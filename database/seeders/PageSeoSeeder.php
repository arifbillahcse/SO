<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    /**
     * One row per public route, so the SEO admin screen always lists
     * every page even before an editor has customized anything.
     */
    private const ROUTES = [
        'home',
        'web-development',
        'seo',
        'manage-cloud',
        'automation',
        'ecommerce-solution',
        'cloud-solution',
        'automation-solution',
        'our-works',
        'about-us',
        'case-study',
        'our-resources',
        'affiliate-program',
        'our-founders',
    ];

    /**
     * Pages whose schema.org type is more specific than the generic
     * "WebPage" default. Applied unconditionally since the schema_type
     * column is new and can't yet have been customized by an editor.
     */
    private const SCHEMA_TYPES = [
        'web-development' => 'Service',
        'seo' => 'Service',
        'manage-cloud' => 'Service',
        'automation' => 'Service',
        'about-us' => 'AboutPage',
    ];

    public function run(): void
    {
        foreach (self::ROUTES as $routeName) {
            $pageSeo = PageSeo::firstOrCreate(
                ['route_name' => $routeName],
                $routeName === 'our-founders'
                    ? ['robots_index' => false, 'robots_follow' => false]
                    : []
            );

            if (isset(self::SCHEMA_TYPES[$routeName])) {
                $pageSeo->update(['schema_type' => self::SCHEMA_TYPES[$routeName]]);
            }
        }
    }
}
