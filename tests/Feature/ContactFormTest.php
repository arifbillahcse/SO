<?php

namespace Tests\Feature;

use App\Mail\ContactSubmissionAutoReply;
use App\Mail\ContactSubmissionReceived;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+880 1234567890',
            'service_interested' => 'Website Development',
            'message' => 'I need a new website for my business.',
            'company_website' => '', // honeypot — must stay empty
        ], $overrides);
    }

    public function test_valid_submission_is_saved_and_sends_two_emails(): void
    {
        Mail::fake();

        $response = $this->postJson(route('contact.store'), $this->validPayload());

        $response->assertOk();
        $response->assertJsonStructure(['message']);

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'service_interested' => 'Website Development',
            'status' => 'new',
        ]);

        Mail::assertSent(ContactSubmissionReceived::class);
        Mail::assertSent(ContactSubmissionAutoReply::class, function ($mail) {
            return $mail->hasTo('jane@example.com');
        });
    }

    public function test_phone_and_service_are_optional(): void
    {
        Mail::fake();

        $response = $this->postJson(route('contact.store'), $this->validPayload([
            'phone' => '',
            'service_interested' => '',
        ]));

        $response->assertOk();
        $this->assertDatabaseCount('contact_submissions', 1);
    }

    public function test_name_is_required(): void
    {
        $response = $this->postJson(route('contact.store'), $this->validPayload(['name' => '']));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_email_is_required_and_must_be_valid(): void
    {
        $response = $this->postJson(route('contact.store'), $this->validPayload(['email' => 'not-an-email']));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_message_is_required(): void
    {
        $response = $this->postJson(route('contact.store'), $this->validPayload(['message' => '']));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('message');
    }

    public function test_honeypot_field_rejects_the_submission(): void
    {
        Mail::fake();

        $response = $this->postJson(route('contact.store'), $this->validPayload([
            'company_website' => 'https://spambot.example.com',
        ]));

        $response->assertStatus(422);
        $this->assertDatabaseCount('contact_submissions', 0);
        Mail::assertNothingSent();
    }

    public function test_sixth_submission_within_a_minute_is_rate_limited(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('contact.store'), $this->validPayload(['email' => "jane{$i}@example.com"]))
                ->assertOk();
        }

        $response = $this->postJson(route('contact.store'), $this->validPayload(['email' => 'jane6@example.com']));

        $response->assertStatus(429);
        $this->assertDatabaseCount('contact_submissions', 5);
    }

    public function test_submission_does_not_leak_into_affiliate_referrals_table(): void
    {
        Mail::fake();

        $this->postJson(route('contact.store'), $this->validPayload());

        $this->assertDatabaseCount('contact_submissions', 1);
        $this->assertDatabaseCount('affiliate_referrals', 0);
    }
}
