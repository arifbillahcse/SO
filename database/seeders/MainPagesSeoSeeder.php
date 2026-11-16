<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainPagesSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(ServicesPageSeoSeeder::class);
        $data = json_decode(file_get_contents(database_path('seeders/data/main-pages-seo.json')), true, flags: JSON_THROW_ON_ERROR);
        $updated = 0;
        $filled = 0;

        DB::transaction(function () use ($data, &$updated, &$filled): void {
            foreach ($data['pages'] as $page) {
                PageSeo::forRoute($page['route_name']);
                $seo = PageSeo::where('route_name', $page['route_name'])->lockForUpdate()->firstOrFail();
                $fields = array_intersect_key($page, array_flip(['title', 'meta_description', 'focus_keyword', 'secondary_keywords', 'content_notes']));
                $fields += [
                    'og_title' => $seo->title ?: $page['title'],
                    'og_description' => $seo->meta_description ?: $page['meta_description'],
                    'og_image' => config('seo.default_og_image'),
                    'twitter_title' => $seo->og_title ?: ($seo->title ?: $page['title']),
                    'twitter_description' => $seo->og_description ?: ($seo->meta_description ?: $page['meta_description']),
                    'twitter_image' => $seo->og_image ?: config('seo.default_og_image'),
                ];

                foreach ($fields as $field => $value) {
                    if (blank($seo->getAttribute($field))) {
                        $seo->setAttribute($field, $value);
                        $filled++;
                    }
                }

                if ($seo->isDirty()) {
                    $seo->save();
                    $updated++;
                }
            }
        });

        $this->command?->info("Updated {$updated} main pages and filled {$filled} empty SEO fields. Saved edits, canonical, schema and indexing settings were preserved.");
    }
}
