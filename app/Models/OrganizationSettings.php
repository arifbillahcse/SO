<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'logo',
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
        'custom_code_enabled' => false,
    ];

    protected $casts = [
        'custom_code_enabled' => 'boolean',
    ];

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

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
