<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Mail\ContactSubmissionReceived;
use App\Models\OrganizationSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RecaptchaTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function enableCaptcha(): OrganizationSettings
    {
        config(['app.url' => 'https://softorio.test']);
        $settings = OrganizationSettings::current();
        $settings->update(['recaptcha_enabled' => true, 'recaptcha_site_key' => 'test-site-key', 'recaptcha_secret_key' => 'test-secret-key']);

        return $settings;
    }

    /** @return array<string, string> */
    private function payload(): array
    {
        return ['name' => 'Jane Doe', 'email' => 'jane@example.test', 'message' => 'Please discuss my project.', 'g-recaptcha-response' => 'test-token'];
    }

    public function test_admin_saves_encrypted_credentials_preserves_blank_secret_and_can_disable_protection(): void
    {
        $settings = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['recaptcha_enabled' => true, 'recaptcha_site_key' => 'test-site-key', 'recaptcha_secret_key' => 'test-secret-key'])
            ->call('save')->assertHasNoFormErrors();

        $settings->refresh();
        $this->assertTrue($settings->recaptcha_enabled);
        $this->assertSame('test-secret-key', $settings->recaptcha_secret_key);
        $this->assertNotSame('test-secret-key', $settings->getRawOriginal('recaptcha_secret_key'));
        $this->assertArrayNotHasKey('recaptcha_secret_key', $settings->toArray());
        $this->get('/contact')->assertOk()->assertSee('content="test-site-key"', false)->assertDontSee('test-secret-key');

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['recaptcha_secret_key' => ''])->call('save')->assertHasNoFormErrors();
        $this->assertSame('test-secret-key', $settings->fresh()->recaptcha_secret_key);
        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['recaptcha_secret_key' => 'replacement-secret'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('replacement-secret', $settings->fresh()->recaptcha_secret_key);

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['recaptcha_enabled' => false, 'recaptcha_secret_key' => ''])
            ->call('save')->assertHasNoFormErrors();
        $this->assertFalse($settings->fresh()->recaptcha_enabled);
        $this->assertSame('replacement-secret', $settings->fresh()->recaptcha_secret_key);
        $this->get('/contact')->assertOk()->assertDontSee('name="recaptcha-site-key"', false);
    }

    public function test_enabling_protection_requires_both_credentials(): void
    {
        $settings = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['recaptcha_enabled' => true, 'recaptcha_site_key' => '', 'recaptcha_secret_key' => ''])
            ->call('save')->assertHasFormErrors(['recaptcha_site_key', 'recaptcha_secret_key']);
        $this->assertFalse($settings->fresh()->recaptcha_enabled);
    }

    public function test_missing_token_is_rejected_on_both_submission_endpoints_without_saving_or_email(): void
    {
        $this->enableCaptcha();
        Http::fake();
        Mail::fake();
        $payload = $this->payload();
        unset($payload['g-recaptcha-response']);
        foreach (['contact.store', 'affiliate-referral.store'] as $route) {
            $this->postJson(route($route), $payload)->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        }
        $this->assertDatabaseEmpty('contact_submissions');
        $this->assertDatabaseEmpty('affiliate_referrals');
        Http::assertNothingSent();
        Mail::assertNothingOutgoing();
    }

    public function test_verified_tokens_allow_enquiries_and_referrals(): void
    {
        $this->enableCaptcha();
        Mail::fake();
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'hostname' => 'www.softorio.test'])]);

        foreach (['contact.store', 'affiliate-referral.store'] as $route) {
            $this->postJson(route($route), $this->payload())->assertOk();
        }
        $this->assertDatabaseCount('contact_submissions', 1);
        $this->assertDatabaseCount('affiliate_referrals', 1);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
            && $request['secret'] === 'test-secret-key' && $request['response'] === 'test-token');
        Http::assertSentCount(2);
        Mail::assertQueued(ContactSubmissionReceived::class);
    }

    /** @return array<string, array{array<string, mixed>, int}> */
    public static function rejectedResponses(): array
    {
        return [
            'invalid or reused token' => [['success' => false, 'error-codes' => ['timeout-or-duplicate']], 200],
            'different website' => [['success' => true, 'hostname' => 'attacker.test'], 200],
            'missing host' => [['success' => true], 200],
            'invalid response' => [[], 200],
            'upstream error' => [['success' => true, 'hostname' => 'softorio.test'], 500],
        ];
    }

    /** @param array<string, mixed> $body */
    #[DataProvider('rejectedResponses')]
    public function test_failed_verification_never_saves_an_enquiry_or_sends_email(array $body, int $status): void
    {
        $this->enableCaptcha();
        Mail::fake();
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response($body, $status)]);
        $this->postJson(route('contact.store'), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertDatabaseEmpty('contact_submissions');
        Mail::assertNothingOutgoing();
    }

    public function test_google_connection_failure_fails_closed(): void
    {
        $this->enableCaptcha();
        Mail::fake();
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::failedConnection()]);
        $this->postJson(route('contact.store'), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertDatabaseEmpty('contact_submissions');
        Mail::assertNothingOutgoing();
    }

    public function test_enabled_but_missing_credentials_fails_closed(): void
    {
        OrganizationSettings::current()->update(['recaptcha_enabled' => true]);
        Http::fake();
        Mail::fake();
        $this->postJson(route('contact.store'), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        $this->assertDatabaseEmpty('contact_submissions');
        Http::assertNothingSent();
        Mail::assertNothingOutgoing();
    }

    public function test_disabled_captcha_does_not_require_tokens_or_contact_google(): void
    {
        Http::fake();
        Mail::fake();
        $payload = $this->payload();
        unset($payload['g-recaptcha-response']);
        $this->postJson(route('contact.store'), $payload)->assertOk();
        $this->assertDatabaseCount('contact_submissions', 1);
        Http::assertNothingSent();
    }
}
