<?php

namespace App\Http\Controllers;

use App\Models\PageSeo;
use App\Models\Redirect;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * Every indexable, self-canonical public URL from the SEO registry. Built
     * live on each request, so new, edited, unpublished or deleted content is
     * reflected immediately.
     */
    public function index(): Response
    {
        PageSeo::syncStaticPages();

        $redirectedPaths = Redirect::where('is_active', true)->pluck('from_path')
            ->map(fn ($path) => '/'.trim($path, '/'))
            ->flip();

        $urls = PageSeo::with('seoable')
            ->get()
            ->filter(fn (PageSeo $seo) => ($seo->isStatic() || $seo->seoable) && $seo->belongsInSitemap())
            ->reject(fn (PageSeo $seo) => $redirectedPaths->has($seo->path()))
            ->map(fn (PageSeo $seo) => [
                'loc' => $seo->selfUrl(),
                'lastmod' => $this->lastModified($seo)->toAtomString(),
            ])
            ->unique('loc')
            ->sortBy('loc')
            ->values();

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * When the page's content last changed: the record itself for content
     * pages; the template file (deployments) or its SEO row for static pages.
     */
    private function lastModified(PageSeo $seo): CarbonInterface
    {
        if ($seo->seoable) {
            return max($seo->seoable->updated_at, $seo->updated_at);
        }

        $view = config("seo.pages.{$seo->route_name}.view");
        $file = $view ? resource_path('views/'.str_replace('.', '/', $view).'.blade.php') : null;

        return $file && is_file($file)
            ? max($seo->updated_at, Carbon::createFromTimestamp(filemtime($file)))
            : $seo->updated_at;
    }
}
