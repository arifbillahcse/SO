<?php

namespace Tests\Feature;

use App\Mail\ContactSubmissionReceived;
use App\Models\ContactPageSettings;
use App\Models\ContactSubmission;
use App\Models\DiagnosticPageSettings;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DiagnoSolutionLandingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_product_page_contains_the_software_screens_and_uses_only_standalone_public_assets(): void
    {
        $response = $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('From the reception counter to the signed report.')
            ->assertSee('Everything DiagnoSolution does')->assertSee('Sample data')
            ->assertSee('name="clinic_name"', false)->assertSee('name="demo_plan"', false)
            ->assertSee('action="'.route('contact.store').'"', false)
            ->assertDontSee('landingPage/')->assertDontSee('example.com.bd')
            ->assertDontSee('8801XXXXXXXXX')->assertDontSee('Most centers pick this')
            ->assertDontSee('core is offered free')->assertDontSee('Your street address');

        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        libxml_clear_errors();
        $xpath = new DOMXPath($document);
        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame(1, $xpath->query('//header[@id="navbar"]')->length);
        $this->assertSame(1, $xpath->query('//footer')->length);
        $this->assertSame(6, $xpath->query('//figure[@class="dg-slide"]')->length);
        foreach ($xpath->query('//main//img[@src]') as $image) {
            $path = parse_url($image->getAttribute('src'), PHP_URL_PATH);
            $this->assertStringStartsWith('/images/diagnocare/', $path);
            $this->assertFileExists(public_path(ltrim($path, '/')));
        }
        $schemas = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $schemas[] = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
        }
        $this->assertStringContainsString('SoftwareApplication', json_encode($schemas));
        $this->assertStringContainsString('FAQPage', json_encode($schemas));
    }

    public function test_demo_uses_backend_contact_settings_and_captcha_configuration(): void
    {
        ContactPageSettings::current()->update(['whatsapp_number' => '8801700000000', 'contact_email' => 'demo@example.test']);
        OrganizationSettings::current()->update(['recaptcha_enabled' => true, 'recaptcha_site_key' => 'demo-site-key', 'recaptcha_secret_key' => 'private-demo-key']);

        $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('https://wa.me/8801700000000', false)->assertSee('mailto:demo@example.test', false)
            ->assertSee('content="demo-site-key"', false)->assertDontSee('private-demo-key');
    }

    public function test_demo_request_saves_clinic_and_plan_in_the_admin_enquiry_and_email(): void
    {
        Mail::fake();
        $this->postJson(route('contact.store'), [
            'name' => 'Jane Doe', 'email' => 'jane@example.test', 'phone' => '+8801700000000',
            'service_interested' => 'Diagnostic Management System - DiagnoSolution demo',
            'clinic_name' => 'Example Diagnostics', 'demo_plan' => 'Standard',
            'message' => 'Please show us the report workflow.',
        ])->assertOk();

        $submission = ContactSubmission::sole();
        $this->assertSame("Please show us the report workflow.\n\nClinic / lab: Example Diagnostics\nRequested plan: Standard", $submission->message);
        Mail::assertQueued(ContactSubmissionReceived::class, fn (ContactSubmissionReceived $mail): bool => str_contains($mail->render(), 'Example Diagnostics') && str_contains($mail->render(), 'Standard'));
    }

    public function test_invalid_demo_plan_and_missing_clinic_do_not_create_enquiries(): void
    {
        Mail::fake();
        $this->postJson(route('contact.store'), [
            'name' => 'Jane Doe', 'email' => 'jane@example.test', 'message' => 'A demo please.',
            'demo_plan' => 'Unknown plan',
        ])->assertUnprocessable()->assertJsonValidationErrors(['demo_plan', 'clinic_name']);

        $this->assertDatabaseEmpty('contact_submissions');
        Mail::assertNothingOutgoing();
    }

    public function test_metadata_refresh_updates_legacy_defaults_and_preserves_admin_edits(): void
    {
        $seo = PageSeo::forRoute('diagnostic-management-system');
        $seo->update([
            'title' => 'My custom DiagnoCare title',
            'meta_description' => "Explore Softorio's diagnostic management system for patients, billing, samples and reports. Software is under development; discuss availability and setup.",
            'og_image' => 'images/custom-share.png',
        ]);
        $migration = require database_path('migrations/2026_11_18_200530_refresh_default_diagnostic_page_metadata.php');

        $migration->up();
        $migration->up();

        $seo->refresh();
        $this->assertSame('My custom DiagnoCare title', $seo->title);
        $this->assertSame('images/custom-share.png', $seo->og_image);
        $this->assertSame('Explore DiagnoCare by Softorio for diagnostic center billing, samples, lab reports and owner operations. Request a software demonstration for your center.', $seo->meta_description);
    }

    public function test_brand_rename_updates_saved_seo_and_plan_text_without_changing_other_content(): void
    {
        $seo = PageSeo::forRoute('diagnostic-management-system');
        $seo->update(['title' => 'Custom DiagnoCare package', 'meta_description' => 'Explore DIAGNOCARE with our team.']);
        $settings = DiagnosticPageSettings::current();
        $plans = $settings->plans;
        $plans[0]['name'] = 'DiagnoCare Starter';
        $plans[0]['price'] = 'BDT 2,500 / month';
        $settings->update(['plans' => $plans, 'pricing_faq_answer' => 'Ask about DiagnoCare packages.']);
        $migration = require database_path('migrations/2026_11_19_035827_rename_diagnostic_product_to_diagnosolution.php');

        $migration->up();
        $migration->up();

        $this->assertSame('Custom DiagnoSolution package', $seo->fresh()->title);
        $this->assertSame('Explore DiagnoSolution with our team.', $seo->fresh()->meta_description);
        $this->assertSame('DiagnoSolution Starter', $settings->fresh()->plans[0]['name']);
        $this->assertSame('BDT 2,500 / month', $settings->fresh()->plans[0]['price']);
        $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('DiagnoSolution')->assertDontSee('DiagnoCare')
            ->assertSee('Ask about DiagnoSolution packages.');
    }
}
