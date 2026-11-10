<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    public static function siteUrl(): string
    {
        return config('seo.site_url');
    }

    /**
     * Absolute URL on the canonical origin for a site-relative path.
     */
    public static function url(string $path): string
    {
        $path = '/'.ltrim(parse_url($path, PHP_URL_PATH) ?? '', '/');

        return static::siteUrl().($path === '/' ? '/' : rtrim($path, '/'));
    }

    public static function routePath(string $routeName, mixed $parameters = []): string
    {
        return route($routeName, $parameters, false);
    }

    /**
     * Section content from @section('x', '...') arrives HTML-escaped, and some
     * templates also hand-escape (&amp;). Normalise to plain text before it is
     * echoed once with {{ }}, so Google never sees "&amp;amp;".
     */
    public static function plain(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $value) {
                break;
            }
            $value = $decoded;
        }

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * Trim text to a length at a word boundary, for generated descriptions.
     */
    public static function excerpt(?string $text, int $limit = 155): ?string
    {
        $text = static::plain(strip_tags((string) $text));

        if ($text === null || $text === '') {
            return null;
        }

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit - 1);
        $cut = Str::beforeLast($cut, ' ');

        return rtrim($cut, ' ,;:—-').'…';
    }

    public static function wordCount(?string $html): int
    {
        $text = static::plain(strip_tags((string) $html));

        return $text === null || $text === '' ? 0 : count(preg_split('/\s+/u', $text));
    }

    /**
     * Absolute URL for an image stored on the public disk or under public/.
     */
    public static function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $public = Str::startsWith($path, 'images/') ? '/'.$path : '/storage/'.ltrim($path, '/');

        return static::siteUrl().$public;
    }
}
