<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Services\Seo\SeoAuditor;
use Illuminate\Database\Seeder;

class CloudPortfolioContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/cloud-portfolio-editorial.json')), true, flags: JSON_THROW_ON_ERROR);
        $updated = 0;

        foreach ($data['projects'] as $editorial) {
            $project = PortfolioItem::where('source_key', $editorial['source_key'])->first();

            if (! $project) {
                continue;
            }

            foreach (['body', 'description'] as $field) {
                if ($project->getAttribute($field) === $editorial['previous_'.$field]) {
                    $project->setAttribute($field, $editorial[$field]);
                }
            }

            if ($project->isDirty()) {
                $project->save();
                $updated++;
            }

            $seo = $project->ensureSeo();

            if (blank($seo->focus_keyword)) {
                $seo->update(['focus_keyword' => $editorial['focus_keyword']]);
            }

            app(SeoAuditor::class)->scan($seo);
        }

        $this->command?->info("Updated {$updated} original demonstration write-ups. Administrator edits and SEO overrides were preserved.");
    }
}
