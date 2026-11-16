<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\ServiceOffering;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/service-catalogue.json')), true, flags: JSON_THROW_ON_ERROR);
        $icons = ['website-development' => 'code', 'seo-optimization' => 'search', 'managed-cloud' => 'cloud', 'business-automation' => 'gear', 'diagnostic-management' => 'gear'];
        $projects = PortfolioItem::all(['id', 'source_key', 'slug']);
        $bySource = $projects->whereNotNull('source_key')->keyBy('source_key');
        $bySlug = $projects->keyBy('slug');
        $createdGroups = 0;
        $createdOfferings = 0;

        DB::transaction(function () use ($data, $icons, $bySource, $bySlug, &$createdGroups, &$createdOfferings): void {
            foreach ($data['groups'] as $groupIndex => $group) {
                $service = Service::where('catalogue_key', $group['key'])->first()
                    ?? Service::firstOrCreate(['route_name' => $group['route_name']], [
                        'catalogue_key' => $group['key'],
                        'slug' => $group['route_name'],
                        'title' => $group['title'],
                        'short_description' => $group['description'],
                        'features' => collect($group['items'])->take(3)->pluck('title')->all(),
                        'icon' => $icons[$group['key']],
                        'is_published' => true,
                        'sort_order' => $groupIndex + 1,
                    ]);
                $createdGroups += $service->wasRecentlyCreated ? 1 : 0;

                if (blank($service->catalogue_key)) {
                    $service->update(['catalogue_key' => $group['key']]);
                }

                foreach ($group['items'] as $itemIndex => $item) {
                    $offering = ServiceOffering::firstOrCreate(['catalogue_key' => $item['key']], [
                        'service_id' => $service->id,
                        'slug' => $item['key'],
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'scope' => $item['scope'],
                        'icon' => $icons[$group['key']],
                        'action_label' => $item['primary_action']['label'],
                        'action_route' => $item['primary_action']['route_name'],
                        'action_fragment' => $item['primary_action']['fragment'] ?? null,
                        'availability' => $item['availability'] ?? null,
                        'is_published' => true,
                        'sort_order' => $itemIndex + 1,
                    ]);

                    if (! $offering->wasRecentlyCreated) {
                        continue;
                    }

                    $createdOfferings++;
                    $related = collect($item['related_source_keys'])
                        ->map(fn (string $key): ?PortfolioItem => $bySource->get($key))
                        ->merge(collect($item['related_website_slugs'] ?? [])
                            ->map(fn (string $slug): ?PortfolioItem => $bySlug->get($slug)))
                        ->filter()->unique('id')->values();
                    $offering->portfolioItems()->sync($related->mapWithKeys(
                        fn (PortfolioItem $project, int $index): array => [$project->id => ['sort_order' => $index + 1]]
                    )->all());
                }
            }
        });

        $this->command?->info("Created {$createdGroups} core groups and {$createdOfferings} service offerings. Existing content, publishing choices and portfolio selections were preserved.");
    }
}
