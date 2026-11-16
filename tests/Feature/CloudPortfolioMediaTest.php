<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\CloudPortfolioMediaSeeder;
use Database\Seeders\CloudPortfolioSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudPortfolioMediaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_media_import_attaches_all_cloud_images_and_reruns_preserve_admin_overrides(): void
    {
        Storage::fake('public');
        $this->seed(CloudPortfolioSeeder::class);
        $manifest = json_decode(file_get_contents(database_path('seeders/data/cloud-portfolio-media.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest['projects'] as $media) {
            Storage::disk('public')->put($media['thumbnail_path'], 'image');
        }
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();
        $project->update(['slug' => 'admin-cloud-slug']);

        $this->seed(CloudPortfolioMediaSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 47);
        $this->assertSame(42, PortfolioItem::where('thumbnail_path', 'like', 'portfolio/cloud-covers/%')->count());
        $this->assertSame(5, PortfolioItem::where('thumbnail_path', 'like', 'portfolio/cloud-videos/%')->count());
        $this->assertSame('portfolio/cloud-covers/arif-cloud-portfolio-001.jpg', $project->fresh()->thumbnail_path);

        $project->update(['thumbnail_path' => 'portfolio/admin-upload.jpg']);
        $this->seed(CloudPortfolioMediaSeeder::class);

        $this->assertSame('portfolio/admin-upload.jpg', $project->fresh()->thumbnail_path);
        $this->assertSame(47, PortfolioItem::whereNotNull('thumbnail_path')->count());
    }

    public function test_missing_files_are_skipped_and_the_media_import_does_not_create_projects(): void
    {
        Storage::fake('public');
        $this->seed(CloudPortfolioMediaSeeder::class);
        $this->assertDatabaseEmpty('portfolio_items');
        $this->seed(CloudPortfolioSeeder::class);

        $this->seed(CloudPortfolioMediaSeeder::class);

        $this->assertSame(0, PortfolioItem::whereNotNull('thumbnail_path')->count());
    }

    public function test_technical_and_video_collections_render_with_clear_media_and_evidence_labels(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $cover = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();
        $cover->update(['thumbnail_path' => 'portfolio/cloud-covers/arif-cloud-portfolio-001.jpg']);
        $video = PortfolioItem::where('source_key', 'arif-cloud-portfolio-043')->firstOrFail();
        $video->update(['thumbnail_path' => 'portfolio/cloud-videos/arif-cloud-portfolio-043.jpg']);

        $this->get(route('our-works'))->assertOk()
            ->assertSee('data-collection="technical"', false)
            ->assertSee('data-collection="video"', false)
            ->assertSee('data-project-type="technical_demonstration"', false)
            ->assertSee('Video demonstration')
            ->assertSee('Technical overview')
            ->assertSee('wp-thumbnail--video')
            ->assertSee('No projects match this combination.');

        $this->get(route('our-works.show', $cover))->assertOk()
            ->assertSee('Illustrative technical cover')
            ->assertSee('The source video was unavailable at the last review.')
            ->assertDontSee('Watch demonstration');

        $this->get(route('our-works.show', $video))->assertOk()
            ->assertSee('Video thumbnail from the linked demonstration')
            ->assertSee('Watch demonstration')
            ->assertSee('portfolio-preview--video');
    }

    public function test_technical_details_recommend_technical_topics_instead_of_website_projects(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        PortfolioItem::create([
            'title' => 'Unrelated legacy website',
            'client' => 'Legacy client',
            'category' => 'wordpress',
            'tags' => ['WordPress'],
            'sort_order' => 0,
        ]);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-047')->firstOrFail();

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('Unrelated legacy website')
            ->assertSee('WordPress Deployment on AWS EC2');
    }
}
