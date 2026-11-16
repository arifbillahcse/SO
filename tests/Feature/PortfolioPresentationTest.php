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

        $this->get(route('our-works'))->assertOk()->assertSee('class="wp-mock"', false);
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('Visit Website')->assertDontSee('class="portfolio-preview"', false);
    }

    public function test_unsafe_urls_in_stored_data_are_not_rendered_as_links(): void
    {
        $project = $this->project(['website_url' => 'javascript:alert(1)']);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('Visit Website')->assertDontSee('href="javascript:', false);
    }
}
