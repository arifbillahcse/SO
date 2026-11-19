<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactPageSettingsResource\Pages\EditContactPageSettings;
use App\Mail\AffiliateReferralReceived;
use App\Mail\ContactSubmissionAutoReply;
use App\Mail\ContactSubmissionReceived;
use App\Models\ContactPageSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationRecipientsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_save_multiple_private_recipients_and_both_forms_notify_them(): void
    {
        $settings = ContactPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])->fillForm([
            'notification_email' => 'owner@example.test',
            'additional_notification_emails' => [['email' => 'sales@example.test'], ['email' => 'owner@example.test']],
        ])->call('save')->assertHasNoFormErrors();
        $this->assertSame(['owner@example.test', 'sales@example.test'], $settings->fresh()->notificationRecipients());
        $this->get('/contact')->assertOk()->assertDontSee('owner@example.test')->assertDontSee('sales@example.test');

        Mail::fake();
        $payload = ['name' => 'Jane Doe', 'email' => 'visitor@example.test', 'message' => 'Please discuss my project.'];
        $this->postJson('/contact', $payload)->assertOk();
        $this->postJson(route('affiliate-referral.store'), $payload)->assertOk();

        Mail::assertQueued(ContactSubmissionReceived::class, fn ($mail): bool => $mail->hasTo('owner@example.test') && $mail->hasTo('sales@example.test') && str_contains($mail->render(), 'Please discuss my project.'));
        Mail::assertQueued(AffiliateReferralReceived::class, fn ($mail): bool => $mail->hasTo('owner@example.test') && $mail->hasTo('sales@example.test'));
        Mail::assertQueued(ContactSubmissionAutoReply::class, fn ($mail): bool => $mail->hasTo('visitor@example.test') && ! $mail->hasTo('sales@example.test'));
    }

    public function test_additional_addresses_work_without_a_primary_and_clearing_all_uses_the_default(): void
    {
        config(['services.softorio.admin_notification_email' => 'default@example.test']);
        $settings = ContactPageSettings::current();
        $settings->update(['notification_email' => null, 'additional_notification_emails' => [' SALES@example.test ', 'sales@example.test']]);
        $this->assertSame(['sales@example.test'], $settings->notificationRecipients());

        $this->actingAs(User::factory()->create());
        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
            ->fillForm(['additional_notification_emails' => []])->call('save')->assertHasNoFormErrors();
        $this->assertSame(['default@example.test'], $settings->fresh()->notificationRecipients());
    }

    public function test_invalid_additional_email_cannot_replace_saved_settings(): void
    {
        $settings = ContactPageSettings::current();
        $settings->update(['additional_notification_emails' => ['valid@example.test']]);
        $this->actingAs(User::factory()->create());
        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
            ->fillForm(['additional_notification_emails' => [['email' => 'invalid-address']]])
            ->call('save')->assertHasFormErrors(['additional_notification_emails.0.email']);
        $this->assertSame(['valid@example.test'], $settings->fresh()->additional_notification_emails);
    }
}
