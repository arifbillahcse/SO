<?php

namespace App\Providers;

use App\Models\PageSeo;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $routeName = request()->route()?->getName();

            $view->with('pageSeo', $routeName ? PageSeo::where('route_name', $routeName)->first() : null);
        });
    }
}
