<?php

use App\Http\Controllers\AffiliateReferralController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OurFoundersController;
use App\Http\Controllers\OurResourcesController;
use App\Http\Controllers\OurWorksController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Temporary diagnostic route — remove once SQLite driver issue is confirmed fixed.
// Skips session/cookie middleware since those also hit the (currently broken) DB connection.
Route::get('/__diag', function () {
    return response()->json([
        'pdo_drivers' => \PDO::getAvailableDrivers(),
        'pdo_sqlite_loaded' => extension_loaded('pdo_sqlite'),
        'sqlite3_loaded' => extension_loaded('sqlite3'),
        'php_version' => PHP_VERSION,
        'php_ini_loaded_file' => php_ini_loaded_file(),
        'loaded_extensions' => get_loaded_extensions(),
    ]);
})->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
]);

// Services
Route::get('/web-development', fn () => view('pages.services.web-development'))->name('web-development');
Route::get('/seo', fn () => view('pages.services.seo'))->name('seo');
Route::get('/manage-cloud', fn () => view('pages.services.manage-cloud'))->name('manage-cloud');
Route::get('/automation', fn () => view('pages.services.automation'))->name('automation');

// Solutions
Route::get('/ecommerce-solution', fn () => view('pages.solutions.ecommerce-solution'))->name('ecommerce-solution');
Route::get('/cloud-solution', fn () => view('pages.solutions.cloud-solution'))->name('cloud-solution');
Route::get('/automation-solution', fn () => view('pages.solutions.automation-solution'))->name('automation-solution');

// Resources
Route::get('/our-works', [OurWorksController::class, 'index'])->name('our-works');
Route::get('/about-us', fn () => view('pages.about-us'))->name('about-us');
Route::get('/case-study', [CaseStudyController::class, 'index'])->name('case-study');
Route::get('/our-resources', [OurResourcesController::class, 'index'])->name('our-resources');
Route::get('/affiliate-program', fn () => view('pages.affiliate-program'))->name('affiliate-program');

// Hidden page — intentionally not linked in nav/footer, matches noindex,nofollow from the static site
Route::get('/our-founders', [OurFoundersController::class, 'index'])->name('our-founders');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Form submissions — throttled to 5 attempts per minute per IP to deter spam/abuse
Route::post('/contact', [ContactSubmissionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::post('/affiliate-referral', [AffiliateReferralController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('affiliate-referral.store');
