<?php

namespace App\Http\Controllers;

use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
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
                'priority' => $this->priority($seo),
            ])
            ->unique('loc')
            ->sortBy([['priority', 'desc'], ['loc', 'asc']])
            ->values();

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function priority(PageSeo $seo): string
    {
        if (! $seo->isStatic()) {
            return '0.6';
        }

        return match (true) {
            $seo->route_name === 'home' => '1.0',
            in_array($seo->route_name, ['tos', 'privacy'], true) => '0.3',
            $seo->route_name === 'services' || in_array($seo->page_type, ['service', 'solution'], true) => '0.9',
            default => '0.8',
        };
    }

    /**
     * When the page's content last changed: the record itself for content
     * pages; the template, SEO row or saved page settings for static routes.
     */
    private function lastModified(PageSeo $seo): CarbonInterface
    {
        if ($seo->seoable) {
            return max($seo->seoable->updated_at, $seo->updated_at);
        }

        $view = config("seo.pages.{$seo->route_name}.view");
        $file = $view ? resource_path('views/'.str_replace('.', '/', $view).'.blade.php') : null;
        $modified = $file && is_file($file)
            ? max($seo->updated_at, Carbon::createFromTimestamp(filemtime($file)))
            : $seo->updated_at;

        $settingsModified = match ($seo->route_name) {
            'contact' => ContactPageSettings::query()->value('updated_at'),
            'tos', 'privacy' => OrganizationSettings::query()->value('updated_at'),
            default => null,
        };

        return $settingsModified ? max($modified, Carbon::parse($settingsModified)) : $modified;
    }
}
