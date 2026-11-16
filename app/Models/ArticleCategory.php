<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    protected static function booted(): void
    {
        static::creating(function (ArticleCategory $category): void {
            if (blank($category->slug)) {
                $base = Str::slug($category->name) ?: 'category';
                $slug = $base;
                $suffix = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$suffix++;
                }
                $category->slug = $slug;
            }
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
