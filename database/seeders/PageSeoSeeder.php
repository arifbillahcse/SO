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

    public function run(): void
    {
        foreach (self::ROUTES as $routeName) {
            PageSeo::firstOrCreate(
                ['route_name' => $routeName],
                $routeName === 'our-founders'
                    ? ['robots_index' => false, 'robots_follow' => false]
                    : []
            );
        }
    }
}
