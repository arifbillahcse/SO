<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\RelationManagers\OfferingsRelationManager;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\ServiceOffering;
use App\Models\User;
use Database\Seeders\CloudPortfolioSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DynamicServicesAdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_an_offering_select_portfolio_work_and_edit_its_public_content(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create(['route_name' => 'web-development']);
        $this->seed(CloudPortfolioSeeder::class);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-045')->firstOrFail();

        Livewire::test(OfferingsRelationManager::class, ['ownerRecord' => $service, 'pageClass' => EditService::class])
            ->callAction(TestAction::make('create')->table(), data: [
                'service_id' => $service->id, 'title' => 'A new website service', 'slug' => 'new-website-service',
                'description' => 'A description written in the dashboard.', 'scope' => [['item' => 'Planning'], ['item' => 'Delivery']],
                'icon' => 'code', 'action_label' => 'Discuss this service', 'action_route' => 'home',
                'action_fragment' => 'contact', 'is_published' => true, 'featured' => true, 'sort_order' => 2,
                'portfolioItems' => [$project->id],
            ])->assertHasNoFormErrors();

        $offering = ServiceOffering::where('slug', 'new-website-service')->firstOrFail();
        $this->assertSame(['Planning', 'Delivery'], $offering->scope);
        $this->assertSame([$project->id], $offering->portfolioItems->modelKeys());
        $this->get('/services')->assertOk()->assertSee('A new website service')->assertSee('Featured service');

        Livewire::test(OfferingsRelationManager::class, ['ownerRecord' => $service, 'pageClass' => EditService::class])
            ->callAction(TestAction::make('edit')->table($offering), data: [
                'title' => 'An edited service', 'description' => 'Updated copy from the dashboard.',
                'is_published' => false, 'catalogue_key' => 'attempted-reference-edit', 'portfolioItems' => [],
            ])->assertHasNoFormErrors();

        $this->assertSame('An edited service', $offering->fresh()->title);
        $this->assertNull($offering->fresh()->catalogue_key);
        $this->assertSame(0, $offering->portfolioItems()->count());
        $this->get('/services')->assertOk()->assertDontSee('An edited service')->assertDontSee('Updated copy from the dashboard.');
    }

    public function test_admin_rejects_unsafe_links_and_can_move_an_offering_to_another_core_group(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create(['route_name' => 'web-development']);
        $other = Service::factory()->create(['route_name' => 'seo']);
        $offering = ServiceOffering::factory()->for($service)->create(['catalogue_key' => 'stable-import-key']);

        Livewire::test(OfferingsRelationManager::class, ['ownerRecord' => $service, 'pageClass' => EditService::class])
            ->callAction(TestAction::make('edit')->table($offering), data: [
                'action_route' => null, 'action_url' => 'javascript:alert(1)',
            ])->assertHasFormErrors(['action_url']);

        $this->assertNull($offering->fresh()->action_url);

        Livewire::test(OfferingsRelationManager::class, ['ownerRecord' => $service, 'pageClass' => EditService::class])
            ->callAction(TestAction::make('edit')->table($offering), data: [
                'service_id' => $other->id, 'action_route' => null, 'action_url' => 'https://example.com/enquiry',
                'catalogue_key' => 'tampered-key', 'is_published' => true,
            ])->assertHasNoFormErrors();

        $this->assertSame($other->id, $offering->fresh()->service_id);
        $this->assertSame('stable-import-key', $offering->fresh()->catalogue_key);
        $this->get('/services')->assertOk()->assertSee('href="https://example.com/enquiry"', false);
    }

    public function test_publishing_and_ordering_control_the_public_catalogue_homepage_and_schema(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create(['route_name' => 'web-development', 'title' => 'A visible group']);
        $first = ServiceOffering::factory()->for($service)->create(['title' => 'First public offering', 'is_published' => true, 'sort_order' => 1]);
        $second = ServiceOffering::factory()->for($service)->create(['title' => 'Second public offering', 'is_published' => true, 'sort_order' => 2]);
        ServiceOffering::factory()->for($service)->create(['title' => 'A private draft']);

        Livewire::test(OfferingsRelationManager::class, ['ownerRecord' => $service, 'pageClass' => EditService::class])
            ->call('reorderTable', [$second->id, $first->id]);

        $this->get('/services')->assertOk()->assertSeeInOrder(['Second public offering', 'First public offering'])
            ->assertDontSee('A private draft')->assertViewHas('serviceCount', 2);
        $this->assertSame([$second->id, $first->id], $service->offerings()->where('is_published', true)->pluck('id')->all());

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm(['is_published' => false])->call('save')->assertHasNoFormErrors();

        $this->get('/')->assertOk()->assertDontSee('A visible group');
        $this->get('/services')->assertOk()->assertDontSee('First public offering')
            ->assertViewHas('serviceCount', 0)
            ->assertViewHas('catalogueSchema', fn (array $schema): bool => $schema['mainEntity']['numberOfItems'] === 0);
    }

    public function test_empty_database_has_no_configuration_fallback_and_public_content_is_escaped(): void
    {
        $this->get('/services')->assertOk()->assertViewHas('serviceCount', 0)->assertDontSee('Custom Laravel &amp; PHP Applications', false);
        $service = Service::factory()->create(['route_name' => 'web-development']);
        ServiceOffering::factory()->for($service)->create([
            'title' => '<script>alert("xss")</script>', 'is_published' => true,
            'action_url' => 'javascript:alert(1)',
        ]);

        $this->get('/services')->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false)->assertDontSee('href="javascript:', false);
    }
}
