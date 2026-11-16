<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Models\OrganizationSettings;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteTrackingSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_save_verification_and_code_in_the_correct_public_positions(): void
    {
        $settings = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])->fillForm([
            'name' => 'Softorio', 'tracking_mode' => 'gtm', 'google_analytics_id' => 'G-TEST12345',
            'google_tag_manager_id' => 'GTM-TEST123', 'google_site_verification' => 'verify_token-123',
            'custom_code_enabled' => true,
            'custom_head_code' => '<meta name="custom-head-marker" content="test">',
            'custom_body_start_code' => '<script id="body-start-marker">window.startMarker = true;</script>',
            'custom_body_end_code' => '<script id="body-end-marker">window.endMarker = true;</script>',
        ])->call('save')->assertHasNoFormErrors();

        $this->assertSame('GTM-TEST123', $settings->fresh()->google_tag_manager_id);
        $this->assertTrue($settings->fresh()->custom_code_enabled);
        foreach (['/', '/services', '/our-resources'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            $this->assertSame(1, $xpath->query('/html/head/meta[@name="google-site-verification"]')->length);
            $this->assertSame('verify_token-123', $xpath->query('/html/head/meta[@name="google-site-verification"]')->item(0)->getAttribute('content'));
            $this->assertSame(1, $xpath->query('/html/head/meta[@name="custom-head-marker"]')->length);
            $this->assertSame(1, $xpath->query('/html/body/script[@id="body-start-marker"]')->length);
            $this->assertSame(1, $xpath->query('/html/body/script[@id="body-end-marker"]')->length);
            $this->assertSame('noscript', $xpath->query('/html/body/*')->item(0)->nodeName);
            $this->assertSame('body-start-marker', $xpath->query('/html/body/*')->item(1)->getAttribute('id'));
            $this->assertSame('body-end-marker', $xpath->query('/html/body/*')->item($xpath->query('/html/body/*')->length - 1)->getAttribute('id'));
            $this->assertStringContainsString('ns.html?id=GTM-TEST123', $html);
            $this->assertStringNotContainsString('gtag/js?id=', $html);
            $this->assertSame(1, substr_count($html, "event:'gtm.js'"));
        }
        $this->get('/admin')->assertOk()->assertDontSee('custom-head-marker')->assertDontSee('id="body-start-marker"', false)
            ->assertDontSee('ns.html?id=GTM-TEST123', false);
    }

    public function test_existing_analytics_id_still_works_and_mode_selection_prevents_builtin_duplicate_installations(): void
    {
        $settings = OrganizationSettings::current();
        $settings->update(['google_analytics_id' => 'G-TEST12345', 'google_tag_manager_id' => 'GTM-TEST123']);

        $this->get('/')->assertOk()->assertSee('gtag/js?id=G-TEST12345', false)
            ->assertDontSee('ns.html?id=', false);
        $settings->update(['tracking_mode' => 'gtm']);
        $this->get('/')->assertOk()->assertSee('ns.html?id=GTM-TEST123', false)
            ->assertDontSee('gtag/js?id=', false);
        $settings->update(['tracking_mode' => 'none', 'google_site_verification' => 'valid_token']);

        $this->get('/')->assertOk()->assertDontSee('www.googletagmanager.com', false)
            ->assertSee('name="google-site-verification" content="valid_token"', false);
        $this->assertSame('G-TEST12345', $settings->fresh()->google_analytics_id);
    }

    public function test_custom_snippets_can_be_disabled_without_losing_code_and_do_not_execute_blade(): void
    {
        $settings = OrganizationSettings::current();
        $settings->update([
            'custom_head_code' => '<meta name="saved-head-marker" content="test">',
            'custom_body_start_code' => '<!-- saved-start-marker -->',
            'custom_body_end_code' => '<!-- saved-end-marker {{ config("app.key") }} -->',
        ]);

        $this->get('/')->assertOk()->assertDontSee('saved-head-marker')->assertDontSee('saved-start-marker')->assertDontSee('saved-end-marker');
        $settings->update(['custom_code_enabled' => true]);
        $this->get('/')->assertOk()->assertSee('saved-head-marker')->assertSee('saved-start-marker')
            ->assertSee('{{ config("app.key") }}', false);
        $settings->update(['custom_code_enabled' => false]);

        $this->assertSame('<!-- saved-start-marker -->', $settings->fresh()->custom_body_start_code);
        $this->get('/')->assertOk()->assertDontSee('saved-start-marker');
    }

    public static function invalidSettings(): array
    {
        return [
            'invalid analytics prefix' => ['google_analytics_id', 'UA-12345'],
            'analytics markup' => ['google_analytics_id', 'G-ABC</script><script>alert(1)</script>'],
            'invalid container' => ['google_tag_manager_id', 'G-12345'],
            'invalid verification token' => ['google_site_verification', '"><script>alert(1)</script>'],
            'invalid mode' => ['tracking_mode', 'both'],
            'oversized custom code' => ['custom_head_code', str_repeat('x', 50001)],
        ];
    }

    #[DataProvider('invalidSettings')]
    public function test_invalid_google_settings_are_rejected_without_changing_saved_values(string $field, string $value): void
    {
        $settings = OrganizationSettings::current();
        $settings->update(['name' => 'Softorio']);
        $before = $settings->getAttribute($field);
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm([$field => $value])->call('save')->assertHasFormErrors([$field]);

        $this->assertSame($before, $settings->fresh()->getAttribute($field));
    }

    public function test_invalid_legacy_google_values_do_not_render_scripts_and_guests_cannot_edit_settings(): void
    {
        $settings = OrganizationSettings::current();
        $settings->update([
            'google_analytics_id' => "G-ABC');alert(1);//",
            'google_site_verification' => '"><script>alert(2)</script>',
        ]);

        $this->get('/')->assertOk()->assertDontSee('alert(1)', false)->assertDontSee('alert(2)', false)
            ->assertDontSee('gtag/js?id=', false);
        $settings->update(['tracking_mode' => 'gtm', 'google_tag_manager_id' => "GTM-ABC');alert(3);//"]);
        $this->get('/')->assertOk()->assertDontSee('alert(3)', false)->assertDontSee('ns.html?id=', false);
        $this->get('/admin/organization-settings/'.$settings->id.'/edit')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertDontSee('custom-head-marker')->assertDontSee('gtag/js?id=', false);
    }

    public function test_blank_settings_render_without_empty_google_tags_or_external_tracking(): void
    {
        OrganizationSettings::current();

        $this->get('/')->assertOk()->assertDontSee('google-site-verification', false)
            ->assertDontSee('www.googletagmanager.com', false);
    }
}
