<?php

namespace App\Models;

use Database\Factories\ClientBrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClientBrand extends Model
{
    /** @use HasFactory<ClientBrandFactory> */
    use HasFactory;

    protected $fillable = ['name', 'logo_path', 'is_published', 'sort_order'];

    protected $attributes = ['is_published' => true, 'sort_order' => 0];

    protected $casts = ['is_published' => 'boolean', 'sort_order' => 'integer'];

    public function logoUrl(): ?string
    {
        $path = $this->logo_path;

        if (! $path || ! str_starts_with($path, 'brands/') || str_contains($path, '..')
            || ! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'webp'], true)
            || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
