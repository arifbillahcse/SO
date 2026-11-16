<?php

namespace Tests\Feature;

use App\Filament\Resources\LegalPagesResource\Pages\EditLegalPages;
use App\Models\OrganizationSettings;
use App\Models\PageSeo;
use App\Models\User;
use Database\Seeders\LegalPagesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_both_policy_pages_have_starter_content_canonicals_and_footer_links(): void
    {
        foreach (['tos' => 'Terms &amp; Conditions', 'privacy' => 'Privacy Policy'] as $page => $title) {
            $this->get("/{$page}")->assertOk()->assertSee($title, false)
                ->assertSee('Effective date:')->assertSee('November 17, 2026')
                ->assertSee('rel="canonical" href="https://softorio.com/'.$page.'"', false)
                ->assertSee('href="'.route('tos').'"', false)
                ->assertSee('href="'.route('privacy').'"', false);
        }

        $this->get('/')->assertOk()->assertSee('class="footer-legal"', false)
            ->assertSee('href="'.route('tos').'"', false)->assertSee('href="'.route('privacy').'"', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('<loc>https://softorio.com/tos</loc>', false)
            ->assertSee('<loc>https://softorio.com/privacy</loc>', false);
        $this->assertNotNull(PageSeo::where('route_name', 'tos')->first());
        $this->assertNotNull(PageSeo::where('route_name', 'privacy')->first());
    }

    public function test_admin_can_edit_both_pages_and_changes_are_visible_immediately(): void
    {
        $organization = OrganizationSettings::current();
        $organization->update(['name' => 'Softorio', 'email' => 'preserved@example.test']);
        $this->actingAs(User::factory()->create());

        $editor = Livewire::test(EditLegalPages::class, ['record' => $organization->id]);
        $editor->assertFormSet(['tos_title' => 'Terms & Conditions', 'privacy_title' => 'Privacy Policy']);
        $editor->fillForm([
            'tos_title' => 'Our website terms', 'tos_intro' => 'Updated terms introduction.',
            'tos_body' => '<h2>Custom terms section</h2><p>Edited project conditions.</p>',
            'tos_effective_date' => '2026-11-18',
            'privacy_title' => 'Our privacy notice', 'privacy_intro' => 'Updated privacy introduction.',
            'privacy_body' => '<h2>Custom privacy section</h2><p>Edited privacy practices.</p>',
            'privacy_effective_date' => '2026-11-19',
        ])->call('save')->assertHasNoFormErrors();

        $this->get('/tos')->assertOk()->assertSee('Our website terms')->assertSee('Updated terms introduction.')
            ->assertSee('<h2>Custom terms section</h2>', false)->assertSee('Edited project conditions.')->assertSee('November 18, 2026');
        $this->get('/privacy')->assertOk()->assertSee('Our privacy notice')->assertSee('Updated privacy introduction.')
            ->assertSee('<h2>Custom privacy section</h2>', false)->assertSee('Edited privacy practices.')->assertSee('November 19, 2026');
        $this->assertSame('preserved@example.test', $organization->fresh()->email);
        $this->assertSame('Our website terms', $organization->fresh()->tos_title);
    }

    public function test_guest_cannot_edit_legal_pages_and_required_fields_are_validated(): void
    {
        $organization = OrganizationSettings::current();
        $this->get('/admin/legal-pages')->assertRedirect('/admin/login');
        $this->get('/admin/legal-pages/'.$organization->id.'/edit')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        Livewire::test(EditLegalPages::class, ['record' => $organization->id])->fillForm([
            'tos_title' => '', 'tos_intro' => '', 'tos_body' => '', 'tos_effective_date' => '',
            'privacy_title' => '', 'privacy_intro' => '', 'privacy_body' => '', 'privacy_effective_date' => '',
        ])->call('save')->assertHasFormErrors([
            'tos_title', 'tos_intro', 'tos_body', 'tos_effective_date',
            'privacy_title', 'privacy_intro', 'privacy_body', 'privacy_effective_date',
        ]);
        $this->assertNull($organization->fresh()->tos_title);
    }

    public function test_seeding_fills_missing_policy_content_and_preserves_admin_changes(): void
    {
        $organization = OrganizationSettings::current();
        $organization->update(['tos_title' => 'Owner edited terms', 'privacy_intro' => 'Owner edited privacy.']);

        $this->seed(LegalPagesSeeder::class);
        $this->seed(LegalPagesSeeder::class);

        $this->assertSame('Owner edited terms', $organization->fresh()->tos_title);
        $this->assertSame('Owner edited privacy.', $organization->fresh()->privacy_intro);
        $this->assertNotEmpty($organization->fresh()->tos_body);
        $this->assertNotEmpty($organization->fresh()->privacy_body);
    }

    public function test_formatted_policy_content_is_sanitized_and_headings_are_escaped(): void
    {
        OrganizationSettings::current()->update([
            'tos_title' => '<script>alert("title")</script>',
            'tos_body' => '<h2>Safe heading</h2><p>Safe content.</p><script>alert("policy")</script><a href="javascript:alert(1)">Bad link</a>',
        ]);

        $this->get('/tos')->assertOk()->assertSee('Safe heading')->assertSee('Safe content.')
            ->assertDontSee('<script>alert("title")</script>', false)
            ->assertDontSee('<script>alert("policy")</script>', false)
            ->assertDontSee('href="javascript:', false);
    }
}
