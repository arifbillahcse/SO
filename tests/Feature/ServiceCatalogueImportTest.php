<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceOffering;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServiceCatalogueImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_import_creates_five_groups_thirty_offerings_and_available_portfolio_selections(): void
    {
        $this->seed([PdfPortfolioSeeder::class, CloudPortfolioSeeder::class]);

        $this->seed(ServiceCatalogueSeeder::class);

        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseCount('service_offerings', 30);
        $this->assertSame([7, 6, 10, 3, 4], Service::withCount('offerings')->orderBy('sort_order')->pluck('offerings_count')->all());
        $this->assertSame(18, ServiceOffering::has('portfolioItems')->count());
        $this->assertSame(30, ServiceOffering::where('is_published', true)->count());
        $this->assertSame(4, ServiceOffering::whereNotNull('availability')->count());
        $wordpress = ServiceOffering::where('catalogue_key', 'wordpress-development')->firstOrFail();
        $this->assertSame('website-development', $wordpress->service->catalogue_key);
        $this->assertSame(['arif-cloud-portfolio-045', 'arif-cloud-portfolio-044'], $wordpress->portfolioItems->pluck('source_key')->all());
        $this->assertCount(3, $wordpress->scope);
        $this->assertSame('web-development', $wordpress->action_route);
        $this->get('/')->assertOk()->assertSee('Diagnostic Management System');
        $this->get('/services')->assertOk()->assertSee('Custom Laravel &amp; PHP Applications', false);
        $this->assertDatabaseCount('portfolio_items', 118);
    }

    public function test_import_adopts_existing_core_services_and_preserves_admin_edits_and_portfolio_selections_on_reruns(): void
    {
        $existing = Service::factory()->create([
            'route_name' => 'web-development', 'title' => 'My existing core service',
            'short_description' => 'Keep this description.', 'is_published' => false,
        ]);
        $this->seed([CloudPortfolioSeeder::class, ServiceCatalogueSeeder::class]);
        $existing->refresh();
        $this->assertSame('My existing core service', $existing->title);
        $this->assertSame('Keep this description.', $existing->short_description);
        $this->assertFalse($existing->is_published);
        $this->assertSame('website-development', $existing->catalogue_key);
        $existing->update(['route_name' => 'my-custom-route', 'sort_order' => 12]);
        $offering = ServiceOffering::where('catalogue_key', 'wordpress-development')->firstOrFail();
        $offering->update([
            'title' => 'My edited offering', 'slug' => 'my-edited-slug', 'description' => 'My own copy.',
            'scope' => ['My edited scope'], 'is_published' => false, 'featured' => true,
            'sort_order' => 29, 'action_url' => 'https://example.com/enquiry', 'action_route' => null,
        ]);
        $offering->portfolioItems()->detach();
        $custom = ServiceOffering::factory()->for($existing)->create();
        $beforeGroups = Service::orderBy('id')->get()->toArray();
        $beforeOfferings = ServiceOffering::with('portfolioItems')->orderBy('id')->get()->toArray();

        $this->seed(ServiceCatalogueSeeder::class);

        $this->assertSame($beforeGroups, Service::orderBy('id')->get()->toArray());
        $this->assertSame($beforeOfferings, ServiceOffering::with('portfolioItems')->orderBy('id')->get()->toArray());
        $this->assertModelExists($custom);
        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseCount('service_offerings', 31);
    }

    public function test_import_without_portfolio_records_does_not_fabricate_projects_or_fail(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $this->assertDatabaseCount('services', 5);
        $this->assertDatabaseCount('service_offerings', 30);
        $this->assertDatabaseEmpty('portfolio_items');
        $this->assertDatabaseEmpty('portfolio_item_service_offering');
    }
}
