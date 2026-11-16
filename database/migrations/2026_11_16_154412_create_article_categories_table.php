<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('article_category_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('source_key')->nullable()->unique();
        });

        $names = collect(['WordPress', 'SEO', 'Cloud', 'Automation', 'eCommerce'])
            ->merge(DB::table('articles')->distinct()->pluck('category'))->filter()->unique();
        foreach ($names as $name) {
            $existingId = DB::table('article_categories')->where('name', $name)->value('id');
            if ($existingId) {
                DB::table('articles')->where('category', $name)->update(['article_category_id' => $existingId]);

                continue;
            }
            $base = Str::slug($name) ?: 'category';
            $slug = $base;
            $suffix = 2;
            while (DB::table('article_categories')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }
            $id = DB::table('article_categories')->insertGetId([
                'name' => $name, 'slug' => $slug, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('articles')->where('category', $name)->update(['article_category_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('article_category_id');
            $table->dropUnique(['source_key']);
            $table->dropColumn('source_key');
        });
        Schema::dropIfExists('article_categories');
    }
};
