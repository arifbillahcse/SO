<?php

namespace Tests\Feature;

use App\Models\PageSeo;
use Database\Seeders\MainPagesSeoSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MainPagesSeoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_all_main_pages_have_saved_metadata_that_renders_with_valid_schema_and_existing_indexing_rules(): void
    {
        $this->seed(MainPagesSeoSeeder::class);
        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();
        $titles = [];
        $descriptions = [];

        foreach (PageSeo::whereNotNull('route_name')->get() as $seo) {
            $response = $this->get(route($seo->route_name))->assertOk();
            $document = new DOMDocument;
            libxml_use_internal_errors(true);
            $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
            libxml_clear_errors();
            $xpath = new DOMXPath($document);

            $this->assertSame([], $seo->missingFields());
            $this->assertNotEmpty($seo->secondary_keywords);
            $this->assertNotEmpty($seo->content_notes);
            $this->assertSame($seo->title, $xpath->query('//title')->item(0)->textContent);
            $this->assertSame($seo->meta_description, $xpath->query('//meta[@name="description"]')->item(0)->getAttribute('content'));
            $this->assertSame($seo->og_title, $xpath->query('//meta[@property="og:title"]')->item(0)->getAttribute('content'));
            $this->assertSame($seo->twitter_description, $xpath->query('//meta[@name="twitter:description"]')->item(0)->getAttribute('content'));
            $this->assertSame('https://softorio.com/images/og-default.png', $xpath->query('//meta[@property="og:image"]')->item(0)->getAttribute('content'));
            $this->assertSame(1, $xpath->query('//h1')->length);
            $this->assertSame($seo->selfUrl(), $xpath->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
            foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
                $schema = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
                $this->assertArrayHasKey('@type', $schema);
            }
            if ($seo->route_name === 'our-founders') {
                $this->assertSame('noindex, nofollow', $xpath->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
                $this->assertStringNotContainsString('<loc>'.$seo->selfUrl().'</loc>', $sitemap);
            } else {
                $this->assertSame('index, follow', $xpath->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
                $this->assertStringContainsString('<loc>'.$seo->selfUrl().'</loc>', $sitemap);
            }
            $titles[] = $seo->title;
            $descriptions[] = $seo->meta_description;
        }

        $this->assertCount(16, array_unique($titles));
        $this->assertCount(16, array_unique($descriptions));
    }

    public function test_import_preserves_admin_edits_settings_and_other_page_types_and_is_safe_to_repeat(): void
    {
        $home = PageSeo::forRoute('home');
        $home->update([
            'title' => 'My custom homepage title',
            'meta_description' => 'My custom homepage description',
            'secondary_keywords' => ['my keyword'],
            'og_image' => 'https://example.com/custom.jpg',
            'canonical_url' => 'https://softorio.com/custom',
            'robots_index' => false,
            'schema_type' => 'AboutPage',
        ]);
        $settings = $home->only(['title', 'meta_description', 'secondary_keywords', 'og_image', 'canonical_url', 'robots_index', 'schema_type']);
        $other = new PageSeo;
        $other->forceFill(['seoable_type' => 'App\\Models\\PortfolioItem', 'seoable_id' => 999, 'title' => 'Unrelated portfolio SEO'])->save();
        $otherBefore = $other->fresh()->toArray();

        $this->seed(MainPagesSeoSeeder::class);

        $home->refresh();
        $this->assertSame($settings, $home->only(array_keys($settings)));
        $this->assertSame('My custom homepage title', $home->og_title);
        $this->assertSame('My custom homepage description', $home->twitter_description);
        $this->assertSame('https://example.com/custom.jpg', $home->twitter_image);
        $this->assertSame($otherBefore, $other->fresh()->toArray());
        $before = PageSeo::orderBy('id')->get()->toArray();

        $this->seed(MainPagesSeoSeeder::class);

        $this->assertSame($before, PageSeo::orderBy('id')->get()->toArray());
    }
}
