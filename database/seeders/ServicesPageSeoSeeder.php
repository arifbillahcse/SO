<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesPageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filled = 0;

        DB::transaction(function () use (&$filled): void {
            PageSeo::forRoute('services');
            $seo = PageSeo::where('route_name', 'services')->lockForUpdate()->firstOrFail();
            $title = 'Digital Services for Websites, SEO & Cloud | Softorio';
            $description = 'Explore Softorio digital services for websites, SEO, cloud hosting, business automation and diagnostic software. Discuss the right scope for your project.';

            foreach ([
                'title' => $title,
                'meta_description' => $description,
                'focus_keyword' => 'digital services',
                'secondary_keywords' => ['website development', 'SEO optimization', 'managed cloud services', 'business automation', 'diagnostic management system'],
                'content_notes' => 'Search intent: explore Softorio digital services and choose a project scope. The page groups 30 offerings under five core services. Keep this overview distinct from the individual service pages. Diagnostic software is under development. Related technical portfolio topics are not independently verified customer delivery. Maintain the automatic self-canonical and preserve administrator edits.',
                'og_title' => $title,
                'og_description' => $description,
                'og_image' => config('seo.default_og_image'),
                'twitter_title' => $title,
                'twitter_description' => $description,
                'twitter_image' => config('seo.default_og_image'),
            ] as $field => $value) {
                if (blank($seo->getAttribute($field))) {
                    $seo->setAttribute($field, $value);
                    $filled++;
                }
            }

            if ($seo->isDirty()) {
                $seo->save();
            }
        });

        $this->command?->info("Filled {$filled} empty services-page SEO fields. Existing edits, canonical and indexing settings were preserved.");
    }
}
