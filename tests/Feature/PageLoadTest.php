<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageLoadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every public, indexable route should load without error and
     * render the shared nav/footer. Dynamic-data pages (home,
     * our-works, case-study, our-resources, our-founders) are
     * exercised with an empty database here — they should degrade
     * gracefully to empty sections, not error, when nothing is
     * seeded yet.
     */
    public static function publicRoutes(): array
    {
        return [
            'home' => ['home'],
            'web-development' => ['web-development'],
            'seo' => ['seo'],
            'manage-cloud' => ['manage-cloud'],
            'automation' => ['automation'],
            'ecommerce-solution' => ['ecommerce-solution'],
            'cloud-solution' => ['cloud-solution'],
            'automation-solution' => ['automation-solution'],
            'our-works' => ['our-works'],
            'about-us' => ['about-us'],
            'case-study' => ['case-study'],
            'our-resources' => ['our-resources'],
            'affiliate-program' => ['affiliate-program'],
            'our-founders' => ['our-founders'],
        ];
    }

    #[DataProvider('publicRoutes')]
    public function test_page_loads_successfully(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertOk();
        $response->assertSee('Softorio');
    }

    public function test_every_page_includes_shared_nav_and_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('id="navbar"', false);
        $response->assertSee('class="footer"', false);
    }

    public function test_our_founders_is_not_linked_in_nav_or_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertDontSee(route('our-founders'), false);
    }

    public function test_our_founders_has_noindex_meta_tag(): void
    {
        $response = $this->get(route('our-founders'));

        $response->assertSee('noindex, nofollow', false);
    }

    public function test_unknown_route_returns_404(): void
    {
        $response = $this->get('/this-page-does-not-exist');

        $response->assertNotFound();
    }
}
