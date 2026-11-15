<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'short_role',
        'bio',
        'photo_path',
        'email',
        'website_url',
        'credentials',
        'credentials_heading',
        'skills',
        'stats',
        'sort_order',
    ];

    protected $casts = [
        'credentials' => 'array',
        'skills' => 'array',
        'stats' => 'array',
    ];
}
