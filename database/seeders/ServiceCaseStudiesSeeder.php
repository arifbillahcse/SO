<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use App\Support\Seo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCaseStudiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/service-case-studies.json')), true, flags: JSON_THROW_ON_ERROR);
        $additional = json_decode(file_get_contents(database_path('seeders/data/service-case-studies-additional.json')), true, flags: JSON_THROW_ON_ERROR);
        $data['studies'] = array_merge($data['studies'], $additional['studies']);
        $projects = PortfolioItem::whereNotNull('source_key')->get()->keyBy('source_key');
        $created = 0;

        DB::transaction(function () use ($data, $projects, &$created): void {
            foreach ($data['studies'] as $index => $study) {
                $body = '';
                foreach ($study['sections'] as $section) {
                    $body .= '<h2>'.e($section['heading']).'</h2>';
                    foreach ($section['paragraphs'] as $paragraph) {
                        $body .= '<p>'.e($paragraph).'</p>';
                    }
                }
                $body .= '<h2>Related services and technical topics</h2><p>Explore the <a href="'.e(Seo::routePath($study['service_route'])).'">related Softorio service</a> or <a href="/services">browse all services</a>.</p>';
                $references = collect($study['portfolio_keys'])->map(fn (string $key): ?PortfolioItem => $projects->get($key))->filter();
                if ($references->isNotEmpty()) {
                    $body .= '<p>These portfolio topics provide technical context. They do not establish the assumptions or results of this illustrative scenario.</p><ul>';
                    foreach ($references as $reference) {
                        $body .= '<li><a href="'.e($reference->seoPath()).'">'.e($reference->title).'</a> — related technical topic.</li>';
                    }
                    $body .= '</ul>';
                }

                $case = CaseStudy::firstOrCreate(['source_key' => $study['source_key']], [
                    'study_type' => 'illustrative', 'title' => $study['title'], 'slug' => $study['slug'],
                    'client' => null, 'tags' => $study['tags'], 'description' => $study['description'],
                    'body' => $body, 'metrics' => $study['metrics'],
                    'featured' => $index === 0 && ! CaseStudy::where('featured', true)->exists(),
                    'sort_order' => 100 + $index,
                ]);
                $created += $case->wasRecentlyCreated ? 1 : 0;
                $seo = $case->ensureSeo();
                foreach ([
                    'title' => $study['title'].' | Softorio', 'meta_description' => $study['description'],
                    'focus_keyword' => $study['focus_keyword'], 'secondary_keywords' => $study['secondary_keywords'],
                    'content_notes' => 'Illustrative planning scenario. Figures are stated assumptions or modelled calculations, not measured client outcomes. No named client is attributed. Related portfolio topics establish subject relevance only. Preserve the disclosure and replace assumptions with documented measurements before reporting actual results.',
                    'og_title' => $study['title'].' | Softorio', 'og_description' => $study['description'],
                    'og_image' => config('seo.default_og_image'),
                    'twitter_title' => $study['title'].' | Softorio', 'twitter_description' => $study['description'],
                    'twitter_image' => config('seo.default_og_image'),
                ] as $field => $value) {
                    if (blank($seo->getAttribute($field))) {
                        $seo->setAttribute($field, $value);
                    }
                }
                if ($seo->isDirty()) {
                    $seo->save();
                }
            }

            $listing = PageSeo::forRoute('case-study');
            $previous = "Follow Softorio's case study collection as project write-ups are added. Discuss your website, hosting or automation requirements directly with our team.";
            $description = 'Explore Softorio case studies and illustrative scenarios for websites, SEO, cloud hosting and automation, with clearly labelled assumptions and project approaches.';
            foreach (['meta_description', 'og_description', 'twitter_description'] as $field) {
                if (blank($listing->getAttribute($field)) || $listing->getAttribute($field) === $previous) {
                    $listing->setAttribute($field, $description);
                }
            }
            if ($listing->isDirty()) {
                $listing->save();
            }
            if (in_array($listing->content_notes, [
                'Case study collection intent. The local case study table is currently empty. Do not promise existing measured results or verified success stories. Review metadata as substantive case studies are published. Preserve current index settings.',
                'Case study collection intent. Includes five service-based illustrative scenarios with explicit assumptions and modelled arithmetic. Keep these distinct from documented client engagements. Do not present assumed figures as measured results. Preserve administrator edits and current indexing settings.',
            ], true)) {
                $listing->update(['content_notes' => 'Case study collection intent. Includes service-based illustrative scenarios with explicit assumptions and modelled arithmetic. Keep these distinct from documented client engagements. Do not present assumed figures as measured results. Preserve administrator edits and current indexing settings.']);
            }
        });

        $this->command?->info("Created {$created} illustrative case studies. Existing content and SEO edits were preserved.");
    }
}
