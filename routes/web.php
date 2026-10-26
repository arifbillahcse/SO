<?php

use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OurResourcesController;
use App\Http\Controllers\OurWorksController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
Route::get('/our-founders', fn () => view('pages.our-founders'))->name('our-founders');
