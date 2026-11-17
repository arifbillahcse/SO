<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomepagePortfolioTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function project(array $attributes = []): PortfolioItem
    {
        return PortfolioItem::create(array_merge([
            'title' => 'Current portfolio project',
            'client' => 'Current client',
            'category' => 'wordpress',
            'tags' => ['WordPress', 'PHP'],
            'description' => 'A current project brief.',
        ], $attributes));
    }

    public function test_homepage_uses_saved_projects_with_briefs_tags_and_detail_links(): void
    {
        $project = $this->project();

        $this->get('/')->assertOk()
            ->assertSee('Current portfolio project')->assertSee('Current client')
            ->assertSee('A current project brief.')->assertSee('PHP')
            ->assertSee('href="'.route('our-works.show', $project).'"', false)
            ->assertSee('portfolio-title-cover')->assertSee('View project')
            ->assertSee('href="'.route('our-works').'"', false)
            ->assertDontSee('Business Corporate Site')->assertDontSee('Tropicana (PepsiCo)')
            ->assertDontSee('Cloud Infrastructure Setup');
    }

    public function test_homepage_shows_six_projects_in_admin_sort_order_with_stable_ties(): void
    {
        $this->project(['title' => 'Last project', 'sort_order' => 50]);
        $first = $this->project(['title' => 'First project', 'sort_order' => 0]);
        $second = $this->project(['title' => 'Second project', 'sort_order' => 0]);
        foreach (['Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh'] as $index => $title) {
            $this->project(['title' => $title.' project', 'sort_order' => $index + 1]);
        }

        $this->get('/')->assertOk()->assertViewHas('homePortfolioItems', fn ($items): bool => $items->pluck('title')->all() === ['First project', 'Second project', 'Third project', 'Fourth project', 'Fifth project', 'Sixth project']
        )->assertDontSee('Seventh project')->assertDontSee('Last project');
        $this->get(route('our-works'))->assertOk()
            ->assertSeeInOrder([$first->title, $second->title, 'Third project'])
            ->assertSee('Seventh project')->assertSee('Last project');
    }

    public function test_portfolio_edits_and_removal_are_reflected_on_the_homepage(): void
    {
        $project = $this->project();
        $this->get('/')->assertOk()->assertSee('A current project brief.');

        $project->update(['title' => 'Updated portfolio project', 'description' => 'Updated scope and project brief.']);
        $this->get('/')->assertOk()->assertSee('Updated portfolio project')
            ->assertSee('Updated scope and project brief.')->assertDontSee('A current project brief.');

        $project->delete();
        $this->get('/')->assertOk()->assertDontSee('Updated portfolio project')
            ->assertSee('New work will appear here as our portfolio grows.');
    }

    public function test_homepage_filters_only_include_categories_in_the_displayed_projects(): void
    {
        $this->project(['category' => 'wordpress']);
        $this->project([
            'title' => 'Cloud demonstration', 'category' => 'cloud',
            'project_type' => 'technical_demonstration', 'attribution' => 'Example contributor',
            'description' => 'A Linux deployment demonstration.',
        ]);

        $this->get('/')->assertOk()->assertSee('data-home-portfolio-filters', false)
            ->assertSee('data-filter="wordpress"', false)->assertSee('data-filter="cloud"', false)
            ->assertDontSee('data-filter="ecommerce"', false)
            ->assertSee('A Linux deployment demonstration.')->assertSee('Example contributor')
            ->assertSee('Technical overview');
    }

    public function test_empty_portfolio_has_no_example_cards_but_keeps_the_works_anchor(): void
    {
        $this->get('/')->assertOk()->assertViewHas('homePortfolioItems', fn ($items): bool => $items->isEmpty())
            ->assertSee('id="works"', false)->assertDontSee('class="wp-card work-card"', false)
            ->assertSee('New work will appear here as our portfolio grows.')
            ->assertDontSee('Business Corporate Site')->assertDontSee('data-home-portfolio-filters', false);
    }
}
