<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PdfPortfolioMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/pdf-portfolio-media.json')), true, flags: JSON_THROW_ON_ERROR);
        $updated = 0;

        foreach ($data['projects'] as $media) {
            if (! Storage::disk('public')->exists($media['thumbnail_path'])) {
                continue;
            }

            $project = PortfolioItem::where('slug', $media['slug'])
                ->orWhere('website_url', $media['website_url'])
                ->first();

            if (! $project || filled($project->thumbnail_path)) {
                continue;
            }

            $project->update(['thumbnail_path' => $media['thumbnail_path']]);
            $updated++;
        }

        $this->command?->info("Attached {$updated} portfolio images. Existing thumbnails were preserved.");
    }
}
