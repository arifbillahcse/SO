<?php

namespace App\Services\Seo;

use App\Models\PageSeo;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Internal SEO completion score (0–100) for one page, from its saved SEO
 * fields plus its actual rendered HTML. Ten checks worth 10 points each;
 * checks that don't apply (e.g. no images) are left out of the total.
 * This is a completeness checklist, not a Google ranking signal.
 */
class SeoAuditor
{
    public const CHECKS = [
        'title' => 'SEO title',
        'description' => 'Meta description',
        'keyword' => 'Focus keyword',
        'h1' => 'Single H1 heading',
        'headings' => 'Heading structure',
        'images' => 'Image alt text',
        'links' => 'Internal links',
        'canonical' => 'Canonical URL',
        'robots' => 'Robots directives',
        'schema' => 'Structured data',
    ];

    public function __construct(private PageRenderer $renderer) {}

    public function scan(PageSeo $seo): PageSeo
    {
        $result = $this->audit($seo, $this->renderer->render($seo->path()));

        $seo->forceFill([
            'seo_score' => $result['score'],
            'seo_checks' => $result,
            'seo_scanned_at' => now(),
        ])->saveQuietly();

        return $seo;
    }

    /**
     * @return array{score: int, rendered: bool, checks: list<array{key: string, label: string, status: string, points: int, message: string}>, page: array<string, mixed>}
     */
    public function audit(PageSeo $seo, ?string $html): array
    {
        $page = $html ? $this->parse($html) : null;

        $checks = [
            $this->checkTitle($seo, $page),
            $this->checkDescription($seo, $page),
            $this->checkKeyword($seo, $page),
        ];

        if ($page) {
            array_push($checks,
                $this->checkH1($page),
                $this->checkHeadings($page),
                $this->checkImages($page),
                $this->checkLinks($page, $seo),
                $this->checkCanonical($page, $seo),
                $this->checkRobots($page, $seo),
                $this->checkSchema($page),
            );
        } else {
            foreach (['h1', 'headings', 'images', 'links', 'canonical', 'robots', 'schema'] as $key) {
                $checks[] = $this->result($key, 'na', 0, 'Not checked — the page could not be rendered. Make sure it loads, then rescan.');
            }
        }

        $applicable = array_filter($checks, fn ($c) => $c['status'] !== 'na');
        $score = $applicable ? (int) round(array_sum(array_column($applicable, 'points')) / (count($applicable) * 10) * 100) : 0;

        return [
            'score' => $score,
            'rendered' => $page !== null,
            'checks' => $checks,
            'page' => $page ? array_intersect_key($page, array_flip(['title', 'description', 'h1', 'schema_types', 'schema_blocks', 'schema_errors', 'robots', 'canonical'])) : [],
        ];
    }

    private function parse(string $html): array
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        // Site-wide chrome is excluded from content checks.
        $outsideChrome = 'not(ancestor::header) and not(ancestor::nav) and not(ancestor::footer)';

        $text = fn (DOMElement $el) => trim(preg_replace('/\s+/u', ' ', $el->textContent));

        $headings = [];
        foreach ($xpath->query("//body//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][{$outsideChrome}]") as $el) {
            $headings[] = ['level' => (int) substr($el->nodeName, 1), 'text' => $text($el)];
        }

        $images = [];
        foreach ($xpath->query('//body//img') as $el) {
            $images[] = ['src' => $el->getAttribute('src'), 'has_alt' => $el->hasAttribute('alt'), 'alt' => $el->getAttribute('alt')];
        }

        $links = [];
        foreach ($xpath->query("//body//a[@href][{$outsideChrome}]") as $el) {
            $links[] = $el->getAttribute('href');
        }

