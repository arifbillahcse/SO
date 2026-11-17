<?php

namespace App\Providers;

use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\TeamMember;
use App\Services\Seo\NullSearchPerformance;
use App\Services\Seo\SearchPerformance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

        View::composer(['pages.home', 'pages.affiliate-program', 'pages.services.*', 'pages.solutions.*'], function ($view): void {
            $organization = OrganizationSettings::current();
            $view->with('siteContact', ContactPageSettings::current()->publicDetails($organization));
            $view->with('contactServiceOptions', Service::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get(['id', 'title', 'route_name']));
        });

        View::composer('pages.services.web-development', function ($view): void {
            $view->with('webDevelopmentProjects', PortfolioItem::whereIn('category', ['wordpress', 'ecommerce', 'web-development'])
                ->orderBy('sort_order')->orderBy('id')->limit(4)->get());
        });

        View::composer('pages.about-us', function ($view): void {
            $founder = TeamMember::orderBy('sort_order')->orderBy('id')->first();
            $photo = $founder?->photo_path;
            $view->with('aboutFounder', $founder);
            $view->with('aboutFounderPhoto', $photo
                ? (str_starts_with($photo, 'images/') ? asset($photo) : Storage::disk('public')->url($photo))
                : asset('images/arif.png'));
        });

        View::composer('partials.client-map', function ($view): void {
            $organization = OrganizationSettings::current();
            $view->with('clientMapEnabled', $organization->client_map_enabled);
            $view->with('clientCountries', $organization->clientCountries());
        });

        View::composer('partials.whatsapp-chat', function ($view): void {
            $settings = ContactPageSettings::current();
            $view->with('whatsappChatUrl', $settings->whatsapp_chat_enabled ? $settings->whatsappUrl() : null);
        });

        View::composer('partials.footer', function ($view): void {
            $organization = OrganizationSettings::current();
            $view->with('orgSettings', $organization);
            $view->with('footerContact', ContactPageSettings::current()->publicDetails($organization));
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
