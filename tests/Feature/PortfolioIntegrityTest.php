<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\CloudPortfolioMediaSeeder;
use Database\Seeders\CloudPortfolioSeeder;
use Database\Seeders\PdfPortfolioMediaSeeder;
use Database\Seeders\PdfPortfolioSeeder;
use Database\Seeders\PortfolioSeoSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioIntegrityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_every_portfolio_detail_has_unique_metadata_valid_schema_an_image_and_consistent_sitemap_membership(): void
    {
        Storage::fake('public');
        foreach (['pdf-portfolio-media.json', 'cloud-portfolio-media.json'] as $manifest) {
            $data = json_decode(file_get_contents(database_path('seeders/data/'.$manifest)), true, flags: JSON_THROW_ON_ERROR);
            foreach ($data['projects'] as $media) {
                Storage::disk('public')->put($media['thumbnail_path'], 'image');
            }
        }
        $this->seed([PdfPortfolioSeeder::class, CloudPortfolioSeeder::class, PdfPortfolioMediaSeeder::class, CloudPortfolioMediaSeeder::class, PortfolioSeoSeeder::class]);
        $projects = PortfolioItem::orderBy('id')->get();
        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();
        $titles = [];
        $descriptions = [];
        $canonicals = [];
        $indexable = 0;

        foreach ($projects as $project) {
            $response = $this->get(route('our-works.show', $project))->assertOk();
            $document = new DOMDocument;
            libxml_use_internal_errors(true);
            $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
            libxml_clear_errors();
            $xpath = new DOMXPath($document);

            foreach (['//title', '//h1', '//meta[@name="description"]', '//link[@rel="canonical"]', '//meta[@name="robots"]', '//meta[@property="og:image"]'] as $selector) {
                $this->assertSame(1, $xpath->query($selector)->length, $project->slug.': '.$selector);
            }
            $this->assertSame($project->title, trim($xpath->query('//h1')->item(0)->textContent));
            $title = $xpath->query('//title')->item(0)->textContent;
            $description = $xpath->query('//meta[@name="description"]')->item(0)->getAttribute('content');
            $canonical = $xpath->query('//link[@rel="canonical"]')->item(0)->getAttribute('href');
            $robots = $xpath->query('//meta[@name="robots"]')->item(0)->getAttribute('content');
            $this->assertNotEmpty($title);
            $this->assertNotEmpty($description);
            $this->assertSame($project->seo->title, $title);
            $this->assertSame($project->seo->meta_description, $description);
            foreach (['og:title' => 'og_title', 'og:description' => 'og_description', 'twitter:title' => 'twitter_title', 'twitter:description' => 'twitter_description'] as $name => $field) {
                $attribute = str_starts_with($name, 'og:') ? 'property' : 'name';
                $nodes = $xpath->query('//meta[@'.$attribute.'="'.$name.'"]');
                $this->assertSame(1, $nodes->length, $project->slug.': '.$name);
                $this->assertSame($project->seo->getAttribute($field), $nodes->item(0)->getAttribute('content'));
            }
            $this->assertSame('https://softorio.com/storage/'.$project->seo->twitter_image, $xpath->query('//meta[@name="twitter:image"]')->item(0)->getAttribute('content'));
            $this->assertSame('https://softorio.com/our-works/'.$project->slug, $canonical);
            $this->assertSame('https://softorio.com/storage/'.$project->thumbnail_path, $xpath->query('//meta[@property="og:image"]')->item(0)->getAttribute('content'));
            $image = $xpath->query('//img[contains(@class,"portfolio-preview")]');
            if (str_starts_with($project->thumbnail_path, 'portfolio/covers/')) {
                $this->assertSame(0, $image->length);
                $this->assertSame(1, $xpath->query('//section[@aria-labelledby="project-visual-title"]//div[contains(@class,"portfolio-title-cover")]')->length);
            } else {
                $this->assertSame(1, $image->length);
                $this->assertNotEmpty($image->item(0)->getAttribute('alt'));
            }
            $schemas = [];
            foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
                $schema = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
                $schemas[$schema['@type']] = $schema;
            }
            $this->assertArrayHasKey('CreativeWork', $schemas);
            $this->assertArrayHasKey('BreadcrumbList', $schemas);
            $this->assertSame($canonical.'#project', $schemas['CreativeWork']['@id']);
            if (str_starts_with($robots, 'index,')) {
                $this->assertStringContainsString('<loc>'.$canonical.'</loc>', $sitemap);
                $indexable++;
            } else {
                $this->assertStringNotContainsString('<loc>'.$canonical.'</loc>', $sitemap);
            }
            $titles[] = $title;
            $descriptions[] = $description;
            $canonicals[] = $canonical;
        }

        $this->assertCount(118, $titles);
        $this->assertCount(118, array_unique($titles));
        $this->assertCount(118, array_unique($descriptions));
        $this->assertCount(118, array_unique($canonicals));
        $this->assertSame(5, $indexable);
    }
}
