<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'route_name',
        'icon',
        'short_description',
        'sort_order',
    ];
}
