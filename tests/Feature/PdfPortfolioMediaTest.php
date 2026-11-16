<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\PdfPortfolioMediaSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfPortfolioMediaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_media_import_attaches_available_images_and_preserves_admin_thumbnails_on_rerun(): void
    {
        Storage::fake('public');
        $this->seed(PdfPortfolioSeeder::class);
        Storage::disk('public')->put('portfolio/screenshots/upcmartbd-com.jpg', 'image');
        $project = PortfolioItem::where('slug', 'upcmartbd-com')->firstOrFail();

        $this->seed(PdfPortfolioMediaSeeder::class);

        $this->assertSame('portfolio/screenshots/upcmartbd-com.jpg', $project->fresh()->thumbnail_path);
        $this->assertSame(1, PortfolioItem::whereNotNull('thumbnail_path')->count());
        $this->assertDatabaseCount('portfolio_items', 71);

        $project->update(['thumbnail_path' => 'portfolio/admin-upload.jpg']);
        $this->seed(PdfPortfolioMediaSeeder::class);

        $this->assertSame('portfolio/admin-upload.jpg', $project->fresh()->thumbnail_path);
    }

    public function test_media_import_does_not_create_projects_in_an_empty_database(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portfolio/screenshots/upcmartbd-com.jpg', 'image');

        $this->seed(PdfPortfolioMediaSeeder::class);

        $this->assertDatabaseEmpty('portfolio_items');
    }

    public function test_media_import_matches_an_existing_website_after_an_admin_changes_its_slug(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portfolio/screenshots/upcmartbd-com.jpg', 'image');
        $project = PortfolioItem::create([
            'title' => 'UPC Mart',
            'slug' => 'custom-store-slug',
            'client' => 'UPC Mart',
            'category' => 'ecommerce',
            'tags' => ['eCommerce'],
            'website_url' => 'https://www.upcmartbd.com/',
        ]);

        $this->seed(PdfPortfolioMediaSeeder::class);

        $this->assertSame('portfolio/screenshots/upcmartbd-com.jpg', $project->fresh()->thumbnail_path);
        $this->assertDatabaseCount('portfolio_items', 1);
    }
}
