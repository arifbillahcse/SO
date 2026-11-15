<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotFoundLog extends Model
{
    protected $fillable = ['path'];

    protected function casts(): array
    {
        return [
            'last_hit_at' => 'datetime',
        ];
    }

    public static function recordHit(string $path, ?string $referrer): void
    {
        $path = '/'.ltrim($path, '/');

        $log = static::firstOrNew(['path' => $path]);
        $log->referrer = $referrer;
        $log->hits = ($log->hits ?? 0) + 1;
        $log->last_hit_at = now();
        $log->save();
    }
}
