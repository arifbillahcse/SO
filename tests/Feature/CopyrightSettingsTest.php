<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Models\OrganizationSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CopyrightSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_change_copyright_on_all_public_page_footers(): void
    {
        $this->travelTo(now()->setDate(2030, 6, 1));
        $settings = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['copyright_text' => 'Copyright {year} Example Studio. All rights reserved.'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('Copyright {year} Example Studio. All rights reserved.', $settings->fresh()->copyright_text);
        foreach (['/', '/contact', '/privacy'] as $path) {
            $this->get($path)->assertOk()->assertSee('Copyright 2030 Example Studio. All rights reserved.');
        }
        $this->travelTo(now()->setDate(2031, 1, 1));
        $this->get('/')->assertOk()->assertSee('Copyright 2031 Example Studio. All rights reserved.');
    }

    public function test_clearing_the_custom_text_restores_the_default_copyright(): void
    {
        $this->travelTo(now()->setDate(2030, 6, 1));
        $settings = OrganizationSettings::current();
        $settings->update(['copyright_text' => 'Custom copyright']);
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['copyright_text' => ''])->call('save')->assertHasNoFormErrors();

        $this->assertNull($settings->fresh()->copyright_text);
        $this->get('/')->assertOk()->assertSee("\u{00A9} 2030 Softorio. All Rights Reserved.")->assertDontSee('Custom copyright');
    }

    public function test_copyright_is_plain_text_and_cannot_execute_html(): void
    {
        OrganizationSettings::current()->update(['copyright_text' => '<script>alert(1)</script> Example']);

        $this->get('/')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; Example', false)
            ->assertDontSee('<script>alert(1)</script> Example', false);
    }

    public function test_oversized_copyright_cannot_replace_the_saved_value(): void
    {
        $settings = OrganizationSettings::current();
        $settings->update(['copyright_text' => 'Saved copyright']);
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $settings->id])
            ->fillForm(['copyright_text' => str_repeat('x', 501)])->call('save')->assertHasFormErrors(['copyright_text']);

        $this->assertSame('Saved copyright', $settings->fresh()->copyright_text);
    }
}
