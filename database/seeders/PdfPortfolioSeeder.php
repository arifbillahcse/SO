<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PdfPortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/pdf-portfolio.json')), true, flags: JSON_THROW_ON_ERROR);
        $batches = collect($data['projects'])->chunk(15);

        foreach ($batches as $index => $batch) {
            $added = DB::transaction(function () use ($batch): int {
                $created = 0;

                foreach ($batch as $project) {
                    $attributes = $project['attributes'];
                    $exists = PortfolioItem::where('slug', $attributes['slug'])
                        ->orWhere('website_url', $attributes['website_url'])
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    PortfolioItem::create($attributes);
                    $created++;
                }

                return $created;
            });

            $number = $index + 1;
            $skipped = $batch->count() - $added;
            $this->command?->info("Batch {$number}/{$batches->count()}: {$added} added, {$skipped} already present.");
        }
    }
}
