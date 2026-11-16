<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\ServiceCaseStudiesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServiceCaseStudiesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ten_illustrative_studies_have_disclosures_consistent_figures_and_complete_seo(): void
    {
        $this->seed([CloudPortfolioSeeder::class, ServiceCaseStudiesSeeder::class]);

        $this->assertDatabaseCount('case_studies', 10);
        $listing = $this->get('/case-study')->assertOk()->assertSee('Illustrative Scenarios')
            ->assertDontSee('Avg Conversion Lift')->assertDontSee('proof of what we deliver');
        foreach (CaseStudy::with('seo')->get() as $case) {
            $this->assertSame('illustrative', $case->study_type);
            $this->assertNull($case->client);
            $this->assertGreaterThan(300, $case->seoWordCount());
            $this->assertSame([], $case->seo->missingFields());
            $this->assertTrue($case->seo->isIndexable());
            $this->assertStringContainsString('illustrative', strtolower($case->seo->meta_description));
            $listing->assertSee($case->title);
            $this->get(route('case-study.show', $case))->assertOk()
                ->assertSee('Illustrative scenario.')->assertSee('not measured client results')
                ->assertSee('Illustrative figures:')->assertSee('related Softorio service')
                ->assertSee('Illustrative scenario', false);
        }
        $automation = CaseStudy::where('source_key', 'softorio-service-scenario-automation')->firstOrFail();
        $this->assertStringContainsString('170 minutes', $automation->body);
        $this->assertStringContainsString('56.7%', $automation->body);
        $diagnostic = CaseStudy::where('source_key', 'softorio-service-scenario-diagnostic')->firstOrFail();
        $this->assertStringContainsString('under development', $diagnostic->body);
        $images = CaseStudy::where('source_key', 'softorio-service-scenario-images')->firstOrFail();
        $this->assertStringContainsString('62.5%', $images->body);
        $migration = CaseStudy::where('source_key', 'softorio-service-scenario-migration')->firstOrFail();
        $this->assertStringContainsString('2,400 seconds: 40 minutes', $migration->body);
        $this->get('/sitemap.xml')->assertOk()->assertSee('/case-study/'.$automation->slug, false);
    }

    public function test_import_preserves_edited_content_slugs_metrics_types_and_seo_on_reruns(): void
    {
        $this->seed(ServiceCaseStudiesSeeder::class);
        $case = CaseStudy::where('source_key', 'softorio-service-scenario-woocommerce')->firstOrFail();
        $case->update(['title' => 'My edited study', 'slug' => 'my-edited-study', 'body' => '<p>Administrator copy.</p>', 'metrics' => [], 'featured' => false]);
        $case->seo->update(['title' => 'My SEO title', 'robots_index' => false]);
        $before = CaseStudy::with('seo')->orderBy('id')->get()->toArray();

        $this->seed(ServiceCaseStudiesSeeder::class);

        $this->assertSame($before, CaseStudy::with('seo')->orderBy('id')->get()->toArray());
        $this->assertDatabaseCount('case_studies', 10);
    }

    public function test_listing_does_not_drop_additional_featured_studies(): void
    {
        $this->seed(ServiceCaseStudiesSeeder::class);
        CaseStudy::where('source_key', 'softorio-service-scenario-cloud')->update(['featured' => true]);

        $response = $this->get('/case-study')->assertOk();

        foreach (CaseStudy::all() as $case) {
            $response->assertSee($case->title);
        }
    }
}
