<?php

namespace Tests\Feature;

use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
use App\Models\PortfolioItem;
use App\Models\Service;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WebsiteConsistencyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_temporary_database_diagnostic_endpoint_is_not_publicly_available(): void
    {
        $this->get('/__diag')->assertNotFound();
    }

    public function test_contact_overrides_are_shared_across_home_services_solutions_and_affiliate_pages(): void
    {
        OrganizationSettings::current()->update(['email' => 'organization@example.test', 'telephone' => '+880 1111111111']);
        ContactPageSettings::current()->update(['contact_email' => 'shared@example.test', 'contact_phone' => '+880 2222222222']);

        foreach (['/', '/web-development', '/seo', '/manage-cloud', '/automation', '/ecommerce-solution', '/cloud-solution', '/automation-solution', '/affiliate-program', '/contact'] as $path) {
            $this->get($path)->assertOk()->assertSee('shared@example.test')->assertSee('+880 2222222222')
                ->assertDontSee('+880 1776060247')->assertDontSee('+880 1779440247');
        }
    }

    public function test_footer_shows_clickable_contact_details_above_socials_with_contact_page_overrides(): void
    {
        OrganizationSettings::current()->update([
            'email' => 'organization@example.test', 'telephone' => '+880 1111111111',
            'facebook_url' => 'https://example.test/facebook',
        ]);
        $settings = ContactPageSettings::current();
        $settings->update(['contact_email' => 'footer@example.test', 'contact_phone' => '+880 2222222222']);

        $this->get('/privacy')->assertOk()->assertSeeInOrder([
            'class="footer-contact-details"', 'href="tel:+8802222222222"',
            'href="mailto:footer@example.test"', 'class="footer-socials"',
        ], false)->assertSee('+880 2222222222');

        $settings->update(['contact_email' => null, 'contact_phone' => null]);
        $this->get('/tos')->assertOk()->assertSee('href="mailto:organization@example.test"', false)
            ->assertSee('href="tel:+8801111111111"', false);
    }

    public function test_footer_omits_empty_or_invalid_contact_details(): void
    {
        OrganizationSettings::current()->update(['email' => null, 'telephone' => null]);
        ContactPageSettings::current()->update(['contact_email' => null, 'contact_phone' => null]);
        $this->get('/privacy')->assertOk()->assertDontSee('class="footer-contact-details"', false);

        ContactPageSettings::current()->update(['contact_email' => 'invalid-email', 'contact_phone' => 'invalid-phone']);
        $this->get('/privacy')->assertOk()->assertDontSee('class="footer-contact-details"', false)
            ->assertDontSee('invalid-email')->assertDontSee('invalid-phone');
    }

    public function test_footer_and_homepage_omit_missing_social_links_and_use_saved_destinations(): void
    {
        OrganizationSettings::current()->update([
            'facebook_url' => 'https://example.test/facebook', 'twitter_url' => null,
            'linkedin_url' => 'javascript:alert(1)', 'youtube_url' => null,
        ]);

        $this->get('/')->assertOk()->assertSee('href="https://example.test/facebook"', false)
            ->assertDontSee('aria-label="Twitter"', false)->assertDontSee('href="#"', false)
            ->assertDontSee('href="javascript:', false)->assertSee('class="footer-legal"', false);
    }

    public function test_footer_uses_the_dedicated_legal_pages_instead_of_legacy_external_urls(): void
    {
        OrganizationSettings::current()->update([
            'privacy_policy_url' => 'https://example.test/old-privacy',
            'terms_of_service_url' => 'https://example.test/old-terms',
        ]);

        $this->get('/contact')->assertOk()->assertSee('href="'.route('privacy').'"', false)
            ->assertSee('href="'.route('tos').'"', false)
            ->assertDontSee('https://example.test/old-privacy', false)
            ->assertDontSee('https://example.test/old-terms', false);
    }

    public function test_web_development_project_links_use_real_portfolio_records(): void
    {
        $project = PortfolioItem::create([
            'title' => 'Actual website portfolio topic', 'slug' => 'actual-website-topic', 'client' => 'Portfolio attribution',
            'category' => 'wordpress', 'tags' => ['WordPress'], 'description' => 'A portfolio description.',
        ]);

        $this->get('/web-development')->assertOk()->assertSee('Actual website portfolio topic')
            ->assertSee('href="'.route('our-works.show', $project).'"', false)
            ->assertDontSee('href="#"', false)->assertDontSee('Tropicana (PepsiCo)');
    }

    public function test_legacy_contact_forms_use_published_service_options_from_the_dashboard(): void
    {
        Service::factory()->create(['title' => 'Available custom offering', 'is_published' => true]);
        Service::factory()->create(['title' => 'Unpublished custom offering', 'is_published' => false]);

        foreach (['/', '/web-development', '/seo', '/cloud-solution'] as $path) {
            $this->get($path)->assertOk()->assertSee('Available custom offering')->assertDontSee('Unpublished custom offering')
                ->assertSee('aria-label="Email address"', false);
        }
    }
}
