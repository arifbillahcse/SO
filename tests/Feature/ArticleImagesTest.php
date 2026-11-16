<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleImagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_an_article_with_a_featured_image_and_it_appears_in_cards_detail_and_seo(): void
    {
        Storage::fake('public');
        $category = ArticleCategory::factory()->create();
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateArticle::class)->fillForm([
            'title' => 'An illustrated guide', 'slug' => 'an-illustrated-guide', 'article_category_id' => $category->id,
            'excerpt' => 'A guide with an image.', 'body' => '<h2>Useful advice</h2><p>Guide content.</p>',
            'read_minutes' => 4, 'published_at' => now()->subDay()->format('Y-m-d H:i:s'), 'featured' => true,
            'featured_image_path' => UploadedFile::fake()->image('cover.jpg', 1200, 675),
            'featured_image_alt' => 'An illustrated website launch plan',
            'featured_image_caption' => 'Plan your launch before publishing.',
        ])->call('create')->assertHasNoFormErrors();

        $article = Article::where('slug', 'an-illustrated-guide')->firstOrFail();
        Storage::disk('public')->assertExists($article->featured_image_path);
        $this->assertStringStartsWith('articles/featured/', $article->featured_image_path);
        $this->get('/our-resources')->assertOk()->assertSee($article->featuredImageUrl(), false)
            ->assertSee('An illustrated website launch plan');
        $this->get('/our-resources/an-illustrated-guide')->assertOk()->assertSee($article->featuredImageUrl(), false)
            ->assertSee('An illustrated website launch plan')->assertSee('Plan your launch before publishing.')
            ->assertSee('/storage/'.$article->featured_image_path, false);

        $article->update(['featured' => false]);
        $this->get('/our-resources')->assertOk()->assertSee('class="resource-card-image"', false);
        $article->seo->update(['og_image' => 'seo/custom-share.jpg']);
        $this->get('/our-resources/an-illustrated-guide')->assertOk()->assertSee('/storage/seo/custom-share.jpg', false);
    }

    public function test_admin_can_replace_and_remove_a_featured_image_without_removing_article_content(): void
    {
        Storage::fake('public');
        $article = Article::factory()->create([
            'featured_image_path' => 'articles/featured/old.jpg',
            'body' => '<p>Existing article content.</p>',
        ]);
        Storage::disk('public')->put($article->featured_image_path, 'old image');
        $body = $article->body;
        $this->actingAs(User::factory()->create());

        Livewire::test(EditArticle::class, ['record' => $article->slug])->fillForm(['featured_image_path' => []])->fillForm([
            'featured_image_path' => UploadedFile::fake()->image('replacement.png'),
        ])->call('save')->assertHasNoFormErrors();

        $path = $article->fresh()->featured_image_path;
        $this->assertNotSame('articles/featured/old.jpg', $path);
        Storage::disk('public')->assertExists($path);
        $this->assertSame($article->title, $article->fresh()->featuredImageAltText());
        Livewire::test(EditArticle::class, ['record' => $article->slug])
            ->fillForm(['featured_image_path' => []])->call('save')->assertHasNoFormErrors();

        $this->assertNull($article->fresh()->featured_image_path);
        $this->assertSame($body, $article->fresh()->body);
        $this->get(route('our-resources.show', $article))->assertOk()->assertDontSee('class="article-cover"', false);
    }

    public function test_featured_upload_rejects_non_images_and_files_above_five_megabytes(): void
    {
        Storage::fake('public');
        $article = Article::factory()->create();
        $this->actingAs(User::factory()->create());

        foreach ([
            UploadedFile::fake()->create('script.php', 1, 'application/x-php'),
            UploadedFile::fake()->image('large.jpg')->size(5121),
        ] as $file) {
            Livewire::test(EditArticle::class, ['record' => $article->slug])
                ->fillForm(['featured_image_path' => $file])->call('save')->assertHasFormErrors(['featured_image_path']);
            $this->assertNull($article->fresh()->featured_image_path);
        }
    }

    public function test_admin_can_upload_an_image_with_the_body_toolbar_and_save_it_in_public_storage(): void
    {
        Storage::fake('public');
        $article = Article::factory()->create();
        $this->actingAs(User::factory()->create());

        $form = Livewire::test(EditArticle::class, ['record' => $article->slug])
            ->callAction(TestAction::make('attachFiles')->schemaComponent('body'), data: [
                'file' => UploadedFile::fake()->image('inline-image.png'),
                'alt' => 'A workflow diagram',
            ], arguments: ['editorSelection' => ['type' => 'text', 'anchor' => 1, 'head' => 1]]);
        $files = $form->get('componentFileAttachments');
        $id = array_key_first($files['data']['body']);
        $form->fillForm(['body' => '<p>Body content.</p><img data-id="'.$id.'" src="blob:test-image" alt="A workflow diagram">'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertCount(1, Storage::disk('public')->allFiles('articles/body'));
        $path = Storage::disk('public')->allFiles('articles/body')[0];
        $this->assertStringContainsString($path, $article->fresh()->body);
        $this->get(route('our-resources.show', $article))->assertOk()
            ->assertSee('/storage/'.$path, false)->assertSee('A workflow diagram')->assertDontSee('blob:test-image', false);
    }

    public function test_body_renderer_resolves_uploaded_images_on_the_current_disk_and_removes_unsafe_markup(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('articles/body/example.png', 'image');
        $article = Article::factory()->create([
            'featured_image_path' => 'articles/featured/example.jpg',
            'featured_image_alt' => 'Image alt <script>alert("alt")</script>',
            'featured_image_caption' => '<script>alert("caption")</script>',
            'body' => '<h2>Image guidance</h2><p>Useful content.</p><img data-id="articles/body/example.png" src="http://old-local.test/storage/articles/body/example.png" alt="Body image" onerror="alert(1)"><script>alert("body")</script>',
        ]);

        $this->get(route('our-resources.show', $article))->assertOk()->assertSee('Body image')
            ->assertSee('/storage/articles/body/example.png', false)->assertDontSee('http://old-local.test', false)
            ->assertDontSee('onerror=', false)->assertDontSee('<script>alert("body")</script>', false)
            ->assertDontSee('<script>alert("caption")</script>', false)->assertDontSee('<script>alert("alt")</script>', false);
    }

    public function test_articles_without_images_keep_working_and_guests_cannot_open_the_upload_form(): void
    {
        $article = Article::factory()->create();

        $this->get('/our-resources')->assertOk()->assertSee($article->title)->assertDontSee('class="resource-card-image"', false);
        $this->get(route('our-resources.show', $article))->assertOk()->assertDontSee('class="article-cover"', false);
        $this->get('/admin/articles/'.$article->slug.'/edit')->assertRedirect('/admin/login');
    }
}
