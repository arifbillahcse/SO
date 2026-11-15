<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Makes page_seos the single SEO store for every public URL. Static pages keep
 * their route_name row; articles, case studies and portfolio items get a
 * polymorphic row instead of the SEO columns briefly stored on their own
 * tables, which are copied across and then dropped.
 */
return new class extends Migration
{
    private const CONTENT_TABLES = [
        'articles' => ['class' => 'App\Models\Article', 'type' => 'article', 'og_image' => true],
        'case_studies' => ['class' => 'App\Models\CaseStudy', 'type' => 'case_study', 'og_image' => true],
        'portfolio_items' => ['class' => 'App\Models\PortfolioItem', 'type' => 'portfolio', 'og_image' => false],
    ];

    public function up(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->string('route_name')->nullable()->change();
            $table->nullableMorphs('seoable');
            $table->string('page_type')->default('page')->index();
            $table->string('page_label')->nullable();
            $table->string('page_path')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->text('content_notes')->nullable();
            $table->unsignedTinyInteger('seo_score')->nullable()->index();
            $table->json('seo_checks')->nullable();
            $table->timestamp('seo_scanned_at')->nullable();
            $table->unique(['seoable_type', 'seoable_id']);
        });

        foreach (DB::table('page_seos')->get() as $row) {
            $keywords = array_values(array_filter(json_decode($row->focus_keywords ?? '[]', true) ?: []));
            $page = config("seo.pages.{$row->route_name}");

            DB::table('page_seos')->where('id', $row->id)->update([
                'focus_keyword' => $keywords[0] ?? null,
                'secondary_keywords' => count($keywords) > 1 ? json_encode(array_slice($keywords, 1)) : null,
                'page_type' => $page['type'] ?? 'page',
                'page_label' => $page['label'] ?? $row->route_name,
            ]);
        }

        foreach (self::CONTENT_TABLES as $table => $meta) {
            foreach (DB::table($table)->get() as $record) {
                $keywords = array_values(array_filter(json_decode($record->focus_keywords ?? '[]', true) ?: []));

                DB::table('page_seos')->insert([
                    'seoable_type' => $meta['class'],
                    'seoable_id' => $record->id,
                    'page_type' => $meta['type'],
                    'page_label' => $record->title,
                    'title' => $record->meta_title,
                    'meta_description' => $record->meta_description,
                    'focus_keyword' => $keywords[0] ?? null,
                    'secondary_keywords' => count($keywords) > 1 ? json_encode(array_slice($keywords, 1)) : null,
                    'og_image' => $meta['og_image'] ? $record->og_image : null,
                    'robots_index' => $record->robots_index ?? true,
                    'robots_follow' => $record->robots_follow ?? true,
                    'schema_type' => $meta['type'] === 'article' ? 'Article' : 'CreativeWork',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table($table, function (Blueprint $blueprint) use ($meta) {
                $blueprint->dropColumn(array_merge(
                    ['meta_title', 'meta_description', 'focus_keywords', 'robots_index', 'robots_follow'],
                    $meta['og_image'] ? ['og_image'] : [],
                ));
            });
        }

        Schema::table('page_seos', function (Blueprint $table) {
            $table->dropColumn('focus_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->json('focus_keywords')->nullable();
        });

        foreach (self::CONTENT_TABLES as $table => $meta) {
            Schema::table($table, function (Blueprint $blueprint) use ($meta) {
                $blueprint->string('meta_title')->nullable();
                $blueprint->string('meta_description', 320)->nullable();
                $blueprint->json('focus_keywords')->nullable();
                if ($meta['og_image']) {
                    $blueprint->string('og_image')->nullable();
                }
                $blueprint->boolean('robots_index')->default(true);
                $blueprint->boolean('robots_follow')->default(true);
            });

            foreach (DB::table('page_seos')->where('seoable_type', $meta['class'])->get() as $seo) {
                DB::table($table)->where('id', $seo->seoable_id)->update(array_merge([
                    'meta_title' => $seo->title,
                    'meta_description' => $seo->meta_description,
                    'focus_keywords' => json_encode(array_values(array_filter(array_merge(
                        [$seo->focus_keyword],
                        json_decode($seo->secondary_keywords ?? '[]', true) ?: [],
                    )))),
                    'robots_index' => $seo->robots_index,
                    'robots_follow' => $seo->robots_follow,
                ], $meta['og_image'] ? ['og_image' => $seo->og_image] : []));
            }
        }

        foreach (DB::table('page_seos')->whereNull('seoable_type')->get() as $row) {
            DB::table('page_seos')->where('id', $row->id)->update([
                'focus_keywords' => json_encode(array_values(array_filter(array_merge(
                    [$row->focus_keyword],
                    json_decode($row->secondary_keywords ?? '[]', true) ?: [],
                )))),
            ]);
        }

        DB::table('page_seos')->whereNotNull('seoable_type')->delete();

        Schema::table('page_seos', function (Blueprint $table) {
            $table->dropUnique(['seoable_type', 'seoable_id']);
            $table->dropIndex(['page_type']);
            $table->dropIndex(['seo_score']);
            $table->dropMorphs('seoable');
            $table->dropColumn([
                'page_type', 'page_label', 'page_path', 'focus_keyword', 'secondary_keywords',
                'content_notes', 'seo_score', 'seo_checks', 'seo_scanned_at',
            ]);
            $table->string('route_name')->nullable(false)->change();
        });
    }
};
