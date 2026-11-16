<?php

namespace Tests\Feature;

use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

    public function test_saved_contact_and_policy_edits_are_reflected_in_last_modified_dates(): void
    {
        $baseline = Carbon::createFromTimestamp(filemtime(resource_path('views/pages/legal.blade.php')))->addDay();
        $this->travelTo($baseline);
        PageSeo::syncStaticPages();
        $contact = ContactPageSettings::current();
        $organization = OrganizationSettings::current();
        $this->travel(2)->hours();

        $contact->update(['hero_title' => 'Updated contact information']);
        $organization->update(['tos_intro' => 'Updated terms introduction.', 'privacy_intro' => 'Updated privacy introduction.']);
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent());
        $dates = [];

        foreach ($xml->url as $url) {
            $dates[(string) $url->loc] = (string) $url->lastmod;
        }

        foreach (['contact', 'tos', 'privacy'] as $path) {
            $this->assertSame(now()->toAtomString(), $dates['https://softorio.com/'.$path]);
        }
    }

    public function test_sitemap_stays_unchanged_between_requests_and_contains_only_protocol_elements(): void
    {
        $first = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->travel(2)->hours();
        $second = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertSame($first, $second);
        $this->assertStringNotContainsString('<script', $second);
        $this->assertStringNotContainsString('chrome-extension://', $second);

        $document = new \DOMDocument;
        $this->assertTrue($document->loadXML($second));
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->assertSame(0, $xpath->query('/s:urlset/*[not(self::s:url)]')->length);
        $this->assertSame(0, $xpath->query('/s:urlset/s:url/*[not(self::s:loc or self::s:lastmod)]')->length);
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
