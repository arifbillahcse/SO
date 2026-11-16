<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceOffering;
use App\Support\Seo;
use App\Support\SeoHead;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class ServicesController extends Controller
{
    public function index(): View
    {
        $services = Service::where('is_published', true)
            ->with(['offerings' => fn (HasMany $query) => $query->where('is_published', true)->with('portfolioItems')])
            ->orderBy('sort_order')->orderBy('id')->get();
        $groups = $services->map(function (Service $service): array {
            return [
                'key' => $service->catalogue_key ?: $service->slug,
                'title' => $service->title,
                'description' => $service->short_description,
                'icon' => $service->icon,
                'url' => $service->publicUrl(),
                'items' => $service->offerings->map(fn (ServiceOffering $offering): array => [
                    'key' => $offering->slug,
                    'title' => $offering->title,
                    'description' => $offering->description,
                    'scope' => $offering->scope ?? [],
                    'icon' => $offering->icon ?: $service->icon,
                    'featured' => $offering->featured,
                    'availability' => $offering->availability,
                    'primary_action' => ['label' => $offering->action_label ?: 'Discuss Your Project', 'url' => $offering->actionUrl()],
                    'related_project' => $offering->portfolioItems->first(),
                ])->all(),
            ];
        });
        $items = $groups->pluck('items')->flatten(1);
        $projects = $services->flatMap(fn (Service $service) => $service->offerings)
            ->flatMap(fn (ServiceOffering $offering) => $offering->portfolioItems)->unique('id');

        return view('pages.services.index', [
            'serviceGroups' => $groups,
            'serviceCount' => $items->count(),
            'catalogueSchema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                '@id' => Seo::url('/services').'#webpage',
                'url' => Seo::url('/services'),
                'name' => 'Softorio Digital Services',
                'description' => 'Website development, SEO, managed cloud, business automation and diagnostic software services.',
                'publisher' => ['@id' => SeoHead::organizationId()],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => $items->count(),
                    'itemListElement' => $items->values()->map(fn (array $item, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'item' => [
                            '@type' => 'Service',
                            '@id' => Seo::url('/services').'#service-'.$item['key'],
                            'url' => Seo::url('/services').'#service-'.$item['key'],
                            'name' => $item['title'],
                            'description' => $item['description'],
                            'provider' => ['@id' => SeoHead::organizationId()],
                        ],
                    ])->all(),
                ],
            ],
            'selectedProjects' => $projects->take(3),
        ]);
    }
}
