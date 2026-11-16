<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageTestimonialDraftsSeeder extends Seeder
{
    public function run(): void
    {
        $drafts = [
            [
                'source_key' => 'softorio-homepage-draft-website',
                'name' => 'Website client - draft',
                'text' => 'Draft wording for review: "The website is easy to navigate on mobile and desktop. The team explained the design decisions clearly and made the handover straightforward for our staff."',
            ],
            [
                'source_key' => 'softorio-homepage-draft-cloud',
                'name' => 'Cloud services client - draft',
                'text' => 'Draft wording for review: "We appreciated having our hosting, DNS and backup requirements explained in one place. The setup and handover gave our team a clearer understanding of how to manage the website."',
            ],
            [
                'source_key' => 'softorio-homepage-draft-seo',
                'name' => 'SEO client - draft',
                'text' => 'Draft wording for review: "The review helped us understand which pages needed clearer titles, descriptions and content. We valued the practical explanations and the organised plan for ongoing improvements."',
            ],
        ];
        $created = 0;

        DB::transaction(function () use ($drafts, &$created): void {
            foreach ($drafts as $index => $draft) {
                $testimonial = Testimonial::firstOrCreate(['source_key' => $draft['source_key']], $draft + [
                    'role' => 'Unverified draft - replace with customer role / company',
                    'stars' => 5,
                    'featured' => false,
                    'sort_order' => 100 + $index,
                ]);
                $created += $testimonial->wasRecentlyCreated ? 1 : 0;
            }
        });

        $this->command?->info("Created {$created} testimonial drafts. No reviews were published. Existing content, publication settings and ordering were preserved.");
    }
}
