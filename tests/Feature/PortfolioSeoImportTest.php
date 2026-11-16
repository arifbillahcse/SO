<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use Database\Seeders\PortfolioSeoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PortfolioSeoImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_import_fills_all_portfolios_and_is_safe_to_repeat(): void
    {
        $this->seed([PdfPortfolioSeeder::class, CloudPortfolioSeeder::class]);
        $originalProjects = PortfolioItem::orderBy('id')->get()->toArray();

        $this->seed(PortfolioSeoSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 118);
        $titles = [];
        $descriptions = [];
        foreach (PortfolioItem::with('seo')->get() as $project) {
            $this->assertSame([], $project->seo->missingFields());
            $this->assertNotEmpty($project->seo->secondary_keywords);
            $this->assertNotEmpty($project->seo->content_notes);
            $this->assertNotEmpty($project->seo->og_title);
            $this->assertNotEmpty($project->seo->twitter_description);
            $this->assertSame($project->thumbnail_path ?: $project->seo->og_image, $project->seo->og_image);
            $titles[] = $project->seo->title;
            $descriptions[] = $project->seo->meta_description;
        }
        $this->assertCount(118, array_unique($titles));
        $this->assertCount(118, array_unique($descriptions));
        $this->assertSame($originalProjects, PortfolioItem::orderBy('id')->get()->toArray());
        $project = PortfolioItem::where('slug', 'verde-glow-com')->firstOrFail();
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('<title>Verde Glow eCommerce Website | Softorio</title>', false)
            ->assertSee('name="description"', false);

        $before = PortfolioItem::with('seo')->orderBy('id')->get()->toArray();
        $this->seed(PortfolioSeoSeeder::class);

        $this->assertSame($before, PortfolioItem::with('seo')->orderBy('id')->get()->toArray());
    }

    public function test_import_preserves_administrator_fields_and_matches_technical_projects_by_source_key(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-043')->firstOrFail();
        $project->update(['slug' => 'admin-custom-project-url', 'thumbnail_path' => 'portfolio/custom-social-cover.jpg']);
        $seo = $project->seo;
        $overrides = [
            'title' => 'Administrator title',
            'meta_description' => 'Administrator description',
            'focus_keyword' => 'Administrator keyword',
            'secondary_keywords' => ['Custom topic'],
            'content_notes' => 'Administrator notes',
            'og_title' => 'Administrator social title',
            'og_description' => 'Administrator social description',
            'og_image' => 'portfolio/administrator.jpg',
            'twitter_title' => 'Administrator Twitter title',
            'twitter_description' => 'Administrator Twitter description',
            'canonical_url' => 'https://softorio.com/custom-canonical',
            'robots_index' => false,
            'robots_follow' => false,
            'schema_type' => 'WebPage',
        ];
        $seo->update($overrides);

        $this->seed(PortfolioSeoSeeder::class);

        $seo->refresh();
        foreach ($overrides as $field => $value) {
            $this->assertSame($value, $seo->getAttribute($field), $field);
        }
        $this->assertSame('portfolio/custom-social-cover.jpg', $seo->twitter_image);
        $this->assertSame('admin-custom-project-url', $project->fresh()->slug);
    }

    public function test_import_does_not_create_missing_portfolio_projects(): void
    {
        $this->seed(PortfolioSeoSeeder::class);

        $this->assertDatabaseEmpty('portfolio_items');
        $this->assertDatabaseEmpty('page_seos');
    }
}
