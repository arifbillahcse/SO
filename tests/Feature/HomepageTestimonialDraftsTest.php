<?php

namespace Tests\Feature;

use App\Filament\Resources\TestimonialResource\Pages\EditTestimonial;
use App\Filament\Resources\TestimonialResource\Pages\ListTestimonials;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\HomepageTestimonialDraftsSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomepageTestimonialDraftsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_three_editable_drafts_are_listed_in_admin_and_not_presented_as_public_customer_feedback(): void
    {
        $this->seed(HomepageTestimonialDraftsSeeder::class);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertSame(0, Testimonial::where('featured', true)->count());
        $this->actingAs(User::factory()->create());

        Livewire::test(ListTestimonials::class)->assertCanSeeTableRecords(Testimonial::all())
            ->assertSee('Website client - draft')->assertSee('Cloud services client - draft')->assertSee('SEO client - draft');
        $this->get('/')->assertOk()->assertDontSee('Draft wording for review:')->assertDontSee('id="testimonials"', false);
    }

    public function test_import_preserves_edits_and_existing_published_testimonials_when_repeated(): void
    {
        $existing = Testimonial::factory()->create(['name' => 'Existing customer', 'featured' => true]);
        $this->seed(HomepageTestimonialDraftsSeeder::class);
        $draft = Testimonial::where('source_key', 'softorio-homepage-draft-website')->firstOrFail();
        $draft->update(['name' => 'Administrator edited name', 'role' => 'Edited role', 'text' => 'Edited quote.', 'sort_order' => 5]);
        $before = Testimonial::orderBy('id')->get()->toArray();

        $this->seed(HomepageTestimonialDraftsSeeder::class);

        $this->assertSame($before, Testimonial::orderBy('id')->get()->toArray());
        $this->assertDatabaseCount('testimonials', 4);
        $this->assertTrue($existing->fresh()->featured);
    }

    public function test_admin_edits_publish_and_unpublish_actions_control_the_homepage(): void
    {
        $this->seed(HomepageTestimonialDraftsSeeder::class);
        $draft = Testimonial::where('source_key', 'softorio-homepage-draft-cloud')->firstOrFail();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditTestimonial::class, ['record' => $draft->id])->fillForm([
            'name' => 'Customer supplied for test', 'role' => 'Customer role for test',
            'text' => 'Approved customer quote supplied for this test.', 'featured' => true,
        ])->call('save')->assertHasNoFormErrors();

        $this->get('/')->assertOk()->assertSee('Customer supplied for test')
            ->assertSee('Approved customer quote supplied for this test.')->assertDontSee('Draft wording for review:');
        Livewire::test(EditTestimonial::class, ['record' => $draft->id])->fillForm(['featured' => false])
            ->call('save')->assertHasNoFormErrors();
        $this->get('/')->assertOk()->assertDontSee('Approved customer quote supplied for this test.');
    }
}
