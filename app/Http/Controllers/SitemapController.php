<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
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
        $lastModByRoute = PageSeo::pluck('updated_at', 'route_name');

        $urls = collect(self::ROUTES)->map(fn (array $route) => [
            'loc' => route($route['name']),
            'priority' => $route['priority'],
            'changefreq' => $route['changefreq'],
            'lastmod' => ($lastModByRoute->get($route['name']) ?? now())->toAtomString(),
        ]);

        $articleUrls = Article::where('robots_index', true)->get()->map(fn (Article $article) => [
            'loc' => route('our-resources.show', $article),
            'priority' => '0.6',
            'changefreq' => 'monthly',
            'lastmod' => $article->updated_at->toAtomString(),
        ]);

        $caseStudyUrls = CaseStudy::where('robots_index', true)->get()->map(fn (CaseStudy $caseStudy) => [
            'loc' => route('case-study.show', $caseStudy),
            'priority' => '0.6',
            'changefreq' => 'monthly',
            'lastmod' => $caseStudy->updated_at->toAtomString(),
        ]);

        $portfolioUrls = PortfolioItem::where('robots_index', true)->get()->map(fn (PortfolioItem $portfolioItem) => [
            'loc' => route('our-works.show', $portfolioItem),
            'priority' => '0.6',
            'changefreq' => 'monthly',
            'lastmod' => $portfolioItem->updated_at->toAtomString(),
        ]);

        $urls = $urls->concat($articleUrls)->concat($caseStudyUrls)->concat($portfolioUrls);

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
