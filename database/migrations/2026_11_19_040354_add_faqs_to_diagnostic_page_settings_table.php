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
            $table->string('faq_title')->nullable();
            $table->json('faqs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostic_page_settings', function (Blueprint $table) {
            $table->dropColumn(['faq_title', 'faqs']);
        });
    }
};
