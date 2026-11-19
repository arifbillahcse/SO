<?php

namespace Tests\Feature;

use App\Filament\Resources\DiagnosticPageSettingsResource\Pages\EditDiagnosticPageSettings;
use App\Filament\Resources\DiagnosticPageSettingsResource\Pages\ListDiagnosticPageSettings;
use App\Models\ContactSubmission;
use App\Models\DiagnosticPageSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class DiagnosticPlanSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @return array<string, mixed> */
    private function plan(string $name, bool $enabled = true): array
    {
        return ['name' => $name, 'price' => 'BDT 2,000 / month', 'audience' => 'Growing clinics',
            'features' => ['Billing & reports', 'Owner dashboard'], 'badge' => 'Recommended',
            'button_label' => 'Request this package', 'is_highlighted' => true, 'is_enabled' => $enabled];
    }

    /** @return array<string, mixed> */
    private function formPlan(string $name, bool $enabled = true): array
    {
        $plan = $this->plan($name, $enabled);
        $plan['features'] = array_map(fn (string $feature): array => ['feature' => $feature], $plan['features']);

        return $plan;
    }

    public function test_admin_edits_plans_and_related_copy_across_cards_dropdown_and_structured_faq(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])->fillForm([
            'plans_title' => 'Choose your package', 'plans_introduction' => 'Annual billing available on request.',
            'plans' => [$this->formPlan('Clinic Plus'), $this->formPlan('Starter'), $this->formPlan('Hidden package', false)],
            'faqs' => [['question' => 'How is pricing calculated?', 'answer' => 'Your branches determine the final quote.']],
            'demo_plan_label' => 'Preferred package', 'undecided_plan_label' => 'Help me choose',
        ])->call('save')->assertHasNoFormErrors();

        $this->assertSame(['Clinic Plus', 'Starter'], $settings->fresh()->planChoices());
        $response = $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('Choose your package')
            ->assertSee('Annual billing available on request.')->assertSee('BDT 2,000 / month')
            ->assertSee('Recommended')->assertSee('Request this package')->assertSee('Preferred package')
            ->assertSee('Help me choose')->assertSee('How is pricing calculated?')
            ->assertSee('Your branches determine the final quote.')
            ->assertSeeInOrder(['<h3>Clinic Plus</h3>', '<h3>Starter</h3>'], false)
            ->assertSee('<option value="Clinic Plus">Clinic Plus</option>', false)
            ->assertDontSee('Hidden package')->assertDontSee('Ask about Basic')
            ->assertDontSee('in three plans: Basic, Standard and Premium');
        $this->assertSame(2, substr_count($response->getContent(), 'Your branches determine the final quote.'));
        $this->assertSame('Choose your package', DiagnosticPageSettings::current()->plans_title);
    }

    public function test_disabled_section_preserves_saved_plans_but_removes_cards_and_named_options(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans_enabled' => false])->call('save')->assertHasNoFormErrors();

        $this->assertCount(3, $settings->fresh()->plans);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertDontSee('id="plans"', false)
            ->assertDontSee('<option value="Basic">', false)->assertSee('value="Not sure yet"', false);
    }

    public function test_removing_all_plans_does_not_restore_hardcoded_cards(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => []])->call('save')->assertHasNoFormErrors();

        $this->assertSame([], DiagnosticPageSettings::current()->publicPlans());
        $this->get(route('diagnostic-management-system'))->assertOk()->assertDontSee('id="plans"', false)
            ->assertDontSee('Ask about Basic');
    }

    public function test_only_current_published_plan_names_are_accepted_and_stored_with_the_enquiry(): void
    {
        DiagnosticPageSettings::current()->update(['plans' => [$this->plan('Clinic Plus'), $this->plan('Private plan', false)]]);
        Mail::fake();
        $payload = ['name' => 'Jane Doe', 'email' => 'jane@example.test', 'message' => 'Please arrange a demo.', 'clinic_name' => 'Example Clinic'];
        foreach (['Basic', 'Private plan', 'Invalid plan'] as $name) {
            $this->postJson(route('contact.store'), [...$payload, 'demo_plan' => $name])
                ->assertUnprocessable()->assertJsonValidationErrors('demo_plan');
        }
        $this->assertDatabaseEmpty('contact_submissions');
        Mail::assertNothingOutgoing();

        $this->postJson(route('contact.store'), [...$payload, 'demo_plan' => 'Clinic Plus'])->assertOk();
        $this->assertStringContainsString('Requested plan: Clinic Plus', ContactSubmission::sole()->message);
    }

    public function test_duplicate_and_reserved_plan_names_cannot_replace_saved_plans(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => [$this->formPlan('Duplicate'), $this->formPlan('Duplicate')]])
            ->call('save')->assertHasFormErrors(['plans.0.name']);
        $this->assertSame(['Basic', 'Standard', 'Premium'], $settings->fresh()->planChoices());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => [$this->formPlan('Not sure yet')]])->call('save')->assertHasFormErrors(['plans.0.name']);
        $this->assertSame(['Basic', 'Standard', 'Premium'], $settings->fresh()->planChoices());
    }

    public function test_plan_text_is_escaped_and_guests_cannot_manage_settings(): void
    {
        $settings = DiagnosticPageSettings::current();
        $settings->update(['plans' => [$this->plan('<script>alert(1)</script>')]]);
        $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/admin/diagnostic-page-settings')->assertRedirect('/admin/login');
        $this->get('/admin/diagnostic-page-settings/'.$settings->id.'/edit')->assertRedirect('/admin/login');
    }

    public function test_admin_can_open_settings_before_the_public_page_has_created_them(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(ListDiagnosticPageSettings::class)->assertOk();
        $this->assertDatabaseCount('diagnostic_page_settings', 1);
        $this->assertSame(['Basic', 'Standard', 'Premium'], DiagnosticPageSettings::current()->planChoices());
    }

    public function test_admin_can_set_and_clear_a_plan_button_destination(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        $plan = $this->formPlan('Starter');
        $plan['button_url'] = 'https://software.example.test/signup?plan=starter';
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => [$plan]])->call('save')->assertHasNoFormErrors();
        $this->assertSame($plan['button_url'], $settings->fresh()->plans[0]['button_url']);
        $this->get(route('diagnostic-management-system'))->assertOk()
            ->assertSee('href="https://software.example.test/signup?plan=starter"', false)
            ->assertDontSee('data-plan="Starter"', false);

        $plan['button_url'] = '';
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => [$plan]])->call('save')->assertHasNoFormErrors();
        $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('href="#demo"', false)
            ->assertSee('data-plan="Starter"', false);
    }

    public function test_unsafe_button_links_are_rejected_and_stored_unsafe_links_fall_back_to_the_demo(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        $plan = $this->formPlan('Starter');
        $plan['button_url'] = 'javascript:alert(1)';
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['plans' => [$plan]])->call('save')->assertHasFormErrors(['plans.0.button_url']);
        $this->assertSame(['Basic', 'Standard', 'Premium'], $settings->fresh()->planChoices());

        $plan = $this->plan('Starter');
        $plan['button_url'] = 'javascript:alert(1)';
        $settings->update(['plans' => [$plan]]);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertDontSee('javascript:alert(1)', false)
            ->assertSee('data-plan="Starter"', false);
    }
}
