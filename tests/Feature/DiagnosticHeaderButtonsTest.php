<?php

namespace Tests\Feature;

use App\Filament\Resources\DiagnosticPageSettingsResource\Pages\EditDiagnosticPageSettings;
use App\Models\DiagnosticPageSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiagnosticHeaderButtonsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_change_both_button_labels_and_destinations(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])->fillForm([
            'hero_primary_text' => 'Open software', 'hero_primary_url' => 'https://software.example.test/signup',
            'hero_secondary_text' => 'Talk to us', 'hero_secondary_url' => '/contact',
        ])->call('save')->assertHasNoFormErrors();
        $this->assertSame('/contact', $settings->fresh()->hero_secondary_url);
        $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('href="https://software.example.test/signup">Open software</a>', false)
            ->assertSee('href="/contact">Talk to us</a>', false);
    }

    public function test_blank_values_restore_defaults_and_section_links_are_allowed(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['hero_primary_url' => '#views'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('#views', $settings->fresh()->heroButtons(null)[0]['url']);
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['hero_primary_url' => '', 'hero_primary_text' => ''])->call('save')->assertHasNoFormErrors();
        $this->assertSame(['text' => 'Book a demo', 'url' => '#demo'], $settings->fresh()->heroButtons(null)[0]);
        $this->assertSame('https://wa.me/8801700000000', $settings->fresh()->heroButtons('https://wa.me/8801700000000')[1]['url']);
    }

    public function test_unsafe_links_are_rejected_and_cannot_be_rendered_from_stored_data(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        foreach (['javascript:alert(1)', '//attacker.test', '/'.chr(92).'attacker.test'] as $url) {
            Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
                ->fillForm(['hero_primary_url' => $url])->call('save')->assertHasFormErrors(['hero_primary_url']);
        }
        $settings->update(['hero_primary_url' => 'javascript:alert(1)', 'hero_primary_text' => '<script>bad</script>']);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertDontSee('javascript:alert(1)', false)
            ->assertSee('&lt;script&gt;bad&lt;/script&gt;', false);
    }
}
