<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactPageSettingsResource\Pages\EditContactPageSettings;
use App\Models\ContactPageSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsappChatTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_default_chat_button_uses_requested_number_on_public_pages_and_is_absent_in_admin(): void
    {
        foreach (['/', '/services', '/privacy', '/tos'] as $path) {
            $this->get($path)->assertOk()->assertSee('class="whatsapp-chat"', false)
                ->assertSee('href="https://wa.me/8801779440297"', false)
                ->assertSee('aria-label="Chat with Softorio on WhatsApp (opens in a new tab)"', false)
                ->assertSee('rel="noopener noreferrer"', false);
        }

        $this->get('/admin/login')->assertOk()->assertDontSee('class="whatsapp-chat"', false);
    }

    public function test_admin_can_disable_change_number_and_reenable_without_losing_the_saved_number(): void
    {
        $settings = ContactPageSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
            ->fillForm(['whatsapp_chat_enabled' => false])->call('save')->assertHasNoFormErrors();
        $this->assertFalse($settings->fresh()->whatsapp_chat_enabled);
        $this->assertSame('+8801779440297', $settings->fresh()->whatsapp_number);
        $this->get('/')->assertOk()->assertDontSee('class="whatsapp-chat"', false);

        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
            ->fillForm(['whatsapp_number' => '+8801712345678', 'whatsapp_chat_enabled' => true])
            ->call('save')->assertHasNoFormErrors();
        $this->assertTrue($settings->fresh()->whatsapp_chat_enabled);
        $this->assertSame('+8801712345678', $settings->fresh()->whatsapp_number);
        $this->get('/')->assertOk()->assertSee('href="https://wa.me/8801712345678"', false)
            ->assertDontSee('https://wa.me/8801779440297', false);
    }

    public function test_invalid_or_missing_enabled_number_is_rejected_and_guests_cannot_change_settings(): void
    {
        $settings = ContactPageSettings::current();
        $this->get('/admin/contact-page-settings/'.$settings->id.'/edit')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        foreach (['javascript:alert(1)', '01779440297', '+880 1779440297', ''] as $number) {
            Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
                ->fillForm(['whatsapp_chat_enabled' => true, 'whatsapp_number' => $number])
                ->call('save')->assertHasFormErrors(['whatsapp_number']);
        }

        $this->assertSame('+8801779440297', $settings->fresh()->whatsapp_number);
        Livewire::test(EditContactPageSettings::class, ['record' => $settings->id])
            ->fillForm(['whatsapp_chat_enabled' => false, 'whatsapp_number' => ''])
            ->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertOk()->assertDontSee('class="whatsapp-chat"', false);
    }

    public function test_invalid_saved_number_hides_the_button_and_digits_without_plus_are_supported(): void
    {
        $settings = ContactPageSettings::current();
        $settings->update(['whatsapp_chat_enabled' => true, 'whatsapp_number' => 'invalid']);
        $this->get('/privacy')->assertOk()->assertDontSee('class="whatsapp-chat"', false);

        $settings->update(['whatsapp_number' => '8801779440297']);
        $this->get('/privacy')->assertOk()->assertSee('class="whatsapp-chat"', false)
            ->assertSee('href="https://wa.me/8801779440297"', false);
    }
}
