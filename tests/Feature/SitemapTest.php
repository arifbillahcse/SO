<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml_with_the_correct_content_type(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function test_sitemap_is_well_formed_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->getContent());

        $this->assertNotFalse($xml, 'Sitemap response is not valid XML: '.implode(', ', array_map(
            fn ($e) => $e->message,
            libxml_get_errors()
        )));
    }

    public function test_sitemap_includes_the_homepage_and_excludes_our_founders(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertSee('<loc>https://softorio.com/</loc>', false);
        $response->assertDontSee('/our-founders', false);
    }

    public function test_sitemap_includes_contact_page_and_excludes_post_only_affiliate_form_route(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertSee('<loc>https://softorio.com/contact</loc>', false);
        $response->assertDontSee('/affiliate-referral<', false);
    }

    public function test_diagnostic_management_service_is_accessible_and_in_the_sitemap(): void
    {
        $this->get('/diagnostic-management-system')
            ->assertOk()
            ->assertSee('Diagnostic Management')
            ->assertSee('Software under development');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/diagnostic-management-system', false);
    }

    public function test_robots_txt_points_to_the_sitemap(): void
    {
        // public/robots.txt is served directly by the webserver in
        // production, bypassing Laravel's router entirely — so it's
        // read straight off disk here rather than via $this->get(),
        // which would 404 against the test HTTP kernel.
        $contents = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://softorio.com/sitemap.xml', $contents);
        // our-founders must stay crawlable: it is kept out of the index by its
        // noindex tag, which Google can only read if robots.txt allows the URL.
        $this->assertStringNotContainsString('Disallow: /our-founders', $contents);
    }
}
