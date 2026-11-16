<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CloudPortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/cloud-portfolio.json')), true, flags: JSON_THROW_ON_ERROR);
        $batches = collect($data['projects'])->chunk(16);

        foreach ($batches as $index => $batch) {
            $added = DB::transaction(function () use ($batch): int {
                $created = 0;

                foreach ($batch as $project) {
                    $attributes = $project['attributes'];

                    if (PortfolioItem::where('source_key', $attributes['source_key'])->exists()) {
                        continue;
                    }

                    if (PortfolioItem::where('slug', $attributes['slug'])->exists()) {
                        $attributes['slug'] = null;
                    }

                    $record = PortfolioItem::firstOrCreate(['source_key' => $attributes['source_key']], $attributes);
                    $created += $record->wasRecentlyCreated ? 1 : 0;
                }

                return $created;
            });

            $number = $index + 1;
            $skipped = $batch->count() - $added;
            $this->command?->info("Cloud batch {$number}/{$batches->count()}: {$added} added, {$skipped} already present.");
        }
    }
}
