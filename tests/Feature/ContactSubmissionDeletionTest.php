<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactSubmissionResource\Pages\EditContactSubmission;
use App\Filament\Resources\ContactSubmissionResource\Pages\ListContactSubmissions;
use App\Models\User;
use Database\Factories\ContactSubmissionFactory;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactSubmissionDeletionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_deletes_one_enquiry_from_the_list_after_confirmation(): void
    {
        $enquiry = ContactSubmissionFactory::new()->create();
        $retained = ContactSubmissionFactory::new()->create();
        $this->actingAs(User::factory()->create());

        $page = Livewire::test(ListContactSubmissions::class)
            ->mountAction(TestAction::make('delete')->table($enquiry))
            ->assertActionMounted(TestAction::make('delete')->table($enquiry));
        $this->assertModelExists($enquiry);
        $page->unmountAction();
        $this->assertModelExists($enquiry);

        $page->callAction(TestAction::make('delete')->table($enquiry))
            ->assertNotified()->assertCanNotSeeTableRecords([$enquiry]);

        $this->assertModelMissing($enquiry);
        $this->assertModelExists($retained);
    }

    public function test_bulk_deletion_removes_only_selected_enquiries_and_can_be_cancelled(): void
    {
        $selected = ContactSubmissionFactory::new()->count(2)->create();
        $retained = ContactSubmissionFactory::new()->create();
        $this->actingAs(User::factory()->create());

        $action = TestAction::make('delete')->table()->bulk();
        $page = Livewire::test(ListContactSubmissions::class)
            ->selectTableRecords($selected)
            ->mountAction($action)->assertActionMounted($action);
        $this->assertDatabaseCount('contact_submissions', 3);
        $page->unmountAction();
        $this->assertDatabaseCount('contact_submissions', 3);

        $page->callAction($action)->assertNotified()->assertCanNotSeeTableRecords($selected);

        foreach ($selected as $enquiry) {
            $this->assertModelMissing($enquiry);
        }
        $this->assertModelExists($retained);
        $this->assertDatabaseCount('contact_submissions', 1);
    }

    public function test_admin_can_also_delete_from_the_enquiry_details(): void
    {
        $enquiry = ContactSubmissionFactory::new()->create();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditContactSubmission::class, ['record' => $enquiry->id])
            ->callAction('delete')->assertNotified()->assertRedirect('/admin/contact-submissions');

        $this->assertModelMissing($enquiry);
    }

    public function test_guests_cannot_access_the_enquiry_management_pages(): void
    {
        $enquiry = ContactSubmissionFactory::new()->create();

        $this->get('/admin/contact-submissions')->assertRedirect('/admin/login');
        $this->get('/admin/contact-submissions/'.$enquiry->id.'/edit')->assertRedirect('/admin/login');

        $this->assertModelExists($enquiry);
    }
}
