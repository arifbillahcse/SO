<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\CloudPortfolioContentSeeder;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PortfolioSeoPolishTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function dom(string $html): DOMXPath
    {
        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        return new DOMXPath($document);
    }

    public function test_portfolio_listing_has_descriptive_metadata_and_structured_links_for_every_entry(): void
    {
        $this->seed([PdfPortfolioSeeder::class, CloudPortfolioSeeder::class]);

        $response = $this->get('/our-works?collection=technical&category=cloud')->assertOk();

        $xpath = $this->dom($response->getContent());
        $this->assertSame('Website & Cloud Portfolio | Softorio', $xpath->query('//title')->item(0)->textContent);
        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame('https://softorio.com/our-works', $xpath->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
        $schema = null;
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $data = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
            if (($data['@type'] ?? null) === 'CollectionPage') {
                $schema = $data;
            }
        }
        $this->assertNotNull($schema);
        $this->assertSame(118, $schema['mainEntity']['numberOfItems']);
        $this->assertCount(118, $schema['mainEntity']['itemListElement']);
        $this->assertSame(1, $schema['mainEntity']['itemListElement'][0]['position']);
        $this->assertSame('https://softorio.com/our-works/verde-glow-com', $schema['mainEntity']['itemListElement'][0]['url']);
        $this->assertSame(118, $schema['mainEntity']['itemListElement'][117]['position']);
    }

    public function test_expanded_video_demonstrations_are_indexable_and_only_eligible_pages_enter_the_sitemap(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $video = PortfolioItem::where('source_key', 'arif-cloud-portfolio-047')->firstOrFail();
        $overview = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();

        $response = $this->get(route('our-works.show', $video))->assertOk();

        $xpath = $this->dom($response->getContent());
        $this->assertSame('index, follow', $xpath->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
        $this->assertSame('Raw HTML Deployment on CyberPanel | Softorio', $xpath->query('//title')->item(0)->textContent);
        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertGreaterThanOrEqual(3, $xpath->query('//article//h2')->length);
        $this->assertTrue($video->seo->isIndexable());
        $this->assertFalse($overview->seo->isIndexable());
        $this->assertSame(5, PortfolioItem::all()->filter(fn (PortfolioItem $item): bool => $item->seo->isIndexable())->count());
        $this->get('/sitemap.xml')->assertOk()
            ->assertSee('/our-works/'.$video->slug, false)
            ->assertDontSee('/our-works/'.$overview->slug, false);
        $response->assertSee('Explore managed cloud services')->assertSee('href="'.route('manage-cloud').'"', false);
    }

    public function test_editorial_upgrade_replaces_only_original_copy_and_preserves_administrator_seo_overrides(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $editorials = json_decode(file_get_contents(database_path('seeders/data/cloud-portfolio-editorial.json')), true, flags: JSON_THROW_ON_ERROR)['projects'];
        foreach ($editorials as $editorial) {
            PortfolioItem::where('source_key', $editorial['source_key'])->firstOrFail()->update([
                'body' => $editorial['previous_body'],
                'description' => $editorial['previous_description'],
            ]);
        }
        $original = PortfolioItem::where('source_key', 'arif-cloud-portfolio-043')->firstOrFail();
        $edited = PortfolioItem::where('source_key', 'arif-cloud-portfolio-044')->firstOrFail();
        $edited->update(['body' => '<h2>Administrator write-up</h2><p>Preserve my project notes.</p>', 'description' => 'Administrator summary.']);
        $edited->seo->update(['title' => 'Administrator SEO title', 'focus_keyword' => 'custom keyword', 'robots_index' => false]);
        $overview = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();
        $before = $overview->toArray();

        $this->seed(CloudPortfolioContentSeeder::class);

        $this->assertStringContainsString('What to review in the setup', $original->fresh()->body);
        $this->assertSame('Comodo SSL on Namecheap', $original->fresh()->seo->focus_keyword);
        $this->assertSame('<h2>Administrator write-up</h2><p>Preserve my project notes.</p>', $edited->fresh()->body);
        $this->assertSame('Administrator summary.', $edited->fresh()->description);
        $this->assertSame('Administrator SEO title', $edited->fresh()->seo->title);
        $this->assertSame('custom keyword', $edited->fresh()->seo->focus_keyword);
        $this->assertFalse($edited->fresh()->seo->robots_index);
        $this->assertSame($before, $overview->fresh()->toArray());

        $this->seed(CloudPortfolioContentSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 47);
        $this->assertStringContainsString('What to review in the setup', $original->fresh()->body);
    }

    public function test_editorial_upgrade_does_not_create_missing_portfolio_records(): void
    {
        $this->seed(CloudPortfolioContentSeeder::class);

        $this->assertDatabaseEmpty('portfolio_items');
    }

    public static function mediaDescriptions(): array
    {
        return [
            'video thumbnail' => ['portfolio/cloud-videos/topic.jpg', 'Video thumbnail: Cloud deployment'],
            'technical cover' => ['portfolio/cloud-covers/topic.jpg', 'Illustrated technical cover: Cloud deployment'],
            'website screenshot' => ['portfolio/screenshots/topic.jpg', 'Cloud deployment website screenshot'],
            'archive cover' => ['portfolio/covers/topic.jpg', 'Portfolio archive cover: Cloud deployment'],
            'custom upload' => ['portfolio/custom.jpg', 'Cloud deployment portfolio image'],
        ];
    }

    #[DataProvider('mediaDescriptions')]
    public function test_portfolio_images_describe_the_type_of_image_without_claiming_covers_are_screenshots(string $path, string $alt): void
    {
        $project = PortfolioItem::create([
            'title' => 'Cloud deployment', 'client' => 'Example client', 'category' => 'cloud',
            'tags' => ['Linux'], 'thumbnail_path' => $path,
        ]);

        $response = $this->get(route('our-works.show', $project))->assertOk();

        $xpath = $this->dom($response->getContent());
        if (str_starts_with($path, 'portfolio/covers/')) {
            $this->assertSame(0, $xpath->query('//img[contains(@class,"portfolio-preview")]')->length);
            $response->assertSee('portfolio-title-cover')->assertSee('Cloud deployment');
        } else {
            $this->assertSame($alt, $xpath->query('//img[contains(@class,"portfolio-preview")]')->item(0)->getAttribute('alt'));
        }
    }

    public function test_related_topics_prioritize_the_same_category(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-018')->firstOrFail();

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertViewHas('relatedItems', fn ($items): bool => $items->count() === 3 && $items->every(
                fn (PortfolioItem $item): bool => $item->category === 'wordpress' && $item->id !== $project->id
            ));
    }
}
