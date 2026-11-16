<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortfolioSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/portfolio-seo.json')), true, flags: JSON_THROW_ON_ERROR);
        $updated = 0;
        $filled = 0;
        $missing = 0;

        DB::transaction(function () use ($data, &$updated, &$filled, &$missing): void {
            foreach ($data['projects'] as $draft) {
                $project = PortfolioItem::query()
                    ->where(filled($draft['source_key']) ? 'source_key' : 'slug', $draft['source_key'] ?: $draft['slug'])
                    ->first();

                if (! $project) {
                    $missing++;

                    continue;
                }

                $seo = $project->ensureSeo();
                $seo = $project->seo()->lockForUpdate()->firstOrFail();

                foreach (['title', 'meta_description', 'focus_keyword', 'secondary_keywords', 'content_notes', 'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image'] as $field) {
                    if (filled($seo->getAttribute($field))) {
                        continue;
                    }

                    $value = in_array($field, ['og_image', 'twitter_image'], true)
                        ? ($project->thumbnail_path ?: $draft['fields'][$field])
                        : $draft['fields'][$field];

                    $seo->setAttribute($field, $value);
                    $filled++;
                }

                if ($seo->isDirty()) {
                    $seo->save();
                    $updated++;
                }
            }
        });

        $this->command?->info("Updated {$updated} portfolios and filled {$filled} empty SEO fields. Skipped {$missing} missing projects. Existing values, canonicals and indexing settings were preserved.");
    }
}
