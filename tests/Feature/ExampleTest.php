<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * Needs RefreshDatabase now that `/` is wired to HomeController,
     * which queries the services/testimonials tables (Phase 5) —
     * without a migrated test database this would fail with
     * "no such table: services" rather than asserting a real 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_hides_testimonials_when_none_are_published(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="testimonials"', false)
            ->assertDontSee('Example preview')
            ->assertDontSee('Placeholder content')
            ->assertDontSee('From 120+ client reviews');
    }

    public function test_homepage_shows_featured_testimonials_instead_of_examples(): void
    {
        Testimonial::create([
            'name' => 'Test Client',
            'role' => 'Owner',
            'avatar_initial' => 'T',
            'stars' => 5,
            'text' => 'A review supplied for this test.',
            'featured' => true,
            'sort_order' => 1,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('A review supplied for this test.')
            ->assertDontSee('Example preview');
    }

    public function test_services_page_lists_and_links_to_every_service(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);
        $response = $this->get('/services')->assertOk();

        foreach (['web-development', 'seo', 'manage-cloud', 'automation', 'diagnostic-management-system'] as $service) {
            $response->assertSee(route($service), false);
        }

        $response->assertSee('Software under development');
        $this->get('/')->assertSee('href="'.route('services').'"', false);
        $this->get('/sitemap.xml')->assertSee('/services', false);
    }
}
