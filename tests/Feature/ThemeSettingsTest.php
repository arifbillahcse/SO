<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Models\OrganizationSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_change_the_default_mode_and_public_pages_use_it(): void
    {
        $organization = OrganizationSettings::current();
        $this->assertSame('light', $organization->default_theme);
        $this->actingAs(User::factory()->create());

        foreach (['dark', 'auto', 'light'] as $mode) {
            Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
                ->fillForm(['default_theme' => $mode])->call('save')->assertHasNoFormErrors();

            $this->assertSame($mode, $organization->fresh()->default_theme);
            $resolvedMode = $mode === 'dark' ? 'dark' : 'light';
            foreach (['/', '/privacy'] as $path) {
                $this->get($path)->assertOk()->assertSee('data-theme="'.$resolvedMode.'" data-default-theme="'.$mode.'"', false);
            }
        }
    }

    public function test_invalid_or_missing_mode_cannot_replace_saved_setting(): void
    {
        $organization = OrganizationSettings::current();
        $organization->update(['default_theme' => 'dark']);
        $this->actingAs(User::factory()->create());

        foreach (['invalid', null] as $mode) {
            Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
                ->fillForm(['default_theme' => $mode])->call('save')->assertHasFormErrors(['default_theme']);
            $this->assertSame('dark', $organization->fresh()->default_theme);
        }
    }

    public function test_unrecognized_stored_mode_safely_falls_back_to_light(): void
    {
        OrganizationSettings::current()->update(['default_theme' => 'invalid']);

        $this->get('/')->assertOk()->assertSee('data-theme="light" data-default-theme="light"', false);
    }
}
