<?php

namespace Tests\Feature;

use App\Filament\Pages\Media;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\OrganizationSettings;
use App\Models\PortfolioItem;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\MediaLibrary;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_library_discovers_uploads_and_groups_featured_body_and_seo_references_by_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('articles/featured', UploadedFile::fake()->image('shared.jpg', 1200, 675), 'shared.jpg');
        $article = Article::factory()->create([
            'title' => 'Shared image article', 'featured_image_path' => 'articles/featured/shared.jpg',
            'body' => '<img data-id="articles/featured/shared.jpg" src="http://old.test/storage/articles/featured/shared.jpg"><img src="/storage/articles/featured/shared.jpg">',
        ]);
        $article->seo->update(['og_image' => 'https://softorio.com/storage/articles/featured/shared.jpg']);

        $images = app(MediaLibrary::class)->images()->keyBy('key');
        $image = $images->get('uploads:articles/featured/shared.jpg');

        $this->assertNotNull($image);
        $this->assertTrue($image['exists']);
        $this->assertSame('1200 ? 675', $image['dimensions']);
        $this->assertGreaterThan(0, $image['size']);
        $this->assertCount(3, $image['uses']);
        $this->assertSame(1, $images->where('path', 'articles/featured/shared.jpg')->count());
        $this->assertFalse($images->has('external:http://old.test/storage/articles/featured/shared.jpg'));
    }

    public function test_usage_covers_portfolio_case_studies_team_organization_and_website_assets(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('shared.png', 'image');
        PortfolioItem::create(['title' => 'Media project', 'client' => 'Company', 'category' => 'cloud', 'tags' => [], 'thumbnail_path' => 'shared.png']);
        CaseStudy::create(['title' => 'Media study', 'slug' => 'media-study', 'tags' => [], 'metrics' => [], 'description' => 'A study.', 'body' => '<img src="/storage/shared.png">']);
        TeamMember::create(['name' => 'Media member', 'role' => 'Engineer', 'bio' => 'Biography.', 'photo_path' => 'shared.png']);
        OrganizationSettings::current()->update(['logo' => 'shared.png']);

        $images = app(MediaLibrary::class)->images()->keyBy('key');

        $this->assertCount(4, $images->get('uploads:shared.png')['uses']);
        $this->assertNotEmpty($images->get('assets:images/logo-icon.png')['uses']);
        $this->assertNotEmpty($images->get('assets:images/og-default.png')['uses']);
        foreach ($images->get('uploads:shared.png')['uses'] as $use) {
            $this->assertStringContainsString('/admin/', $use['url']);
        }
    }

    public function test_external_missing_and_unreferenced_images_are_distinguished_without_remote_fetches(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('unreferenced.png', 'image');
        Article::factory()->create([
            'featured_image_path' => 'articles/missing.jpg',
            'body' => '<picture><source srcset="https://cdn.example.test/photo?size=400 1x, https://cdn.example.test/photo?size=800 2x"><img src="https://cdn.example.test/photo?size=800"></picture>',
        ]);

        $images = app(MediaLibrary::class)->images()->keyBy('key');

        $this->assertFalse($images->get('uploads:articles/missing.jpg')['exists']);
        $this->assertSame([], $images->get('uploads:unreferenced.png')['uses']);
        $this->assertNull($images->get('external:https://cdn.example.test/photo?size=400')['exists']);
        $this->assertNull($images->get('external:https://cdn.example.test/photo?size=800')['size']);
    }

    public function test_admin_can_search_filter_paginate_and_refresh_the_media_library(): void
    {
        Storage::fake('public');
        for ($index = 1; $index <= 25; $index++) {
            Storage::disk('public')->put('media-test/image-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT).'.png', 'image');
        }
        Article::factory()->create(['title' => 'Named usage target', 'featured_image_path' => 'media-test/image-01.png']);
        $this->actingAs(User::factory()->create());

        $page = Livewire::test(Media::class)->set('source', 'uploads')->set('search', 'media-test');
        $page->assertSee('image-01.png')->assertDontSee('image-25.png')->assertSee('Page 1 of 2')
            ->call('nextPage')->assertSee('image-25.png')->assertSee('Page 2 of 2');
        $page->set('search', 'Named usage target')->assertSee('image-01.png')->assertDontSee('image-25.png');
        $page->set('search', 'media-test')->set('usage', 'referenced')->assertSee('image-01.png')->assertDontSee('image-02.png');
        $page->set('usage', 'unreferenced')->assertDontSee('image-01.png')->assertSee('image-02.png');
        $page->set('usage', 'missing')->assertSee('No images match these filters');
        Storage::disk('public')->delete('media-test/image-01.png');
        $page->call('$refresh')->assertSee('image-01.png')->assertSee('Image file not found');
    }

    public function test_guests_cannot_access_media_and_names_are_escaped_while_private_files_are_excluded(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('private-image.png', 'private');
        Storage::disk('public')->put('livewire-tmp/temporary-image.png', 'temporary');
        Article::factory()->create([
            'title' => '<script>alert("media")</script>',
            'body' => '<img src="javascript:alert(1)"><img src="/storage/../private-image.png"><img src="/storage/%2e%2e/private-image.png">',
            'featured_image_path' => 'missing-safe-image.png',
        ]);
        $this->get('/admin/media')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        $this->get('/admin/media')->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("media")</script>', false)->assertDontSee('private-image.png')->assertDontSee('temporary-image.png')
            ->assertDontSee('javascript:alert(1)', false);
    }
}
