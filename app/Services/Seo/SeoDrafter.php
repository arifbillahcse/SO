<?php

namespace App\Services\Seo;

use App\Models\PageSeo;
use App\Support\Seo;
use DOMDocument;
use DOMXPath;

/**
 * Draft SEO text from a page's own content, for an editor to review.
 * Never invents facts: it only reuses the page's existing title, intro or
 * description.
 */
class SeoDrafter
{
    public function __construct(private PageRenderer $renderer) {}

    /**
     * @return array{title: ?string, meta_description: ?string}
     */
    public function draft(PageSeo $seo): array
    {
        if ($seoable = $seo->seoable) {
            return [
                'title' => $seoable->seoDefaultTitle(),
                'meta_description' => Seo::excerpt($seoable->seoDefaultDescription(), 158),
            ];
        }

        $html = $this->renderer->render($seo->path());
        if (! $html) {
            return ['title' => null, 'meta_description' => null];
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        // When the editor has cleared the fields, the rendered page shows the
        // template's own title and description: the page author's defaults.
        $title = trim((string) $xpath->query('//head/title')->item(0)?->textContent);
        $description = $xpath->query('//meta[@name="description"]')->item(0)?->getAttribute('content');

        if (blank($description)) {
            foreach ($xpath->query('//body//p[not(ancestor::header) and not(ancestor::nav) and not(ancestor::footer)]') as $p) {
                if (mb_strlen(trim($p->textContent)) >= 80) {
                    $description = $p->textContent;
                    break;
                }
            }
        }

        return [
            'title' => Seo::plain($title) ?: null,
            'meta_description' => Seo::excerpt($description, 158),
        ];
    }
}
