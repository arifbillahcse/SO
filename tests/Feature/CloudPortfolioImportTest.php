<?php

namespace Tests\Feature;

use App\Filament\Resources\PortfolioItemResource\Pages\EditPortfolioItem;
use App\Models\PortfolioItem;
use App\Models\User;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CloudPortfolioImportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cloud_import_adds_all_topics_to_the_existing_portfolio_with_accurate_evidence_and_counts(): void
    {
        $this->seed(PdfPortfolioSeeder::class);
        $existing = PortfolioItem::orderBy('id')->get()->toArray();

        $this->seed(CloudPortfolioSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 118);
        $this->assertSame($existing, PortfolioItem::whereNull('source_key')->orderBy('id')->get()->toArray());
        $this->assertSame(118, PortfolioItem::has('seo')->count());
        $this->assertSame(47, PortfolioItem::where('project_type', 'technical_demonstration')->count());
        $this->assertSame(47, PortfolioItem::whereNotNull('source_key')->whereNull('client')->where('attribution', 'Md Arif Billah')->count());
        $this->assertSame(39, PortfolioItem::where('evidence_status', 'linked_video_unavailable')->count());
        $this->assertSame(5, PortfolioItem::where('evidence_status', 'available_video_title_matches_topic')->count());
        $this->assertSame(1, PortfolioItem::where('evidence_status', 'available_video_partially_matches_topic')->count());
        $this->assertSame(2, PortfolioItem::where('evidence_status', 'specific_repository_needed')->whereNull('repository_url')->count());
        $this->assertSame(2, PortfolioItem::where('category', 'web-development')->count());

        $this->get(route('our-works'))->assertOk()
            ->assertSee('Showing 118 of 118 projects')
            ->assertSee('58</span><span class="trust-label">Website Projects', false)
            ->assertSee('47</span><span class="trust-label">Technical Topics', false)
            ->assertSee('Technical portfolio · Md Arif Billah')
            ->assertSee('data-filter="web-development"', false);

        $first = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();
        $database = PortfolioItem::where('source_key', 'arif-cloud-portfolio-032')->firstOrFail();
        $this->assertNotSame($first->id, $database->id);
        $this->assertSame($first->video_url, $database->video_url);
    }

    public function test_all_imported_detail_pages_credit_the_author_and_only_link_to_matching_available_videos(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $projects = PortfolioItem::whereNotNull('source_key')->get();

        foreach ($projects as $project) {
            $response = $this->get(route('our-works.show', $project));
            $response->assertOk()->assertSeeText($project->title)
                ->assertSee('Technical portfolio by Md Arif Billah')
                ->assertSee('"creator":{"@type":"Person","name":"Md Arif Billah"}', false)
                ->assertDontSee('Visit Website')
                ->assertDontSee('Original PDF reference:')
                ->assertDontSee('zero downtime')
                ->assertDontSee('maximum email deliverability');

            if ($project->evidence_status === 'available_video_title_matches_topic') {
                $response->assertSee('Watch demonstration')->assertSee('href="'.$project->video_url.'"', false)
                    ->assertSee('rel="noopener noreferrer"', false);
            } else {
                $response->assertDontSee('Watch demonstration');
            }
        }

        $partial = PortfolioItem::where('source_key', 'arif-cloud-portfolio-005')->firstOrFail();
        $this->get(route('our-works.show', $partial))->assertOk()->assertSee('does not establish a separate full email-server deployment');
    }

    public function test_rerunning_the_import_preserves_admin_changes_and_source_identity(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-043')->firstOrFail();
        $project->update([
            'title' => 'Edited cloud topic', 'slug' => 'custom-cloud-topic',
            'description' => 'Description edited by the administrator.',
            'evidence_status' => 'not_reviewed', 'thumbnail_path' => 'portfolio/admin-image.jpg',
        ]);
        $before = $project->fresh()->toArray();

        $this->seed(CloudPortfolioSeeder::class);

        $this->assertDatabaseCount('portfolio_items', 47);
        $this->assertSame($before, $project->fresh()->toArray());
        $this->assertSame(47, PortfolioItem::has('seo')->count());
    }

    public function test_a_slug_collision_does_not_overwrite_or_skip_an_unrelated_existing_project(): void
    {
        $existing = PortfolioItem::create([
            'title' => 'Existing project',
            'slug' => 'arif-cloud-deployed-a-full-wordpress-site-on-aws-ec2-ubuntu-lamp',
            'client' => 'Existing client', 'category' => 'wordpress', 'tags' => ['WordPress'],
        ]);
        $before = $existing->fresh()->toArray();

        $this->seed(CloudPortfolioSeeder::class);

        $imported = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();
        $this->assertDatabaseCount('portfolio_items', 48);
        $this->assertSame($before, $existing->fresh()->toArray());
        $this->assertNotSame($existing->slug, $imported->slug);
        $this->get(route('our-works.show', $imported))->assertOk()->assertSee('WordPress Deployment on AWS EC2');
    }

    public static function unsafeEvidence(): array
    {
        return [
            'video' => ['video_url', 'available_video_title_matches_topic'],
            'repository' => ['repository_url', 'repository_available'],
        ];
    }

    #[DataProvider('unsafeEvidence')]
    public function test_public_pages_do_not_render_unsafe_stored_evidence_links(string $field, string $status): void
    {
        $project = PortfolioItem::create([
            'title' => 'Technical overview', 'client' => null, 'category' => 'cloud', 'tags' => ['Linux'],
            'project_type' => 'technical_demonstration', 'attribution' => 'Md Arif Billah',
            'evidence_status' => $status, $field => 'javascript:alert(1)',
        ]);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertDontSee('href="javascript:', false)
            ->assertDontSee('Watch demonstration')
            ->assertDontSee('View repository');
    }

    public function test_reviewed_repository_link_is_rendered_separately_from_the_website_link(): void
    {
        $project = PortfolioItem::create([
            'title' => 'Deployment code', 'client' => null, 'category' => 'automation', 'tags' => ['Git'],
            'project_type' => 'technical_demonstration', 'attribution' => 'Md Arif Billah',
            'evidence_status' => 'repository_available', 'repository_url' => 'https://github.com/example/deployment',
        ]);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('View repository')
            ->assertSee('href="https://github.com/example/deployment"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('Visit Website');
    }

    public function test_admin_can_edit_an_imported_topic_while_preserving_identity_and_private_review_notes(): void
    {
        $this->seed(CloudPortfolioSeeder::class);
        $this->actingAs(User::factory()->create());
        $project = PortfolioItem::where('source_key', 'arif-cloud-portfolio-001')->firstOrFail();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm([
                'description' => 'An updated technical overview of WordPress hosting on AWS EC2.',
                'evidence_notes' => 'Private audit note from the portfolio administrator.',
            ])->call('save')->assertHasNoFormErrors();

        $project->refresh();
        $this->assertSame('arif-cloud-portfolio-001', $project->source_key);
        $this->assertSame('Md Arif Billah', $project->attribution);
        $this->assertSame('An updated technical overview of WordPress hosting on AWS EC2.', $project->description);
        $this->assertSame('Private audit note from the portfolio administrator.', $project->evidence_notes);
        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('An updated technical overview of WordPress hosting on AWS EC2.')
            ->assertDontSee('Private audit note from the portfolio administrator.');
    }
}
