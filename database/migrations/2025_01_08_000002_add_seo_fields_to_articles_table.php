<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('body');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->json('focus_keywords')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('focus_keywords');
            $table->boolean('robots_index')->default(true)->after('og_image');
            $table->boolean('robots_follow')->default(true)->after('robots_index');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title', 'meta_description', 'focus_keywords',
                'og_image', 'robots_index', 'robots_follow',
            ]);
        });
    }
};
