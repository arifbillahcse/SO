<?php

namespace App\Support;

use App\Models\OrganizationSettings;
use App\Models\PageSeo;

/**
 * Builds every SEO tag for one page. Values saved in the admin (PageSeo)
 * win; otherwise the template's own @section defaults are used.
 */
class SeoHead
{
    public const DEFAULT_TITLE = 'Softorio — Website Development, Cloud & SEO Agency';

    /**
     * @param  array{title?: ?string, description?: ?string, image?: ?string, og_type?: ?string, breadcrumbs?: ?string, page_schema?: ?string}  $sections
     */
    public static function build(?PageSeo $seo, array $sections, OrganizationSettings $org, ?string $requestPath = null): array
    {
        $seoable = $seo?->seoable;

        $title = Seo::plain($seo?->title)
            ?: Seo::plain($sections['title'] ?? null)
            ?: ($seoable?->seoDefaultTitle() ?? self::DEFAULT_TITLE);

        $description = Seo::plain($seo?->meta_description)
            ?: Seo::plain($sections['description'] ?? null)
            ?: Seo::excerpt($seoable?->seoDefaultDescription());

        $canonical = $seo ? $seo->canonicalUrl() : Seo::url($requestPath ?? '/');
        $selfUrl = $seo ? $seo->selfUrl() : $canonical;

        $robots = [
            ($seo?->isIndexable() ?? true) ? 'index' : 'noindex',
            ($seo?->robots_follow ?? true) ? 'follow' : 'nofollow',
        ];
        foreach (array_filter(array_map('trim', explode(',', (string) $seo?->robots_advanced))) as $directive) {
            if (! in_array($directive, $robots, true)) {
                $robots[] = $directive;
            }
        }

        $image = Seo::imageUrl($seo?->og_image)
            ?: Seo::imageUrl($sections['image'] ?? null)
            ?: Seo::imageUrl($seoable?->seoDefaultImage())
            ?: Seo::imageUrl(config('seo.default_og_image'));

        $ogTitle = Seo::plain($seo?->og_title) ?: $title;
        $ogDescription = Seo::plain($seo?->og_description) ?: $description;

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => implode(', ', $robots),
            'og' => array_filter([
                'og:site_name' => config('seo.site_name'),
                'og:type' => $sections['og_type'] ?? 'website',
                'og:url' => $canonical,
                'og:title' => $ogTitle,
                'og:description' => $ogDescription,
                'og:image' => $image,
                'og:locale' => 'en_US',
            ]),
            'twitter' => array_filter([
                'twitter:card' => $seo?->twitter_card_type ?: 'summary_large_image',
                'twitter:title' => Seo::plain($seo?->twitter_title) ?: $ogTitle,
                'twitter:description' => Seo::plain($seo?->twitter_description) ?: $ogDescription,
                'twitter:image' => Seo::imageUrl($seo?->twitter_image) ?: $image,
            ]),
            'jsonld' => array_values(array_filter([
                self::organization($org),
                $seo?->route_name === 'home' ? self::website() : null,
                self::pageSchema($seo, $sections, $title, $description, $selfUrl, $image),
                self::breadcrumbs($seo, $sections, $selfUrl),
            ])),
        ];
    }

    public static function organizationId(): string
    {
        return Seo::siteUrl().'/#organization';
    }

    public static function encode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    private static function organization(OrganizationSettings $org): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => self::organizationId(),
            'name' => $org->name ?: config('seo.site_name'),
            'url' => Seo::siteUrl().'/',
            'logo' => Seo::imageUrl($org->logo) ?: Seo::imageUrl('images/logo-icon.png'),
            'description' => $org->description,
            'telephone' => $org->telephone,
            'email' => $org->email,
            'sameAs' => array_values(array_filter([
                $org->facebook_url,
                $org->twitter_url,
                $org->linkedin_url,
                $org->youtube_url,
            ])) ?: null,
        ]);
    }

    private static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => Seo::siteUrl().'/#website',
            'url' => Seo::siteUrl().'/',
            'name' => config('seo.site_name'),
            'publisher' => ['@id' => self::organizationId()],
        ];
    }

    private static function pageSchema(?PageSeo $seo, array $sections, string $title, ?string $description, string $url, ?string $image): ?array
    {
        if (filled($sections['page_schema'] ?? null)) {
            return json_decode(htmlspecialchars_decode($sections['page_schema'], ENT_QUOTES), true) ?: null;
        }

        $type = $seo?->schema_type ?: 'WebPage';

        if ($type === 'Service') {
            return array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                '@id' => $url.'#service',
                'name' => $seo?->page_label ?: $title,
                'description' => $description,
                'url' => $url,
                'provider' => ['@id' => self::organizationId()],
            ]);
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $type,
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $title,
            'description' => $description,
            'primaryImageOfPage' => $image,
            'isPartOf' => ['@id' => Seo::siteUrl().'/#website'],
            'publisher' => ['@id' => self::organizationId()],
        ]);
    }

    private static function breadcrumbs(?PageSeo $seo, array $sections, string $url): ?array
    {
        if (filled($sections['breadcrumbs'] ?? null)) {
            $items = json_decode(htmlspecialchars_decode($sections['breadcrumbs'], ENT_QUOTES), true) ?: [];
        } elseif ($seo && $seo->route_name !== 'home') {
            $items = [
                ['name' => 'Home', 'url' => Seo::url('/')],
                ['name' => $seo->page_label ?: $seo->route_name, 'url' => $url],
            ];
        } else {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => Seo::plain($item['name']),
                'item' => Seo::url($item['url']),
            ])->all(),
        ];
    }
}
