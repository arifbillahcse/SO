<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_seeded_services(): void
    {
        Service::create([
            'title' => 'Test Service',
            'slug' => 'test-service',
            'route_name' => 'web-development',
            'icon' => 'code',
            'short_description' => 'A service created for testing.',
            'features' => ['Feature One', 'Feature Two'],
            'featured' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'));

        $response->assertSee('Test Service');
        $response->assertSee('A service created for testing.');
        $response->assertSee('Feature One');
    }

    public function test_homepage_only_shows_featured_testimonials(): void
    {
        Testimonial::create([
            'name' => 'Featured Person', 'role' => 'CEO', 'avatar_initial' => 'F',
            'stars' => 5, 'text' => 'Great work!', 'featured' => true, 'sort_order' => 1,
        ]);
        Testimonial::create([
            'name' => 'Hidden Person', 'role' => 'CTO', 'avatar_initial' => 'H',
            'stars' => 5, 'text' => 'Also great!', 'featured' => false, 'sort_order' => 2,
        ]);

        $response = $this->get(route('home'));

        $response->assertSee('Featured Person');
        $response->assertDontSee('Hidden Person');
    }

    public function test_our_works_renders_seeded_portfolio_items(): void
    {
        PortfolioItem::create([
            'title' => 'Test Project', 'client' => 'Test Client Inc.',
            'category' => 'wordpress', 'tags' => ['WordPress', 'Test'], 'sort_order' => 1,
        ]);

        $response = $this->get(route('our-works'));

        $response->assertSee('Test Project');
        $response->assertSee('Test Client Inc.');
    }

    public function test_case_study_page_shows_featured_case_study_separately_from_grid(): void
    {
        CaseStudy::create([
            'title' => 'Featured Case', 'slug' => 'featured-case', 'client' => 'Big Co',
            'tags' => ['Featured'], 'description' => 'The featured one.',
            'metrics' => [['value' => '100%', 'label' => 'Success']],
            'featured' => true, 'sort_order' => 1,
        ]);
        CaseStudy::create([
            'title' => 'Grid Case', 'slug' => 'grid-case', 'client' => 'Small Co',
            'tags' => ['Grid'], 'description' => 'A grid one.',
            'metrics' => [['value' => '50%', 'label' => 'Growth']],
            'featured' => false, 'sort_order' => 2,
        ]);

        $response = $this->get(route('case-study'));

        $response->assertSee('Featured Case');
        $response->assertSee('Grid Case');
    }

    public function test_our_resources_renders_seeded_articles_ordered_by_published_date(): void
    {
        Article::create([
            'title' => 'Older Article', 'slug' => 'older-article', 'category' => 'SEO',
            'excerpt' => 'An older one.', 'read_minutes' => 5, 'featured' => false,
            'published_at' => now()->subDays(10),
        ]);
        Article::create([
            'title' => 'Newer Article', 'slug' => 'newer-article', 'category' => 'SEO',
            'excerpt' => 'A newer one.', 'read_minutes' => 5, 'featured' => false,
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('our-resources'));

        $response->assertSeeInOrder(['Newer Article', 'Older Article']);
    }

    public function test_our_founders_alternates_reverse_layout_between_members(): void
    {
        TeamMember::create([
            'name' => 'First Founder', 'role' => 'CEO', 'short_role' => 'CEO',
            'bio' => 'Bio one.', 'credentials_heading' => 'Recognition', 'sort_order' => 1,
        ]);
        TeamMember::create([
            'name' => 'Second Founder', 'role' => 'CTO', 'short_role' => 'CTO',
            'bio' => 'Bio two.', 'credentials_heading' => 'Recognition', 'sort_order' => 2,
        ]);

        $response = $this->get(route('our-founders'));

        $response->assertSee('First Founder');
        $response->assertSee('Second Founder');
        // Second member should get the "reverse" layout class, first should not.
        $response->assertSeeInOrder(['First Founder', 'class="founder reverse"', 'Second Founder']);
    }
}
