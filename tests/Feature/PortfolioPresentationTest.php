<?php

namespace Tests\Feature;

use App\Filament\Resources\PortfolioItemResource\Pages\EditPortfolioItem;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioPresentationTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $attributes = []): PortfolioItem
    {
        return PortfolioItem::create(array_merge([
            'title' => 'Portfolio Presentation Project',
            'client' => 'Test Client',
            'category' => 'wordpress',
            'tags' => ['WordPress'],
        ], $attributes));
    }

    public function test_admin_can_save_a_public_website_url_and_clear_it(): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm(['website_url' => 'https://example.com/project'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('https://example.com/project', $project->fresh()->website_url);

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm(['website_url' => null])
            ->call('save')->assertHasNoFormErrors();

        $this->assertNull($project->fresh()->website_url);
    }

    public function test_admin_cannot_save_a_non_http_website_url(): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm(['website_url' => 'javascript:alert(1)'])
            ->call('save')->assertHasFormErrors(['website_url']);

        $this->assertNull($project->fresh()->website_url);
    }

    public function test_project_detail_links_to_the_public_website_in_a_new_tab(): void
    {
        $project = $this->project(['website_url' => 'https://example.com/project']);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('href="https://example.com/project"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_thumbnail_is_displayed_on_listing_and_detail_pages(): void
    {
        Storage::fake('public');
        $project = $this->project(['thumbnail_path' => 'portfolio/project.jpg']);
        $url = Storage::disk('public')->url('portfolio/project.jpg');

        $this->get(route('our-works'))->assertOk()->assertSee('src="'.$url.'"', false);
        $this->get(route('our-works.show', $project))->assertOk()->assertSee('src="'.$url.'"', false);
    }

    public function test_projects_without_images_or_urls_keep_the_fallback_presentation(): void
    {
        $project = $this->project();

        $this->get(route('our-works'))->assertOk()->assertSee('class="portfolio-title-cover"', false)->assertSee('Portfolio Presentation Project');
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('Visit Website')->assertDontSee('class="portfolio-preview"', false);
    }

    public function test_unsafe_urls_in_stored_data_are_not_rendered_as_links(): void
    {
        $project = $this->project(['website_url' => 'javascript:alert(1)']);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('Visit Website')->assertDontSee('href="javascript:', false);
    }

    public function test_website_cards_show_brief_technologies_and_a_visible_project_link(): void
    {
        $this->project([
            'description' => 'A shop with product categories and a checkout flow.',
            'tags' => ['WordPress', 'WooCommerce'],
        ]);

        $this->get(route('our-works'))->assertOk()
            ->assertSee('A shop with product categories and a checkout flow.')
            ->assertSee('WooCommerce')->assertSee('View project')
            ->assertDontSee('View Details');
    }

    public function test_saved_brief_and_write_up_are_editable_and_displayed_on_project_pages(): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm([
                'description' => 'A clear summary of the project.',
                'body' => '<h2>What we did</h2><p>Configured product categories and payment settings.</p>',
                'tags' => 'WordPress, WooCommerce',
            ])->call('save')->assertHasNoFormErrors();

        $this->assertSame('A clear summary of the project.', $project->fresh()->description);
        $this->assertStringContainsString('Configured product categories', $project->fresh()->body);
        $this->assertSame(['WordPress', 'WooCommerce'], $project->fresh()->tags);
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('A clear summary of the project.')
            ->assertSee('What we did')->assertSee('Configured product categories and payment settings.')
            ->assertSee('Technologies &amp; focus', false)->assertSee('WooCommerce')
            ->assertSee('Project information')->assertSee('Test Client');
    }

    public function test_missing_briefs_use_existing_write_up_or_known_project_information(): void
    {
        $project = $this->project(['body' => '<h2>Overview</h2><p>A server deployment with DNS configuration.</p>']);

        $this->assertSame('A server deployment with DNS configuration.', $project->publicBrief());
        $project->update(['body' => null]);
        $this->assertSame('WordPress project for Test Client. Explore the project information and available references.', $project->publicBrief());
        $this->get(route('our-works.show', $project))->assertOk()->assertSee('Overview')
            ->assertSee('WordPress project for Test Client.')
            ->assertSee('data-image-state="missing"', false);
    }

    public function test_project_write_ups_are_sanitized_and_plain_text_fields_are_escaped(): void
    {
        $project = $this->project([
            'title' => 'Shop <img src=x onerror=alert(1)>',
            'description' => 'A shop with <b>catalogue</b> pages.',
            'body' => '<h2>What we did</h2><p>Configured checkout.</p><script>alert("unsafe")</script><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">Unsafe link</a>',
        ]);

        $this->get(route('our-works'))->assertOk()
            ->assertSee('Shop &lt;img src=x onerror=alert(1)&gt;', false)
            ->assertSee('A shop with catalogue pages.');
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('<h2>What we did</h2>', false)->assertSee('Configured checkout.')
            ->assertDontSee('<script>alert("unsafe")</script>', false)
            ->assertDontSee('onerror="alert(1)"', false)->assertDontSee('href="javascript:', false);
    }
}
