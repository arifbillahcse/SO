<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactPageSettingsResource\Pages\EditContactPageSettings;
use App\Mail\ContactSubmissionAutoReply;
use App\Mail\ContactSubmissionReceived;
use App\Models\ContactPageSettings;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use App\Models\Service;
use App\Models\User;
use App\Services\Seo\SeoAuditor;
use Database\Seeders\ContactPageSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_contact_page_uses_organization_fallbacks_published_services_and_real_navigation_links(): void
    {
        OrganizationSettings::current()->update(['email' => 'contact@example.test', 'telephone' => '+880 1234567890']);
        Service::factory()->create(['title' => 'Visible custom service', 'is_published' => true]);
        Service::factory()->create(['title' => 'Hidden custom service', 'is_published' => false]);

        $this->get('/contact')->assertOk()->assertSee('contact@example.test')->assertSee('tel:+8801234567890', false)
            ->assertSee('Visible custom service')->assertDontSee('Hidden custom service')
            ->assertSee('name="_token"', false)->assertSee('name="company_website"', false);
        $this->get('/')->assertOk()->assertSee('href="'.route('contact').'"', false);
        $this->assertDatabaseCount('contact_page_settings', 1);
    }

    public function test_admin_changes_page_details_form_text_steps_and_faqs_and_updates_appear_publicly(): void
    {
        $settings = ContactPageSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])->fillForm([
            'hero_title' => 'Discuss your next idea', 'hero_description' => 'A custom page introduction.',
            'contact_email' => 'hello@example.test', 'contact_phone' => '+880 1712345678',
            'whatsapp_number' => '+8801712345678', 'address' => "Office address\nDhaka", 'business_hours' => 'Monday to Friday, 10 to 6',
            'map_url' => 'https://maps.google.com/?q=Dhaka',
            'form_title' => 'Ask us a question', 'form_description' => 'A custom form introduction.',
            'submit_label' => 'Send enquiry', 'success_message' => 'Your enquiry is recorded.',
            'notification_email' => 'private-inbox@example.test',
            'next_steps' => [['title' => 'Custom first step', 'description' => 'A custom next step.']],
            'faqs' => [['question' => 'A custom question?', 'answer' => 'A custom answer.']],
        ])->call('save')->assertHasNoFormErrors();

        $this->get('/contact')->assertOk()->assertSee('Discuss your next idea')->assertSee('hello@example.test')
            ->assertSee('https://wa.me/8801712345678', false)->assertSee('Office address')->assertSee('Monday to Friday')
            ->assertSee('Ask us a question')->assertSee('Send enquiry')->assertSee('Custom first step')->assertSee('A custom answer.')
            ->assertDontSee('private-inbox@example.test');
        $this->assertSame('Your enquiry is recorded.', $settings->fresh()->success_message);
    }

    public function test_invalid_settings_are_rejected_and_guests_cannot_manage_page_content(): void
    {
        $settings = ContactPageSettings::current();
        $this->get('/admin/contact-page-settings/'.$settings->id.'/edit')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])->fillForm([
            'contact_email' => 'not-an-email', 'notification_email' => 'bad-inbox',
            'whatsapp_number' => 'not-a-number', 'map_url' => 'javascript:alert(1)', 'hero_title' => '',
        ])->call('save')->assertHasFormErrors(['contact_email', 'notification_email', 'whatsapp_number', 'map_url', 'hero_title']);

        $this->assertSame("Let's talk about your next project.", $settings->fresh()->hero_title);
        $this->assertNull($settings->fresh()->contact_email);
    }

    public function test_custom_notification_recipient_and_success_text_are_used_by_the_existing_contact_endpoint(): void
    {
        Mail::fake();
        ContactPageSettings::current()->update(['notification_email' => 'new-inbox@example.test', 'success_message' => 'Your enquiry is recorded.']);

        $this->postJson('/contact', [
            'name' => 'Test Visitor', 'email' => 'visitor@example.test', 'message' => 'A project enquiry for this test.',
        ])->assertOk()->assertJsonPath('message', 'Your enquiry is recorded.');

        $this->assertDatabaseHas('contact_submissions', ['name' => 'Test Visitor', 'email' => 'visitor@example.test', 'status' => 'new']);
        Mail::assertQueued(ContactSubmissionReceived::class, fn ($mail): bool => $mail->hasTo('new-inbox@example.test'));
        Mail::assertQueued(ContactSubmissionAutoReply::class, fn ($mail): bool => $mail->hasTo('visitor@example.test'));
    }

    public function test_form_works_without_javascript_and_redirects_to_a_confirmation_after_submission(): void
    {
        Mail::fake();
        ContactPageSettings::current()->update(['success_message' => 'Your enquiry is recorded.']);

        $this->from('/contact')->post('/contact', [
            'name' => 'Native form visitor', 'email' => 'native@example.test', 'message' => 'A non-JavaScript message.',
        ])->assertRedirect(route('contact'))->assertSessionHas('contact_success', 'Your enquiry is recorded.');

        $this->assertDatabaseHas('contact_submissions', ['email' => 'native@example.test']);
        $this->get('/contact')->assertOk()->assertSee('Your enquiry is recorded.');
    }

    public function test_native_form_validation_keeps_user_input_and_does_not_save_or_send_invalid_submissions(): void
    {
        Mail::fake();

        $this->from('/contact')->post('/contact', ['name' => 'Keep my name', 'email' => 'invalid', 'message' => ''])
            ->assertRedirect('/contact')->assertSessionHasErrors(['email', 'message'])->assertSessionHasInput('name', 'Keep my name');

        $this->assertDatabaseCount('contact_submissions', 0);
        Mail::assertNothingOutgoing();
    }

    public function test_contact_seo_is_registered_and_import_preserves_page_and_seo_edits(): void
    {
        $this->seed(ContactPageSeeder::class);
        $settings = ContactPageSettings::current();
        $seo = PageSeo::forRoute('contact');
        $this->assertSame([], $seo->missingFields());
        $this->assertTrue(app(SeoAuditor::class)->scan($seo)->seo_checks['rendered']);
        $this->get('/contact')->assertOk()->assertSee('ContactPage')->assertSee($seo->title);
        $this->get('/sitemap.xml')->assertOk()->assertSee('https://softorio.com/contact', false);
        $settings->update(['hero_title' => 'Administrator contact heading']);
        $seo->update(['title' => 'Administrator contact SEO', 'robots_index' => false]);

        $this->seed(ContactPageSeeder::class);

        $this->assertSame('Administrator contact heading', $settings->fresh()->hero_title);
        $this->assertSame('Administrator contact SEO', $seo->fresh()->title);
        $this->assertFalse($seo->fresh()->robots_index);
        $this->assertDatabaseCount('contact_page_settings', 1);
    }

    public function test_text_is_escaped_optional_sections_can_be_removed_and_unsafe_saved_urls_are_not_linked(): void
    {
        ContactPageSettings::current()->update([
            'hero_title' => '<script>alert("heading")</script>',
            'address' => '<script>alert("address")</script>',
            'map_url' => 'javascript:alert(1)', 'whatsapp_number' => '<script>bad</script>',
            'next_steps' => [], 'faqs' => [],
        ]);

        $this->get('/contact')->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("heading")</script>', false)->assertDontSee('<script>alert("address")</script>', false)
            ->assertDontSee('href="javascript:', false)->assertDontSee('https://wa.me/', false)
            ->assertDontSee('class="contact-next"', false)->assertDontSee('class="contact-faq"', false);
    }
}
