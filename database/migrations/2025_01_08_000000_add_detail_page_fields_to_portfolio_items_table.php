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
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->text('description')->nullable()->after('category');
            $table->longText('body')->nullable()->after('description');
            $table->string('meta_title')->nullable()->after('body');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->json('focus_keywords')->nullable()->after('meta_description');
            $table->boolean('robots_index')->default(true)->after('focus_keywords');
            $table->boolean('robots_follow')->default(true)->after('robots_index');
        });

        foreach (DB::table('portfolio_items')->whereNull('slug')->get() as $item) {
            DB::table('portfolio_items')->where('id', $item->id)->update([
                'slug' => Str::slug($item->title).'-'.$item->id,
            ]);
        }

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 'description', 'body', 'meta_title',
                'meta_description', 'focus_keywords', 'robots_index', 'robots_follow',
            ]);
        });
    }
};
