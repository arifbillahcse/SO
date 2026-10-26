<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Public, indexable pages only — our-founders is intentionally
     * excluded (noindex,nofollow), as are the POST-only form routes.
     */
    private const ROUTES = [
        ['name' => 'home', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['name' => 'web-development', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['name' => 'seo', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['name' => 'manage-cloud', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['name' => 'automation', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ['name' => 'ecommerce-solution', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['name' => 'cloud-solution', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['name' => 'automation-solution', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['name' => 'our-works', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['name' => 'about-us', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['name' => 'case-study', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['name' => 'our-resources', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['name' => 'affiliate-program', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ];

    public function index(): Response
    {
        $urls = collect(self::ROUTES)->map(fn (array $route) => [
            'loc' => route($route['name']),
            'priority' => $route['priority'],
            'changefreq' => $route['changefreq'],
        ]);

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
