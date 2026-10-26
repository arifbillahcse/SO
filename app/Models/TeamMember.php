<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo_path',
        'credentials',
        'skills',
        'sort_order',
    ];

    protected $casts = [
        'credentials' => 'array',
        'skills' => 'array',
    ];
}
