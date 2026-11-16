<?php

namespace Tests\Feature;

use App\Models\PageSeo;
use App\Models\PortfolioItem;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\ServiceCatalogueSeeder;
use Database\Seeders\ServicesPageSeoSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServicesCatalogueTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        return new DOMXPath($document);
    }

    public function test_all_services_and_category_links_render_without_portfolio_records(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);
        $response = $this->get('/services')->assertOk();

        $xpath = $this->xpath($response->getContent());
        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame(5, $xpath->query('//article[@class="services-core-card"]')->length);
        $this->assertSame(30, $xpath->query('//article[@class="services-detail-card"]')->length);
        foreach (['website-development' => 7, 'seo-optimization' => 6, 'managed-cloud' => 10, 'business-automation' => 3, 'diagnostic-management' => 4] as $key => $count) {
            $this->assertSame($count, $xpath->query('//section[@id="'.$key.'"]//article')->length);
            $this->assertSame(1, $xpath->query('//nav[@aria-label="Service categories"]/a[@href="#'.$key.'"]')->length);
        }
        $this->assertSame(0, $xpath->query('//a[@class="services-related-link"]')->length);
        $this->assertSame(0, $xpath->query('//section[@class="services-work"]')->length);
        $this->assertSame(4, $xpath->query('//span[@class="services-availability"]')->length);
        $response->assertSee('Software under development')->assertSee('AI Chatbots &amp; Assisted Features', false);
        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseEmpty('portfolio_items');
    }

    public function test_related_work_uses_current_project_slugs_and_does_not_invent_examples_for_declared_offerings(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-045')->firstOrFail();
        $project->update(['slug' => 'updated-cyberpanel-project']);
        $this->seed(ServiceCatalogueSeeder::class);

        $response = $this->get('/services')->assertOk();

        $xpath = $this->xpath($response->getContent());
        $this->assertSame(3, $xpath->query('//a[@class="services-work-card"]')->length);
        $this->assertSame(1, $xpath->query('//article[@id="service-wordpress-development"]//a[@class="services-related-link"][@href="'.route('our-works.show', $project).'"]')->length);
        $this->assertSame(0, $xpath->query('//article[@id="service-ai-chatbots"]//a[@class="services-related-link"]')->length);
        $this->assertSame(1, $xpath->query('//article[@id="service-ai-chatbots"]//a[@href="'.route('home').'#contact"]')->length);
        $response->assertDontSee('View verified customer results');
    }

    public function test_services_metadata_and_structured_catalogue_describe_the_visible_offerings(): void
    {
        $this->seed([ServicesPageSeoSeeder::class, ServiceCatalogueSeeder::class]);

        $response = $this->get('/services')->assertOk();

        $xpath = $this->xpath($response->getContent());
        $this->assertSame('Digital Services for Websites, SEO & Cloud | Softorio', $xpath->query('//title')->item(0)->textContent);
        $this->assertSame('https://softorio.com/services', $xpath->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
        $this->assertSame('index, follow', $xpath->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
        $this->assertSame('https://softorio.com/images/og-default.png', $xpath->query('//meta[@property="og:image"]')->item(0)->getAttribute('content'));
        $seo = PageSeo::where('route_name', 'services')->firstOrFail();
        $this->assertSame($seo->meta_description, $xpath->query('//meta[@name="description"]')->item(0)->getAttribute('content'));
        $this->assertSame($seo->twitter_description, $xpath->query('//meta[@name="twitter:description"]')->item(0)->getAttribute('content'));
        $schemas = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $schema = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
            $schemas[$schema['@type']] = $schema;
        }
        $this->assertArrayHasKey('BreadcrumbList', $schemas);
        $catalogue = $schemas['CollectionPage']['mainEntity'];
        $this->assertSame(30, $catalogue['numberOfItems']);
        $this->assertCount(30, $catalogue['itemListElement']);
        foreach ($catalogue['itemListElement'] as $index => $entry) {
            $this->assertSame($index + 1, $entry['position']);
            $anchor = parse_url($entry['item']['url'], PHP_URL_FRAGMENT);
            $heading = $xpath->query('//article[@id="'.$anchor.'"]/h3')->item(0);
            $this->assertNotNull($heading);
            $this->assertSame($heading->textContent, $entry['item']['name']);
        }
        $this->get('/sitemap.xml')->assertOk()->assertSee('<loc>https://softorio.com/services</loc>', false);
    }

    public function test_services_seo_import_preserves_edits_and_is_safe_to_repeat(): void
    {
        $seo = PageSeo::forRoute('services');
        $seo->update([
            'title' => 'Administrator services title',
            'focus_keyword' => 'custom topic',
            'og_image' => 'https://example.com/custom-cover.jpg',
            'secondary_keywords' => ['custom phrase'],
            'canonical_url' => 'https://softorio.com/custom-services',
            'robots_index' => false,
            'robots_follow' => false,
        ]);
        $overrides = $seo->only(['title', 'focus_keyword', 'og_image', 'secondary_keywords', 'canonical_url', 'robots_index', 'robots_follow']);

        $this->seed(ServicesPageSeoSeeder::class);

        $seo->refresh();
        $this->assertSame($overrides, $seo->only(array_keys($overrides)));
        $this->assertSame([], $seo->missingFields());
        $before = $seo->toArray();

        $this->seed(ServicesPageSeoSeeder::class);

        $this->assertSame($before, $seo->fresh()->toArray());
    }
}
