<?php

namespace Tests\Feature;

use App\Filament\Resources\DiagnosticPageSettingsResource\Pages\EditDiagnosticPageSettings;
use App\Models\DiagnosticPageSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiagnosticFaqSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_replaces_faqs_and_both_visible_answers_and_schema_follow_the_saved_order(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])->fillForm([
            'faq_title' => 'Your software questions',
            'faqs' => [['question' => 'Second question?', 'answer' => 'Second answer.'], ['question' => 'First question?', 'answer' => 'First answer.']],
        ])->call('save')->assertHasNoFormErrors();

        $this->assertSame('Second question?', $settings->fresh()->faqs[0]['question']);
        $response = $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('Your software questions')
            ->assertSeeInOrder(['<summary>Second question?</summary>', '<summary>First question?</summary>'], false)
            ->assertDontSee('What is DiagnoSolution?');
        $this->assertSame(2, substr_count($response->getContent(), 'Second answer.'));
    }

    public function test_clearing_all_faqs_hides_the_section_and_faq_schema(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['faqs' => []])->call('save')->assertHasNoFormErrors();

        $this->assertSame([], $settings->fresh()->publicFaqs());
        $this->get(route('diagnostic-management-system'))->assertOk()->assertDontSee('id="faq"', false)->assertDontSee('FAQPage');
    }

    public function test_existing_pricing_answer_is_preserved_until_faqs_are_saved(): void
    {
        $settings = DiagnosticPageSettings::current();
        $settings->update(['pricing_faq_answer' => 'My saved pricing answer.']);
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['faq_title' => 'Questions'])->call('save')->assertHasNoFormErrors();

        $this->assertSame('My saved pricing answer.', $settings->fresh()->faqs[1]['answer']);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('My saved pricing answer.');
    }

    public function test_empty_questions_are_rejected_and_html_answers_are_escaped(): void
    {
        $settings = DiagnosticPageSettings::current();
        $this->actingAs(User::factory()->create());
        Livewire::test(EditDiagnosticPageSettings::class, ['record' => $settings->id])
            ->fillForm(['faqs' => [['question' => '', 'answer' => 'Answer']]])->call('save')->assertHasFormErrors(['faqs.0.question']);
        $this->assertNull($settings->fresh()->faqs);
        $settings->update(['faqs' => [['question' => 'Safe?', 'answer' => '<script>alert(1)</script>']]]);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
