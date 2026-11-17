<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'tos_title', 'tos_intro', 'tos_body', 'tos_effective_date',
    'privacy_title', 'privacy_intro', 'privacy_body', 'privacy_effective_date',
    'privacy_policy_url',
    'terms_of_service_url',
    'logo',
    'favicon_path',
    'client_countries',
    'client_map_enabled',
    'description',
    'telephone',
    'email',
    'facebook_url',
    'twitter_url',
    'linkedin_url',
    'youtube_url',
    'google_analytics_id',
    'tracking_mode',
    'google_tag_manager_id',
    'google_site_verification',
    'custom_code_enabled',
    'custom_head_code',
    'custom_body_start_code',
    'custom_body_end_code',
])]
class OrganizationSettings extends Model
{
    protected $attributes = [
        'tracking_mode' => 'ga4',
        'client_map_enabled' => true,
        'custom_code_enabled' => false,
    ];

    protected $casts = [
        'custom_code_enabled' => 'boolean',
        'client_countries' => 'array',
        'client_map_enabled' => 'boolean',
        'tos_effective_date' => 'date',
        'privacy_effective_date' => 'date',
    ];

    /** @return array<string, string> */
    public function clientCountries(): array
    {
        $selected = array_filter($this->client_countries ?? [], fn (mixed $code): bool => is_string($code));

        return array_intersect_key(config('client-map.countries'), array_flip($selected));
    }

    public function faviconUrl(): string
    {
        $path = $this->favicon_path;

        if ($path && str_starts_with($path, 'seo/favicons/') && ! str_contains($path, '..')
            && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['png', 'ico'], true)
            && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('images/logo-icon.png');
    }

    public function googleAnalyticsId(): ?string
    {
        $value = trim((string) $this->google_analytics_id);

        return $this->tracking_mode === 'ga4' && preg_match('/\\AG-[A-Z0-9]{1,98}\\z/', $value) ? $value : null;
    }

    public function googleTagManagerId(): ?string
    {
        $value = trim((string) $this->google_tag_manager_id);

        return $this->tracking_mode === 'gtm' && preg_match('/\\AGTM-[A-Z0-9]{1,96}\\z/', $value) ? $value : null;
    }

    public function googleVerificationToken(): ?string
    {
        $value = trim((string) $this->google_site_verification);

        return preg_match('/\\A[A-Za-z0-9_-]{1,255}\\z/', $value) ? $value : null;
    }

    /** @return array<string, string> */
    public function publicSocialLinks(): array
    {
        return collect([
            'Facebook' => $this->facebook_url, 'X / Twitter' => $this->twitter_url,
            'LinkedIn' => $this->linkedin_url, 'YouTube' => $this->youtube_url,
        ])->filter(fn (?string $url): bool => $this->isPublicUrl($url))->all();
    }

    /** @return array<string, string> */
    public function publicLegalLinks(): array
    {
        return ['Terms & Conditions' => route('tos'), 'Privacy Policy' => route('privacy')];
    }

    /** @return array{title: string, intro: string, body: string, effective_date: string} */
    public function legalPage(string $page): array
    {
        abort_unless(in_array($page, ['tos', 'privacy'], true), 404);

        return collect(config("legal.{$page}"))->map(function (string $default, string $field) use ($page): string {
            $value = $this->getAttribute("{$page}_{$field}");

            return $field === 'effective_date' ? ($value?->format('Y-m-d') ?? $default) : ($value ?? $default);
        })->all();
    }

    private function isPublicUrl(?string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
