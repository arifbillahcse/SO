<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Testimonial $testimonial): void {
            if (blank($testimonial->avatar_initial)) {
                $testimonial->avatar_initial = Str::upper(collect(preg_split('/\s+/u', trim($testimonial->name)))
                    ->filter()->take(2)->map(fn (string $word): string => mb_substr($word, 0, 1))->implode('')) ?: 'S';
            }
        });
    }

    protected $fillable = [
        'name',
        'role',
        'avatar_initial',
        'stars',
        'text',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'stars' => 'integer',
    ];
}
