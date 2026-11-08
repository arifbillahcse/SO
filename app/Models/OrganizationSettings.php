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
])]
class OrganizationSettings extends Model
{
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
