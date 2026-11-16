<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('featured_image_path')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->text('featured_image_caption')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['featured_image_path', 'featured_image_alt', 'featured_image_caption']);
        });
    }
};
