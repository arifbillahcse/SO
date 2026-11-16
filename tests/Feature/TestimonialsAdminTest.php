<?php

namespace Tests\Feature;

use App\Filament\Resources\TestimonialResource\Pages\CreateTestimonial;
use App\Filament\Resources\TestimonialResource\Pages\EditTestimonial;
use App\Filament\Resources\TestimonialResource\Pages\ListTestimonials;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TestimonialsAdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_a_draft_with_automatic_initials_then_publish_and_edit_it(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateTestimonial::class)->fillForm([
            'name' => 'Customer Name', 'role' => 'Owner, Customer Company', 'avatar_initial' => null,
            'stars' => 4, 'text' => 'Feedback supplied for this test only.', 'sort_order' => 1,
        ])->call('create')->assertHasNoFormErrors();

        $testimonial = Testimonial::firstOrFail();
        $this->assertSame('CN', $testimonial->avatar_initial);
        $this->assertFalse($testimonial->featured);
        $this->get('/')->assertOk()->assertDontSee('Feedback supplied for this test only.')->assertDontSee('id="testimonials"', false);

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->id])
            ->fillForm(['featured' => true, 'text' => 'Updated customer quote for this test.', 'stars' => 5])
            ->call('save')->assertHasNoFormErrors();

        $this->assertTrue($testimonial->fresh()->featured);
        $this->get('/')->assertOk()->assertSee('Updated customer quote for this test.')
            ->assertSee('Customer Name')->assertSee('Owner, Customer Company')->assertSee('5 out of 5 stars');

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->id])
            ->fillForm(['featured' => false])->call('save')->assertHasNoFormErrors();

        $this->get('/')->assertOk()->assertDontSee('Updated customer quote for this test.')->assertDontSee('Example preview');
    }

    public function test_admin_ordering_controls_published_quotes_and_drafts_remain_hidden(): void
    {
        $this->actingAs(User::factory()->create());
        $first = Testimonial::factory()->create(['name' => 'First visible customer', 'featured' => true, 'sort_order' => 1]);
        $second = Testimonial::factory()->create(['name' => 'Second visible customer', 'featured' => true, 'sort_order' => 2]);
        $draft = Testimonial::factory()->create(['name' => 'Private draft customer', 'featured' => false]);

        Livewire::test(ListTestimonials::class)->call('reorderTable', [$second->id, $draft->id, $first->id]);

        $this->get('/')->assertOk()->assertSeeInOrder(['Second visible customer', 'First visible customer'])
            ->assertDontSee('Private draft customer');
        $this->assertSame([$second->id, $first->id], Testimonial::where('featured', true)->orderBy('sort_order')->pluck('id')->all());
    }

    public static function invalidFields(): array
    {
        return [
            'rating below one' => ['stars', 0],
            'rating above five' => ['stars', 6],
            'negative ordering' => ['sort_order', -1],
            'avatar markup' => ['avatar_initial', '<>'],
        ];
    }

    #[DataProvider('invalidFields')]
    public function test_admin_rejects_invalid_testimonial_fields(string $field, mixed $value): void
    {
        $this->actingAs(User::factory()->create());
        $testimonial = Testimonial::factory()->create();
        $original = $testimonial->getAttribute($field);

        Livewire::test(EditTestimonial::class, ['record' => $testimonial->id])->fillForm([$field => $value])
            ->call('save')->assertHasFormErrors([$field]);

        $this->assertSame($original, $testimonial->fresh()->getAttribute($field));
    }

    public function test_public_customer_content_is_escaped_and_guests_cannot_edit_testimonials(): void
    {
        $testimonial = Testimonial::factory()->create([
            'name' => '<script>alert("name")</script>', 'text' => '<script>alert("quote")</script>',
            'featured' => true, 'avatar_initial' => 'CN',
        ]);

        $this->get('/')->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("quote")</script>', false)->assertDontSee('<script>alert("name")</script>', false);
        $this->get('/admin/testimonials/'.$testimonial->id.'/edit')->assertRedirect('/admin/login');
    }
}
