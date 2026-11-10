<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keeps SEO scores current after template deployments. Runs only if cPanel has
// a cron job for `php artisan schedule:run` (see DEPLOYMENT.md); optional.
Schedule::command('seo:scan')->dailyAt('02:41');
