<?php

namespace App\Providers;

use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use App\Services\Seo\NullSearchPerformance;
use App\Services\Seo\SearchPerformance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SearchPerformance::class, NullSearchPerformance::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('pageSeo', $this->currentPageSeo());
            $view->with('orgSettings', OrganizationSettings::current());
        });

        View::composer('partials.footer', function ($view): void {
            $view->with('orgSettings', OrganizationSettings::current());
        });
    }

    /**
     * The SEO registry row for the page being rendered: the bound content
     * record's row on detail pages, otherwise the static route's row.
     */
    private function currentPageSeo(): ?PageSeo
    {
        $route = request()->route();

        if (! $route) {
            return null;
        }

        foreach ($route->parameters() as $parameter) {
            if ($parameter instanceof Model && method_exists($parameter, 'ensureSeo')) {
                return $parameter->ensureSeo();
            }
        }

        return $route->getName() ? PageSeo::forRoute($route->getName()) : null;
    }
}