        $schemas = [];
        $schemaBlocks = [];
        $schemaErrors = 0;
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $el) {
            $decoded = json_decode($el->textContent, true);
            if (! is_array($decoded)) {
                $schemaErrors++;

                continue;
            }
            $schemas[] = $decoded['@type'] ?? 'Unknown';
            $schemaBlocks[] = $decoded;
        }

        $meta = fn (string $query) => array_map(
            fn (DOMElement $el) => $el->getAttribute('content') ?: $el->getAttribute('href'),
            iterator_to_array($xpath->query($query)),
        );

        $titleNode = $xpath->query('//head/title')->item(0);

        return [
            'title' => $titleNode ? $text($titleNode) : null,
            'description' => $meta('//meta[@name="description"]')[0] ?? null,
            'h1' => array_values(array_map(fn ($h) => $h['text'], array_filter($headings, fn ($h) => $h['level'] === 1))),
            'headings' => $headings,
            'images' => $images,
            'links' => $links,
            'canonical' => $meta('//link[@rel="canonical"]'),
            'robots' => $meta('//meta[@name="robots"]'),
            'schema_types' => $schemas,
            'schema_blocks' => $schemaBlocks,
            'schema_errors' => $schemaErrors,
        ];
    }

    private function checkTitle(PageSeo $seo, ?array $page): array
    {
        if (blank($seo->title)) {
            $fallback = $page['title'] ?? null;

            return $this->result('title', 'fail', 0, $fallback
                ? "No SEO title set — Google sees the template default \"{$fallback}\" (".mb_strlen($fallback).' chars). Write a unique title.'
                : 'No SEO title set. Write a unique title of 50–60 characters.');
        }

        $length = mb_strlen($seo->title);

        return match (true) {
            $length >= 30 && $length <= 60 => $this->result('title', 'pass', 10, "{$length} characters — good length."),
            $length >= 20 && $length <= 70 => $this->result('title', 'warn', 6, "{$length} characters — aim for 50–60 so Google doesn't cut it off or pad it."),
            default => $this->result('title', 'warn', 3, "{$length} characters — too ".($length < 20 ? 'short' : 'long').'. Aim for 50–60.'),
        };
    }

    private function checkDescription(PageSeo $seo, ?array $page): array
    {
        if (blank($seo->meta_description)) {
            return $this->result('description', 'fail', 0, filled($page['description'] ?? null)
                ? 'No meta description set — the template default is used. Write one that sells this page.'
                : 'No meta description. Write 140–160 characters describing the page.');
        }

        $length = mb_strlen($seo->meta_description);

        return match (true) {
            $length >= 120 && $length <= 160 => $this->result('description', 'pass', 10, "{$length} characters — good length."),
            $length >= 70 && $length <= 180 => $this->result('description', 'warn', 6, "{$length} characters — aim for 140–160."),
            default => $this->result('description', 'warn', 3, "{$length} characters — too ".($length < 70 ? 'short' : 'long').'. Aim for 140–160.'),
        };
    }

    private function checkKeyword(PageSeo $seo, ?array $page): array
    {
        $keyword = trim((string) $seo->focus_keyword);

        if ($keyword === '') {
            return $this->result('keyword', 'fail', 0, 'No focus keyword. Choose the main search phrase this page should rank for.');
        }

        $in = fn (?string $haystack) => $haystack !== null && mb_stripos($haystack, $keyword) !== false;
        $title = $seo->title ?: ($page['title'] ?? null);
        $places = array_keys(array_filter([
            'title' => $in($title),
            'description' => $in($seo->meta_description ?: ($page['description'] ?? null)),
            'H1' => $page && $in(implode(' ', $page['h1'])),
        ]));

        if (in_array('title', $places, true) && count($places) >= 2) {
            return $this->result('keyword', 'pass', 10, '"'.$keyword.'" appears in the '.implode(', ', $places).'.');
        }

        if ($places) {
            return $this->result('keyword', 'warn', 6, '"'.$keyword.'" appears only in the '.implode(', ', $places).'. Use it naturally in the title and description too.');
        }

        return $this->result('keyword', 'warn', 3, '"'.$keyword.'" isn\'t used in the title, description or H1. Use it where it reads naturally.');
    }

    private function checkH1(array $page): array
    {
        $count = count($page['h1']);

        return match ($count) {
            1 => $this->result('h1', 'pass', 10, 'One H1: "'.$page['h1'][0].'".'),
            0 => $this->result('h1', 'fail', 0, 'No H1 heading on the page.'),
            default => $this->result('h1', 'fail', 0, "{$count} H1 headings — a page should have exactly one."),
        };
    }

    private function checkHeadings(array $page): array
    {
        $levels = array_column($page['headings'], 'level');

        if (! in_array(2, $levels, true)) {
            return $this->result('headings', 'fail', 0, 'No H2 subheadings — break the content into sections.');
        }

        $skips = [];
        $previous = 1;
        foreach ($page['headings'] as $heading) {
            if ($heading['level'] > $previous + 1) {
                $skips[] = "H{$previous}→H{$heading['level']} at \"".mb_strimwidth($heading['text'], 0, 40, '…').'"';
            }
            $previous = $heading['level'];
        }

        return $skips
            ? $this->result('headings', 'warn', 5, 'Skipped heading levels: '.implode('; ', array_slice($skips, 0, 3)).(count($skips) > 3 ? ' …' : '').'.')
            : $this->result('headings', 'pass', 10, count($levels).' headings in a logical H1→H2→H3 order.');
    }

    private function checkImages(array $page): array
    {
        if (! $page['images']) {
            return $this->result('images', 'na', 0, 'No images on this page.');
        }

        $missing = array_filter($page['images'], fn ($img) => ! $img['has_alt']);

        return $missing
            ? $this->result('images', 'fail', 0, count($missing).' of '.count($page['images']).' images have no alt attribute: '.implode(', ', array_map(fn ($i) => basename($i['src']), array_slice($missing, 0, 3))).'.')
            : $this->result('images', 'pass', 10, 'All '.count($page['images']).' images have alt text (empty alt = decorative).');
    }

    private function checkLinks(array $page, PageSeo $seo): array
    {
        $self = rtrim($seo->path(), '/') ?: '/';
        $hosts = array_filter([parse_url(config('seo.site_url'), PHP_URL_HOST), parse_url(config('app.url'), PHP_URL_HOST), request()->getHost()]);

        $internal = collect($page['links'])
            ->map(function (string $href) use ($hosts) {
                if (str_starts_with($href, '#') || preg_match('/^(mailto|tel|javascript):/i', $href)) {
                    return null;
                }
                $host = parse_url($href, PHP_URL_HOST);
                if ($host && ! in_array($host, $hosts, true)) {
                    return null;
                }

                return rtrim(parse_url($href, PHP_URL_PATH) ?: '/', '/') ?: '/';
            })
            ->filter(fn ($path) => $path !== null && $path !== $self)
            ->unique()
            ->values();

        return match (true) {
            $internal->count() >= 3 => $this->result('links', 'pass', 10, $internal->count().' internal links to other pages in the content.'),
            $internal->count() >= 1 => $this->result('links', 'warn', 5, 'Only '.$internal->count().' internal link(s) in the content — link to 3+ related pages.'),
            default => $this->result('links', 'fail', 0, 'No internal links in the page content (navigation and footer excluded).'),
        };
    }

    private function checkCanonical(array $page, PageSeo $seo): array
    {
        $tags = $page['canonical'];

        if (count($tags) !== 1) {
            return $this->result('canonical', 'fail', 0, count($tags).' canonical tags found — there must be exactly one.');
        }

        if ($tags[0] !== $seo->canonicalUrl()) {
            return $this->result('canonical', 'fail', 0, "Canonical is {$tags[0]} but should be {$seo->canonicalUrl()}.");
        }

        return $seo->canonicalisesElsewhere()
            ? $this->result('canonical', 'warn', 5, "Points to another URL ({$tags[0]}), so this page is left out of the sitemap.")
            : $this->result('canonical', 'pass', 10, "Self-referencing: {$tags[0]}");
    }

    private function checkRobots(array $page, PageSeo $seo): array
    {
        $tags = $page['robots'];

        if (count($tags) !== 1) {
            return $this->result('robots', 'fail', 0, count($tags).' robots meta tags found — there must be exactly one.');
        }

        $saysNoindex = str_contains($tags[0], 'noindex');

        if ($saysNoindex !== ! $seo->isIndexable()) {
            return $this->result('robots', 'fail', 0, "Rendered \"{$tags[0]}\" doesn't match this page's settings.");
        }

        if (! $saysNoindex) {
            return $this->result('robots', 'pass', 10, "\"{$tags[0]}\" — indexable and included in the sitemap.");
        }

        // A deliberate noindex is correct configuration; one forced by thin or
        // unpublished content is something to fix.
        return $seo->robots_index
            ? $this->result('robots', 'warn', 5, 'noindex — '.$seo->noindexReason())
            : $this->result('robots', 'pass', 10, 'noindex, as set in this page\'s SEO settings; excluded from the sitemap.');
    }

    private function checkSchema(array $page): array
    {
        if ($page['schema_errors'] > 0) {
            return $this->result('schema', 'fail', 0, $page['schema_errors'].' JSON-LD block(s) contain invalid JSON.');
        }

        $pageLevel = array_values(array_diff($page['schema_types'], ['Organization', 'WebSite']));

        return $pageLevel
            ? $this->result('schema', 'pass', 10, 'Valid JSON-LD: '.implode(', ', $page['schema_types']).'.')
            : $this->result('schema', 'warn', 5, 'Only site-wide schema found; no page-level structured data.');
    }

    private function result(string $key, string $status, int $points, string $message): array
    {
        return ['key' => $key, 'label' => self::CHECKS[$key], 'status' => $status, 'points' => $points, 'message' => $message];
    }
}
