<?php

namespace App\Services;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\CaseStudyResource;
use App\Filament\Resources\OrganizationSettingsResource;
use App\Filament\Resources\PageSeoResource;
use App\Filament\Resources\PortfolioItemResource;
use App\Filament\Resources\TeamMemberResource;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use App\Models\TeamMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaLibrary
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico'];

    /**
     * @return Collection<int, array{key: string, source: string, path: string, name: string, url: string, exists: bool|null, size: int|null, dimensions: string|null, uses: array<int, array{label: string, url: string|null}>}>
     */
    public function images(): Collection
    {
        $images = [];
        foreach (Storage::disk('public')->allFiles() as $path) {
            if (! str_starts_with($path, 'livewire-tmp/')) {
                $this->add($images, $path);
            }
        }
        if (is_dir(public_path('images'))) {
            foreach (File::allFiles(public_path('images')) as $file) {
                $this->add($images, 'images/'.str_replace('\\', '/', $file->getRelativePathname()));
            }
        }
        if (is_file(public_path('favicon.ico'))) {
            $this->add($images, '/favicon.ico');
        }

        foreach (Article::without('articleCategory')->get(['id', 'title', 'slug', 'featured_image_path', 'body']) as $article) {
            $url = ArticleResource::getUrl('edit', ['record' => $article]);
            $this->add($images, $article->featured_image_path, 'Article: '.$article->title.' ? Featured image', $url);
            $this->content($images, $article->body, 'Article: '.$article->title.' ? Body', $url);
        }
        foreach (PortfolioItem::get(['id', 'title', 'slug', 'thumbnail_path', 'body']) as $project) {
            $url = PortfolioItemResource::getUrl('edit', ['record' => $project]);
            $this->add($images, $project->thumbnail_path, 'Portfolio: '.$project->title.' ? Thumbnail', $url);
            $this->content($images, $project->body, 'Portfolio: '.$project->title.' ? Body', $url);
        }
        foreach (CaseStudy::get(['id', 'title', 'slug', 'body']) as $study) {
            $this->content($images, $study->body, 'Case study: '.$study->title.' ? Body', CaseStudyResource::getUrl('edit', ['record' => $study]));
        }
        foreach (TeamMember::get(['id', 'name', 'photo_path', 'bio']) as $member) {
            $url = TeamMemberResource::getUrl('edit', ['record' => $member]);
            $this->add($images, $member->photo_path, 'Team: '.$member->name.' ? Photo', $url);
            $this->content($images, $member->bio, 'Team: '.$member->name.' ? Biography', $url);
        }
        foreach (PageSeo::get(['id', 'page_label', 'og_image', 'twitter_image']) as $seo) {
            $url = PageSeoResource::getUrl('edit', ['record' => $seo]);
            $this->add($images, $seo->og_image, 'SEO: '.$seo->page_label.' ? Open Graph', $url);
            $this->add($images, $seo->twitter_image, 'SEO: '.$seo->page_label.' ? X / Twitter', $url);
        }
        foreach (OrganizationSettings::all() as $organization) {
            $url = OrganizationSettingsResource::getUrl('edit', ['record' => $organization]);
            $this->add($images, $organization->logo, 'Organization ? Logo', $url);
            $this->add($images, $organization->favicon_path, 'Organization ? Favicon', $url);
            foreach (['custom_head_code', 'custom_body_start_code', 'custom_body_end_code'] as $field) {
                $this->content($images, $organization->{$field}, 'Organization ? Saved custom code', $url);
            }
        }
        $this->add($images, config('seo.default_og_image'), 'Website default ? Social sharing image');
        foreach (['views', 'css', 'js'] as $directory) {
            if (! is_dir(resource_path($directory))) {
                continue;
            }
            foreach (File::allFiles(resource_path($directory)) as $file) {
                if (! in_array($file->getExtension(), ['php', 'css', 'js'], true) || str_starts_with($file->getRelativePathname(), 'filament')) {
                    continue;
                }
                preg_match_all('~["\']([^"\'\r\n]+\.(?:jpe?g|png|gif|webp|avif|svg|ico)(?:\?[^"\']*)?)["\']~i', $file->getContents(), $matches);
                foreach ($matches[1] as $path) {
                    if (! str_contains($path, '{{') && ! str_contains($path, '$')) {
                        $this->add($images, $path, 'Website source ? '.$directory.'/'.str_replace('\\', '/', $file->getRelativePathname()));
                    }
                }
            }
        }

        return collect($images)->map(function (array $image): array {
            $image['uses'] = array_values($image['uses']);

            return $image;
        })->sortBy(fn (array $image): string => strtolower($image['name']).$image['key'])->values();
    }

    /** @param array<string, array<string, mixed>> $images */
    private function content(array &$images, ?string $html, string $label, ?string $url = null): void
    {
        if (blank($html)) {
            return;
        }
        preg_match_all('~<(?:img|source)\b[^>]*>~is', $html, $nodes);
        foreach ($nodes[0] as $node) {
            preg_match_all('~\b(src|data-id|srcset)\s*=\s*(["\'])(.*?)\2~is', $node, $attributes, PREG_SET_ORDER);
            $hasId = collect($attributes)->contains(fn (array $attribute): bool => strtolower($attribute[1]) === 'data-id' && $this->normalize($attribute[3]) !== null);
            foreach ($attributes as $attribute) {
                if ($hasId && strtolower($attribute[1]) === 'src') {
                    continue;
                }
                if (strtolower($attribute[1]) === 'srcset') {
                    foreach (explode(',', $attribute[3]) as $candidate) {
                        $this->add($images, preg_split('/\s+/', trim($candidate))[0] ?? '', $label, $url);
                    }
                } else {
                    $this->add($images, $attribute[3], $label, $url);
                }
            }
        }
        preg_match_all('~url\(\s*(["\']?)([^)"\']+)\1\s*\)~i', $html, $backgrounds);
        foreach ($backgrounds[2] as $path) {
            $this->add($images, trim($path), $label, $url);
        }
    }

    /** @param array<string, array<string, mixed>> $images */
    private function add(array &$images, ?string $value, ?string $label = null, ?string $usageUrl = null): void
    {
        $reference = $this->normalize($value);
        if (! $reference) {
            return;
        }
        [$source, $path] = $reference;
        $key = $source.':'.$path;
        if (! isset($images[$key])) {
            $local = $source === 'assets' ? public_path($path) : null;
            $exists = match ($source) {
                'uploads' => Storage::disk('public')->exists($path),
                'assets' => is_file($local),
                default => null,
            };
            $size = null;
            $dimensions = null;
            if ($exists) {
                $size = $source === 'uploads' ? Storage::disk('public')->size($path) : filesize($local);
                if ($source === 'uploads' && config('filesystems.disks.public.driver') === 'local') {
                    $local = Storage::disk('public')->path($path);
                }
                if ($local && is_file($local) && ($info = @getimagesize($local))) {
                    $dimensions = $info[0].' ? '.$info[1];
                }
            }
            $images[$key] = [
                'key' => $key, 'source' => $source, 'path' => $path,
                'name' => basename(parse_url($path, PHP_URL_PATH) ?: $path),
                'url' => match ($source) {
                    'uploads' => Storage::disk('public')->url($path),
                    'assets' => asset($path),
                    default => $path,
                },
                'exists' => $exists, 'size' => $size, 'dimensions' => $dimensions, 'uses' => [],
            ];
        }
        if ($label) {
            $images[$key]['uses'][$label.'|'.$usageUrl] = ['label' => $label, 'url' => $usageUrl];
        }
    }

    /** @return array{string, string}|null */
    private function normalize(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }
        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (str_starts_with($value, '//')) {
            $value = 'https:'.$value;
        }
        $parsed = parse_url($value);
        if ($parsed === false || isset($parsed['scheme']) && ! in_array(strtolower($parsed['scheme']), ['http', 'https'], true)) {
            return null;
        }
        $path = rawurldecode($parsed['path'] ?? '');
        if (str_contains($path, "\0") || str_contains($path, '\\') || in_array('..', explode('/', $path), true)) {
            return null;
        }
        $hosts = array_filter([
            parse_url(config('app.url'), PHP_URL_HOST), parse_url(config('seo.site_url'), PHP_URL_HOST),
            request()->getHost(), 'localhost', '127.0.0.1',
        ]);
        if (isset($parsed['host']) && ! in_array(strtolower($parsed['host']), array_map('strtolower', $hosts), true)) {
            return ['external', $value];
        }
        if (! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
            return null;
        }
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            return ['uploads', substr($path, 8)];
        }

        return [str_starts_with($path, 'images/') || $path === 'favicon.ico' || str_starts_with($path, 'build/') ? 'assets' : 'uploads', $path];
    }
}
