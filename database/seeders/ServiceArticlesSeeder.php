<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\PortfolioItem;
use App\Support\Seo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/service-articles.json')), true, flags: JSON_THROW_ON_ERROR);
        $projects = PortfolioItem::whereNotNull('source_key')->get()->keyBy('source_key');
        $created = 0;

        DB::transaction(function () use ($data, $projects, &$created): void {
            foreach ($data['articles'] as $index => $entry) {
                $article = Article::where('source_key', $entry['source_key'])->first();
                if (! $article) {
                    if (Article::where('slug', $entry['slug'])->exists()) {
                        $this->command?->warn("Skipped existing article slug: {$entry['slug']}");

                        continue;
                    }
                    $category = ArticleCategory::firstOrCreate(['name' => $entry['category']]);
                    $body = '';
                    foreach ($entry['sections'] as $section) {
                        $body .= '<h2>'.e($section['heading']).'</h2>';
                        foreach ($section['paragraphs'] as $paragraph) {
                            $body .= '<p>'.e($paragraph).'</p>';
                        }
                    }
                    $body .= '<h2>Explore related Softorio services</h2><p>Discuss your requirements through our <a href="'.e(Seo::routePath($entry['service_route'])).'">related service</a> or <a href="/services">browse all services</a>.</p>';
                    $references = collect($entry['portfolio_keys'])->map(fn (string $key): ?PortfolioItem => $projects->get($key))->filter();
                    if ($references->isNotEmpty()) {
                        $body .= '<p>Explore these related portfolio topics for additional context:</p><ul>';
                        foreach ($references as $reference) {
                            $body .= '<li><a href="'.e($reference->seoPath()).'">'.e($reference->title).'</a></li>';
                        }
                        $body .= '</ul>';
                    }
                    $body .= '<h2>Official references</h2><p>Check the documentation for the product and version you use:</p><ul>';
                    foreach ($entry['sources'] as [$label, $url]) {
                        $body .= '<li><a href="'.e($url).'">'.e($label).'</a></li>';
                    }
                    $body .= '</ul>';
                    $article = Article::create([
                        'source_key' => $entry['source_key'], 'title' => $entry['title'], 'slug' => $entry['slug'],
                        'article_category_id' => $category->id, 'excerpt' => $entry['excerpt'], 'body' => $body,
                        'read_minutes' => max(1, (int) ceil(Seo::wordCount($body) / 200)),
                        'featured' => $index === 0 && ! Article::where('featured', true)->exists(),
                        'published_at' => now()->subMinutes($index),
                    ]);
                    $created++;
                }
                $seo = $article->ensureSeo();
                foreach ([
                    'title' => $entry['seo_title'], 'meta_description' => $entry['excerpt'],
                    'focus_keyword' => $entry['focus_keyword'], 'secondary_keywords' => $entry['secondary_keywords'],
                    'content_notes' => 'Original practical guide based on Softorio service and portfolio topics. No invented client outcomes. Official references are included in the article. Review periodically when providers or workflows change.',
                    'og_title' => $entry['seo_title'], 'og_description' => $entry['excerpt'],
                    'og_image' => config('seo.default_og_image'),
                    'twitter_title' => $entry['seo_title'], 'twitter_description' => $entry['excerpt'],
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
        });

        $this->command?->info("Created {$created} articles. Existing articles, categories and saved SEO edits were preserved.");
    }
}
