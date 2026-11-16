<?php

namespace Tests\Feature;

use App\Filament\Resources\PortfolioItemResource\Pages\ListPortfolioItems;
use App\Models\PortfolioItem;
use App\Models\User;
use Database\Seeders\PdfPortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PdfPortfolioImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_all_unique_projects_with_labeled_demos_and_detail_pages(): void
    {
        $this->seed(PdfPortfolioSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 71);
        $this->assertSame(71, PortfolioItem::distinct()->count('website_url'));
        $this->assertSame(13, PortfolioItem::whereJsonContains('tags', 'Demo')->count());
        $this->assertSame(71, PortfolioItem::has('seo')->count());

        $this->get('/our-works')->assertOk()->assertSee('UPC Mart')->assertSee('Demo')
            ->assertSee('Showing 71 of 71 projects')
            ->assertSee('data-filter="demo"', false)
            ->assertDontSee('data-filter="automation"', false);
        $project = PortfolioItem::where('slug', 'upcmartbd-com')->firstOrFail();
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('href="https://www.upcmartbd.com/"', false);
    }

    public function test_reimport_does_not_duplicate_projects_or_overwrite_admin_edits(): void
    {
        $this->seed(PdfPortfolioSeeder::class);
        $project = PortfolioItem::where('slug', 'upcmartbd-com')->firstOrFail();
        $project->update(['title' => 'Edited by admin', 'description' => 'A revised project description.']);

        $this->seed(PdfPortfolioSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 71);
        $this->assertSame('Edited by admin', $project->fresh()->title);
        $this->assertSame('A revised project description.', $project->fresh()->description);
    }

    public function test_every_imported_project_has_a_working_public_detail_page(): void
    {
        $this->seed(PdfPortfolioSeeder::class);

        foreach (PortfolioItem::all() as $project) {
            $this->get(route('our-works.show', $project))->assertOk()
                ->assertSeeText($project->title)
                ->assertSee('href="'.$project->website_url.'"', false);
        }
    }

    public function test_admin_can_find_imported_projects_by_title_and_filter_by_category(): void
    {
        $this->seed(PdfPortfolioSeeder::class);
        $this->actingAs(User::factory()->create());
        $store = PortfolioItem::where('slug', 'upcmartbd-com')->firstOrFail();
        $cloudProjects = PortfolioItem::where('category', 'cloud')->get();

        Livewire::test(ListPortfolioItems::class)
            ->searchTable('UPC Mart')
            ->assertCanSeeTableRecords([$store])
            ->assertCanNotSeeTableRecords($cloudProjects)
            ->searchTable('')
            ->filterTable('category', 'cloud')
            ->assertCanSeeTableRecords($cloudProjects)
            ->assertCanNotSeeTableRecords([$store]);
    }

    public function test_import_preserves_an_existing_project_with_the_same_url_and_a_different_slug(): void
    {
        $project = PortfolioItem::create([
            'title' => 'Existing UPC Project',
            'slug' => 'existing-upc-project',
            'client' => 'UPC Mart',
            'category' => 'ecommerce',
            'tags' => ['eCommerce'],
            'website_url' => 'https://www.upcmartbd.com/',
        ]);

        $this->seed(PdfPortfolioSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 71);
        $this->assertSame('Existing UPC Project', $project->fresh()->title);
        $this->assertDatabaseMissing('portfolio_items', ['slug' => 'upcmartbd-com']);
    }
}
