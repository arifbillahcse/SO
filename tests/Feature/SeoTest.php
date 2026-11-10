<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Filament\Resources\PageSeoResource\Pages\EditPageSeo;
use App\Models\Article;
use App\Models\PageSeo;
use App\Models\PortfolioItem;
use App\Models\Redirect;
use App\Models\User;
use App\Services\Seo\SeoAuditor;
use Database\Seeders\DatabaseSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeded inside the test transaction so rows don't leak into other test classes.
        $this->seed(DatabaseSeeder::class);
    }

    private function dom(string $path): DOMXPath
    {
        $html = $this->get($path)->assertOk()->getContent();
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return new DOMXPath($dom);
    }

    private function staticPaths(): array
    {
        return array_map(fn ($name) => route($name, [], false), array_keys(config('seo.pages')));
    }

    public function test_every_static_page_has_exactly_one_of_each_seo_tag(): void
    {
        foreach ($this->staticPaths() as $path) {
            $x = $this->dom($path);
            foreach (['//head/title', '//meta[@name="description"]', '//link[@rel="canonical"]', '//meta[@name="robots"]', '//meta[@property="og:title"]', '//meta[@name="twitter:card"]'] as $query) {
                $this->assertSame(1, $x->query($query)->length, "{$path}: expected one {$query}");
            }
            $this->assertSame(1, $x->query('//h1')->length, "{$path}: expected one H1");
        }
    }

    public function test_titles_and_descriptions_are_not_double_escaped(): void
    {
        foreach ($this->staticPaths() as $path) {
            $html = $this->get($path)->getContent();
            $this->assertStringNotContainsString('&amp;amp;', $html, $path);
            $this->assertStringNotContainsString('&amp;#039;', $html, $path);
        }

        $this->assertStringStartsWith('Meet the Softorio Co-Founders: Arif Billah & Asma Akter', $this->dom('/our-founders')->query('//head/title')->item(0)->textContent);
    }

    public function test_seeded_seo_content_is_unique_and_rendered(): void
    {
        $titles = [];
        $descriptions = [];
        foreach ($this->staticPaths() as $path) {
            $x = $this->dom($path);
            $titles[] = $x->query('//head/title')->item(0)->textContent;
            $descriptions[] = $x->query('//meta[@name="description"]')->item(0)->getAttribute('content');
        }

        $this->assertSame(count($titles), count(array_unique($titles)), 'Duplicate page titles');
        $this->assertSame(count($descriptions), count(array_unique($descriptions)), 'Duplicate meta descriptions');
        $this->assertSame(PageSeo::forRoute('seo')->title, $this->dom('/seo')->query('//head/title')->item(0)->textContent);
    }

    public function test_canonical_and_og_url_use_the_production_origin(): void
    {
        $x = $this->dom('/web-development');

        $this->assertSame('https://softorio.com/web-development', $x->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
        $this->assertSame('https://softorio.com/web-development', $x->query('//meta[@property="og:url"]')->item(0)->getAttribute('content'));
        $this->assertSame('https://softorio.com/', $this->dom('/')->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
    }

    public function test_all_json_ld_is_valid_and_has_page_level_schema(): void
    {
        foreach ($this->staticPaths() as $path) {
            $types = [];
            foreach ($this->dom($path)->query('//script[@type="application/ld+json"]') as $script) {
                $data = json_decode($script->textContent, true);
                $this->assertIsArray($data, "{$path}: invalid JSON-LD");
                $types[] = $data['@type'];
            }
            $this->assertContains('Organization', $types, $path);
            $this->assertNotEmpty(array_diff($types, ['Organization', 'WebSite', 'BreadcrumbList']), "{$path}: no page-level schema");
        }

        $this->assertContains('WebSite', $this->jsonLdTypes('/'));
        $this->assertContains('Service', $this->jsonLdTypes('/seo'));
    }

    private function jsonLdTypes(string $path): array
    {
        return collect($this->dom($path)->query('//script[@type="application/ld+json"]'))
            ->map(fn ($s) => json_decode($s->textContent, true)['@type'])->all();
    }

    public function test_dashboard_values_reach_the_frontend(): void
    {
        PageSeo::forRoute('about-us')->update([
            'title' => 'Custom About Title From The Dashboard',
            'meta_description' => 'Custom description from the dashboard.',
            'og_title' => 'Custom OG title',
            'robots_advanced' => 'noarchive, max-image-preview:large',
        ]);

        $x = $this->dom('/about-us');

        $this->assertSame('Custom About Title From The Dashboard', $x->query('//head/title')->item(0)->textContent);
        $this->assertSame('Custom description from the dashboard.', $x->query('//meta[@name="description"]')->item(0)->getAttribute('content'));
        $this->assertSame('Custom OG title', $x->query('//meta[@property="og:title"]')->item(0)->getAttribute('content'));
        $this->assertSame('index, follow, noarchive, max-image-preview:large', $x->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
    }

    public function test_noindex_static_page_is_excluded_from_sitemap_even_without_seeding(): void
    {
        PageSeo::where('route_name', 'our-founders')->delete();

        $this->assertSame('noindex, nofollow', $this->dom('/our-founders')->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
        $this->get('/sitemap.xml')->assertDontSee('/our-founders', false);
    }

    public function test_thin_article_is_noindex_and_out_of_sitemap_until_written(): void
    {
        $article = Article::first();
        $this->assertNull($article->body);

        $path = route('our-resources.show', $article, false);
        $this->assertSame('noindex, follow', $this->dom($path)->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
        $this->get('/sitemap.xml')->assertDontSee($path, false);

        $article->update(['body' => '<p>'.str_repeat('Useful practical advice about WordPress performance. ', 30).'</p>']);

        $x = $this->dom($path);
        $this->assertSame('index, follow', $x->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
        $this->assertSame('article', $x->query('//meta[@property="og:type"]')->item(0)->getAttribute('content'));
        $this->assertContains('Article', $this->jsonLdTypes($path));
        $this->get('/sitemap.xml')->assertSee('https://softorio.com'.$path, false);
    }

    public function test_unpublished_article_is_noindex(): void
    {
        $article = Article::first();
        $article->update([
            'body' => '<p>'.str_repeat('word ', 200).'</p>',
            'published_at' => now()->addWeek(),
        ]);

        $this->assertStringContainsString('noindex', $this->dom(route('our-resources.show', $article, false))
            ->query('//meta[@name="robots"]')->item(0)->getAttribute('content'));
    }

    public function test_content_seo_row_follows_its_record(): void
    {
        $item = PortfolioItem::create(['title' => 'Brand New Project', 'client' => 'Acme', 'category' => 'cloud', 'tags' => ['Cloud']]);

        $this->assertSame('brand-new-project', $item->slug);
        $this->assertSame('/our-works/brand-new-project', $item->seo->page_path);

        $item->update(['title' => 'Renamed Project', 'slug' => 'renamed-project']);
        $this->assertSame('Renamed Project', $item->seo->fresh()->page_label);

        $id = $item->seo->id;
        $item->delete();
        $this->assertNull(PageSeo::find($id));
    }

    public function test_sitemap_is_valid_unique_and_respects_redirects_and_canonicals(): void
    {
        PageSeo::forRoute('affiliate-program')->update(['canonical_url' => 'https://softorio.com/about-us']);
        Redirect::create(['from_path' => '/automation-solution', 'to_path' => '/automation', 'status_code' => 301, 'is_active' => true]);

        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent());
        $locs = [];
        foreach ($xml->url as $url) {
            $locs[] = (string) $url->loc;
            $this->assertNotEmpty((string) $url->lastmod);
        }

        $this->assertSame(count($locs), count(array_unique($locs)));
        $this->assertContains('https://softorio.com/', $locs);
        $this->assertNotContains('https://softorio.com/affiliate-program', $locs);
        $this->assertNotContains('https://softorio.com/automation-solution', $locs);
        foreach ($locs as $loc) {
            $this->assertStringStartsWith('https://softorio.com/', $loc);
        }
    }

    public function test_robots_txt_allows_assets_and_points_to_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://softorio.com/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringNotContainsString('Disallow: /build', $robots);
        $this->assertStringNotContainsString('Disallow: /images', $robots);
    }

    public function test_auditor_scores_from_rendered_html(): void
    {
        $seo = app(SeoAuditor::class)->scan(PageSeo::forRoute('seo'));

        $this->assertTrue($seo->seo_checks['rendered']);
        $this->assertSame(100, $seo->seo_score);

        $blank = PageSeo::forRoute('about-us');
        $blank->update(['title' => null, 'meta_description' => null, 'focus_keyword' => null]);
        $scored = app(SeoAuditor::class)->scan($blank);
        $this->assertSame(70, $scored->seo_score);
        $this->assertSame('fail', collect($scored->seo_checks['checks'])->firstWhere('key', 'title')['status']);
    }

    public function test_auditor_detects_structural_problems(): void
    {
        $seo = PageSeo::forRoute('seo');
        $html = '<html><head><title>T</title><link rel="canonical" href="https://softorio.com/seo"><link rel="canonical" href="https://x.test"><meta name="robots" content="index, follow"></head>'
            .'<body><h1>A</h1><h1>B</h1><h4>skip</h4><img src="a.png"><script type="application/ld+json">{bad</script></body></html>';

        $checks = collect(app(SeoAuditor::class)->audit($seo, $html)['checks'])->keyBy('key');

        $this->assertSame('fail', $checks['h1']['status']);
        $this->assertSame('fail', $checks['headings']['status']);
        $this->assertSame('fail', $checks['images']['status']);
        $this->assertSame('fail', $checks['links']['status']);
        $this->assertSame('fail', $checks['canonical']['status']);
        $this->assertSame('fail', $checks['schema']['status']);
    }

    public function test_admin_can_edit_page_seo_and_conflicting_canonical_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $seo = PageSeo::forRoute('automation');

        Livewire::test(EditPageSeo::class, ['record' => $seo->getRouteKey()])
            ->fillForm(['title' => 'New Automation Title', 'focus_keyword' => 'automation', 'secondary_keywords' => ['zapier']])
            ->call('save')
            ->assertHasNoFormErrors();

        $seo->refresh();
        $this->assertSame('New Automation Title', $seo->title);
        $this->assertSame(['zapier'], $seo->secondary_keywords);
        $this->assertNotNull($seo->seo_scanned_at);

        Livewire::test(EditPageSeo::class, ['record' => $seo->getRouteKey()])
            ->fillForm(['robots_index' => false, 'canonical_url' => 'https://softorio.com/'])
            ->call('save')
            ->assertHasFormErrors(['canonical_url']);
    }

    public function test_article_form_saves_seo_through_the_relationship(): void
    {
        $this->actingAs(User::factory()->create());
        $article = Article::first();

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->fillForm(['seo.title' => 'Article SEO Title From Form', 'seo.focus_keyword' => 'wordpress speed'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Article SEO Title From Form', $article->seo->fresh()->title);
        $this->assertSame(1, PageSeo::where('seoable_type', Article::class)->where('seoable_id', $article->id)->count());
        $this->assertSame('Article SEO Title From Form', $this->dom(route('our-resources.show', $article, false))->query('//head/title')->item(0)->textContent);
    }
}
