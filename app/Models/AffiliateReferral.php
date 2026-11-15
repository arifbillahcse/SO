<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateReferral extends Model
{
    protected $fillable = [
        'referrer_name',
        'referrer_email',
        'referrer_phone',
        'message',
        'status',
    ];
}
