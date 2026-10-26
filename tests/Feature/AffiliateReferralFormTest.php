<?php

namespace Tests\Feature;

use App\Mail\AffiliateReferralAutoReply;
use App\Mail\AffiliateReferralReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AffiliateReferralFormTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Referrer',
            'email' => 'john@example.com',
            'phone' => '+880 1234567890',
            'message' => "I'd like to refer a friend who runs an eCommerce store.",
            'company_website' => '', // honeypot — must stay empty
        ], $overrides);
    }

    public function test_valid_submission_is_saved_with_mapped_field_names_and_sends_two_emails(): void
    {
        Mail::fake();

        $response = $this->postJson(route('affiliate-referral.store'), $this->validPayload());

        $response->assertOk();

        // Note: form sends name/email/phone, but the model columns are
        // referrer_name/referrer_email/referrer_phone — confirms the
        // controller's field mapping is actually happening.
        $this->assertDatabaseHas('affiliate_referrals', [
            'referrer_name' => 'John Referrer',
            'referrer_email' => 'john@example.com',
            'status' => 'new',
        ]);

        Mail::assertSent(AffiliateReferralReceived::class);
        Mail::assertSent(AffiliateReferralAutoReply::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    public function test_name_email_and_message_are_required(): void
    {
        $response = $this->postJson(route('affiliate-referral.store'), $this->validPayload([
            'name' => '',
            'email' => '',
            'message' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('affiliate_referrals', 0);
    }

    public function test_honeypot_field_rejects_the_submission(): void
    {
        Mail::fake();

        $response = $this->postJson(route('affiliate-referral.store'), $this->validPayload([
            'company_website' => 'https://spambot.example.com',
        ]));

        $response->assertStatus(422);
        $this->assertDatabaseCount('affiliate_referrals', 0);
        Mail::assertNothingSent();
    }

    public function test_submission_does_not_leak_into_contact_submissions_table(): void
    {
        Mail::fake();

        $this->postJson(route('affiliate-referral.store'), $this->validPayload());

        $this->assertDatabaseCount('affiliate_referrals', 1);
        $this->assertDatabaseCount('contact_submissions', 0);
    }
}
