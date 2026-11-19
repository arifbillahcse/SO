<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diagnostic_page_settings', function (Blueprint $table) {
            $table->string('hero_primary_text', 100)->nullable();
            $table->string('hero_primary_url', 2048)->nullable();
            $table->string('hero_secondary_text', 100)->nullable();
            $table->string('hero_secondary_url', 2048)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_page_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_primary_text', 'hero_primary_url', 'hero_secondary_text', 'hero_secondary_url']);
        });
    }
};
