<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleCategoryResource\Pages\CreateArticleCategory;
use App\Filament\Resources\ArticleCategoryResource\Pages\EditArticleCategory;
use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Database\Seeders\ServiceArticlesSeeder;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleCategoriesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_a_category_assign_an_article_and_rename_its_public_label(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(CreateArticleCategory::class)->fillForm([
            'name' => 'Website Planning', 'slug' => 'website-planning', 'description' => 'Launch preparation.',
        ])->call('create')->assertHasNoFormErrors();
        $category = ArticleCategory::where('slug', 'website-planning')->firstOrFail();

        Livewire::test(CreateArticle::class)->fillForm([
            'title' => 'A launch guide', 'slug' => 'a-launch-guide', 'article_category_id' => $category->id,
            'excerpt' => 'Useful preparation for a website launch.', 'body' => '<p>Launch preparation.</p>',
            'read_minutes' => 3, 'published_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ])->call('create')->assertHasNoFormErrors();

        $article = Article::where('slug', 'a-launch-guide')->firstOrFail();
        $this->assertSame($category->id, $article->article_category_id);
        Livewire::test(EditArticleCategory::class, ['record' => $category->id])
            ->fillForm(['name' => 'Launch Planning'])->call('save')->assertHasNoFormErrors();
        $this->get('/our-resources?category=website-planning')->assertOk()->assertSee('Launch Planning')->assertSee('A launch guide');
        $this->get('/our-resources/a-launch-guide')->assertOk()->assertSee('Launch Planning');
        $this->actingAs(User::factory()->create());
        Livewire::test(EditArticle::class, ['record' => $article->slug])
            ->fillForm(['read_minutes' => 4])->call('save')->assertHasNoFormErrors();
        $this->assertSame(4, $article->fresh()->read_minutes);
    }

    public function test_admin_can_add_a_category_from_the_article_form(): void
    {
        $this->actingAs(User::factory()->create());

        $form = Livewire::test(CreateArticle::class)
            ->callAction(TestAction::make('createOption')->schemaComponent('article_category_id'), data: [
                'name' => 'Operations', 'slug' => 'operations', 'description' => 'Operational guides.',
            ]);

        $category = ArticleCategory::where('slug', 'operations')->firstOrFail();
        $form->assertFormSet(['article_category_id' => $category->id]);
    }

    public function test_invalid_or_duplicate_category_fields_do_not_create_records(): void
    {
        $this->actingAs(User::factory()->create());
        $category = ArticleCategory::factory()->create(['name' => 'Existing Topic', 'slug' => 'existing-topic']);
        $before = ArticleCategory::count();

        Livewire::test(CreateArticleCategory::class)->fillForm(['name' => '', 'slug' => 'Bad Slug!'])
            ->call('create')->assertHasFormErrors(['name', 'slug']);
        Livewire::test(CreateArticleCategory::class)->fillForm(['name' => $category->name, 'slug' => $category->slug])
            ->call('create')->assertHasFormErrors(['name', 'slug']);

        $this->assertDatabaseCount('article_categories', $before);
    }

    public function test_categories_in_use_are_protected_and_guests_cannot_manage_them(): void
    {
        $category = ArticleCategory::factory()->create();
        Article::factory()->for($category, 'articleCategory')->create();
        $this->get('/admin/article-categories')->assertRedirect('/admin/login');
        $this->get('/admin/article-categories/'.$category->id.'/edit')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        Livewire::test(EditArticleCategory::class, ['record' => $category->id])->assertActionHidden(DeleteAction::class);
        $this->assertModelExists($category);
        $unused = ArticleCategory::factory()->create();
        Livewire::test(EditArticleCategory::class, ['record' => $unused->id])->callAction(DeleteAction::class);
        $this->assertModelMissing($unused);
    }

    public function test_public_filters_include_all_featured_articles_and_hide_drafts_from_the_collection_and_related_items(): void
    {
        $first = ArticleCategory::factory()->create(['name' => 'First Topic', 'slug' => 'first-topic']);
        $second = ArticleCategory::factory()->create(['name' => 'Second Topic', 'slug' => 'second-topic']);
        $one = Article::factory()->for($first, 'articleCategory')->create(['title' => 'Published featured one', 'featured' => true]);
        Article::factory()->for($first, 'articleCategory')->create(['title' => 'Published featured two', 'featured' => true]);
        Article::factory()->for($second, 'articleCategory')->create(['title' => 'Different category article']);
        Article::factory()->create(['title' => 'Future private article', 'published_at' => now()->addDay()]);
        Article::factory()->create(['title' => 'Undated private article', 'published_at' => null]);

        $this->get('/our-resources')->assertOk()->assertSee('Published featured one')->assertSee('Published featured two')
            ->assertSee('Different category article')->assertDontSee('Future private article')->assertDontSee('Undated private article');
        $this->get('/our-resources?category=first-topic')->assertOk()->assertSee('Published featured one')->assertSee('Published featured two')
            ->assertDontSee('Different category article');
        $this->get('/our-resources?category=missing-topic')->assertOk()->assertSee('No published articles')->assertDontSee('Published featured one');
        $this->get(route('our-resources.show', $one))->assertOk()->assertDontSee('Future private article')->assertDontSee('Undated private article');
        $this->get('/our-resources?category[]=bad')->assertRedirect();
    }

    public function test_three_full_articles_have_seo_and_repeat_import_preserves_admin_edits(): void
    {
        $this->seed(ServiceArticlesSeeder::class);
        $this->assertDatabaseCount('articles', 3);
        foreach (Article::with('seo')->get() as $article) {
            $this->assertGreaterThan(500, $article->seoWordCount());
            $this->assertNotNull($article->articleCategory);
            $this->assertSame([], $article->seo->missingFields());
            $this->assertTrue($article->seo->isIndexable());
            $this->get(route('our-resources.show', $article))->assertOk()->assertSee('Official references')->assertSee('related service');
            $this->get('/sitemap.xml')->assertOk()->assertSee($article->seoPath(), false);
        }
        $article = Article::firstOrFail();
        $article->update(['title' => 'Administrator title', 'slug' => 'administrator-title', 'body' => '<p>Edited content.</p>', 'published_at' => null]);
        $article->seo->update(['title' => 'Administrator SEO']);
        $article->articleCategory->update(['name' => 'Renamed category']);
        $before = Article::with('seo')->orderBy('id')->get()->toArray();

        $this->seed(ServiceArticlesSeeder::class);

        $this->assertSame($before, Article::with('seo')->orderBy('id')->get()->toArray());
        $this->assertDatabaseCount('articles', 3);
    }

    public function test_migration_preserves_legacy_category_names_and_article_data(): void
    {
        $migration = require database_path('migrations/2026_11_16_154412_create_article_categories_table.php');
        $migration->down();
        $id = DB::table('articles')->insertGetId([
            'title' => 'Legacy guide', 'slug' => 'legacy-guide', 'category' => 'Custom Legacy Topic',
            'excerpt' => 'Existing excerpt.', 'body' => '<p>Existing content.</p>', 'published_at' => now(),
        ]);

        $migration->up();

        $article = Article::findOrFail($id);
        $this->assertSame('Custom Legacy Topic', $article->category_label);
        $this->assertSame('<p>Existing content.</p>', $article->body);
        $this->get('/our-resources?category=custom-legacy-topic')->assertOk()->assertSee('Legacy guide');
    }
}
