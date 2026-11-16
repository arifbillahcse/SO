<?php

namespace Tests\Feature;

use App\Filament\Resources\PortfolioItemResource\Pages\CreatePortfolioItem;
use App\Filament\Resources\PortfolioItemResource\Pages\EditPortfolioItem;
use App\Filament\Resources\PortfolioItemResource\Pages\ListPortfolioItems;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TechnicalPortfolioAdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function project(array $attributes = []): PortfolioItem
    {
        return PortfolioItem::create(array_merge([
            'title' => 'Cloud demonstration',
            'client' => 'Existing client',
            'category' => 'cloud',
            'tags' => ['Linux'],
        ], $attributes));
    }

    public function test_admin_can_create_an_attributed_demonstration_without_a_client(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePortfolioItem::class)->fillForm([
            'title' => 'Cloud deployment demonstration',
            'slug' => 'cloud-deployment-demonstration',
            'project_type' => 'technical_demonstration',
            'client' => null,
            'attribution' => 'Md Arif Billah',
            'category' => 'web-development',
            'tags' => 'HTML, CyberPanel',
            'video_url' => 'https://www.youtube.com/watch?v=7qDroBeHYn8',
            'repository_url' => 'https://github.com/example/project',
            'evidence_status' => 'available_video_title_matches_topic',
            'evidence_notes' => 'Metadata reviewed; full video not reviewed.',
            'evidence_checked_at' => '2026-11-16 11:00:00',
        ])->call('create')->assertHasNoFormErrors();

        $project = PortfolioItem::where('slug', 'cloud-deployment-demonstration')->firstOrFail();
        $this->assertNull($project->client);
        $this->assertSame('technical_demonstration', $project->project_type);
        $this->assertSame('Md Arif Billah', $project->attribution);
        $this->assertSame('web-development', $project->category);
        $this->assertSame(['HTML', 'CyberPanel'], $project->tags);
        $this->assertSame('https://www.youtube.com/watch?v=7qDroBeHYn8', $project->publicVideoUrl());
        $this->assertSame('https://github.com/example/project', $project->repository_url);
        $this->assertNull($project->publicRepositoryUrl());
        $this->assertSame('Metadata reviewed; full video not reviewed.', $project->evidence_notes);
        $this->assertSame('2026-11-16 11:00:00', $project->evidence_checked_at->format('Y-m-d H:i:s'));
        $this->assertModelExists($project->seo);
        $this->assertSame('CreativeWork', $project->seo->schema_type);
        $this->assertSame(1, $project->seo()->count());
        $this->get(route('our-works'))->assertOk()->assertSee('data-filter="web-development"', false);
    }

    public static function requiredIdentityFields(): array
    {
        return [
            'demonstration author' => ['technical_demonstration', 'attribution'],
            'website client' => ['website_project', 'client'],
        ];
    }

    #[DataProvider('requiredIdentityFields')]
    public function test_admin_requires_the_identity_appropriate_to_the_project_type(string $type, string $field): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm(['project_type' => $type, 'attribution' => null, 'client' => null])
            ->call('save')->assertHasFormErrors([$field => 'required']);

        $this->assertSame('website_project', $project->fresh()->project_type);
        $this->assertSame('Existing client', $project->fresh()->client);
    }

    public static function invalidEvidenceFields(): array
    {
        return [
            'script video' => ['video_url', 'javascript:alert(1)'],
            'data repository' => ['repository_url', 'data:text/html,unsafe'],
            'ftp video' => ['video_url', 'ftp://example.com/video'],
            'ftp repository' => ['repository_url', 'ftp://example.com/repository'],
            'unknown type' => ['project_type', 'unknown'],
            'unknown review status' => ['evidence_status', 'verified_customer'],
        ];
    }

    #[DataProvider('invalidEvidenceFields')]
    public function test_admin_rejects_invalid_evidence_fields(string $field, string $value): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();
        $original = $project->fresh()->getAttribute($field);

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm([$field => $value])->call('save')->assertHasFormErrors([$field]);

        $this->assertSame($original, $project->fresh()->getAttribute($field));
    }

    public static function reviewedEvidenceWithoutLinks(): array
    {
        return [
            'matching video' => ['available_video_title_matches_topic', 'video_url'],
            'partial video' => ['available_video_partially_matches_topic', 'video_url'],
            'reviewed repository' => ['repository_available', 'repository_url'],
        ];
    }

    #[DataProvider('reviewedEvidenceWithoutLinks')]
    public function test_admin_cannot_mark_missing_evidence_as_available(string $status, string $field): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project();

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])
            ->fillForm(['evidence_status' => $status])->call('save')->assertHasFormErrors([$field => 'required']);

        $this->assertSame('not_reviewed', $project->fresh()->evidence_status);
    }

    public function test_admin_can_clear_optional_evidence_but_cannot_change_the_import_reference(): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->project([
            'source_key' => 'arif-cloud-portfolio-001',
            'video_url' => 'https://example.com/video',
            'repository_url' => 'https://github.com/example/project',
            'evidence_notes' => 'Previous review',
            'evidence_status' => 'available_video_title_matches_topic',
        ]);

        Livewire::test(EditPortfolioItem::class, ['record' => $project->getRouteKey()])->fillForm([
            'source_key' => 'changed-reference', 'video_url' => null, 'repository_url' => null,
            'evidence_notes' => null, 'evidence_checked_at' => null, 'evidence_status' => 'not_reviewed',
        ])->call('save')->assertHasNoFormErrors();

        $project->refresh();
        $this->assertSame('arif-cloud-portfolio-001', $project->source_key);
        $this->assertNull($project->video_url);
        $this->assertNull($project->repository_url);
        $this->assertNull($project->evidence_notes);
        $this->assertNull($project->evidence_checked_at);
        $this->assertNull($project->publicVideoUrl());
    }

    public function test_admin_can_filter_demonstrations_by_type_and_evidence(): void
    {
        $this->actingAs(User::factory()->create());
        $website = $this->project(['title' => 'Website']);
        $demonstration = $this->project([
            'title' => 'Technical topic', 'client' => null, 'attribution' => 'Md Arif Billah',
            'project_type' => 'technical_demonstration', 'evidence_status' => 'linked_video_unavailable',
        ]);

        Livewire::test(ListPortfolioItems::class)
            ->filterTable('project_type', 'technical_demonstration')
            ->assertCanSeeTableRecords([$demonstration])->assertCanNotSeeTableRecords([$website])
            ->filterTable('evidence_status', 'not_reviewed')->assertCanNotSeeTableRecords([$demonstration]);
    }

    public function test_import_identity_allows_shared_videos_but_rejects_duplicate_source_references(): void
    {
        $this->project(['source_key' => 'arif-cloud-portfolio-001', 'video_url' => 'https://example.com/shared-video']);
        $this->project(['source_key' => 'arif-cloud-portfolio-032', 'video_url' => 'https://example.com/shared-video']);

        $this->assertDatabaseCount('portfolio_items', 2);
        $this->expectException(QueryException::class);

        $this->project(['source_key' => 'arif-cloud-portfolio-001']);
    }

    public static function evidenceVisibility(): array
    {
        return [
            'unreviewed' => ['not_reviewed', false, false],
            'matching video' => ['available_video_title_matches_topic', true, false],
            'partial match' => ['available_video_partially_matches_topic', false, false],
            'unavailable' => ['linked_video_unavailable', false, false],
            'repository needed' => ['specific_repository_needed', false, false],
            'repository reviewed' => ['repository_available', false, true],
        ];
    }

    #[DataProvider('evidenceVisibility')]
    public function test_public_evidence_links_respect_review_status(string $status, bool $videoVisible, bool $repositoryVisible): void
    {
        $project = new PortfolioItem([
            'evidence_status' => $status, 'video_url' => 'https://example.com/video',
            'repository_url' => 'https://github.com/example/project',
        ]);

        $this->assertSame($videoVisible ? 'https://example.com/video' : null, $project->publicVideoUrl());
        $this->assertSame($repositoryVisible ? 'https://github.com/example/project' : null, $project->publicRepositoryUrl());
    }

    public function test_unsafe_stored_evidence_urls_are_never_eligible_for_public_buttons(): void
    {
        $video = new PortfolioItem(['evidence_status' => 'available_video_title_matches_topic', 'video_url' => 'javascript:alert(1)']);
        $repository = new PortfolioItem(['evidence_status' => 'repository_available', 'repository_url' => 'data:text/html,unsafe']);

        $this->assertNull($video->publicVideoUrl());
        $this->assertNull($repository->publicRepositoryUrl());
    }

    public function test_demonstration_seo_credits_the_author_without_claiming_a_client_delivery(): void
    {
        $project = $this->project([
            'client' => null, 'attribution' => 'Md Arif Billah', 'project_type' => 'technical_demonstration',
        ]);

        $this->get(route('our-works.show', $project))->assertOk()
            ->assertSee('a technical demonstration by Md Arif Billah.')
            ->assertDontSee('a project delivered by Softorio for');
    }

    public function test_technical_project_admin_requires_authentication(): void
    {
        $this->get('/admin/portfolio-items/create')->assertRedirect('/admin/login');
    }
}
