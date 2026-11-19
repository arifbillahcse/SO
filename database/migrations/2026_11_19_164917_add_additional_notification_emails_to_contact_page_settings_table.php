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
        Schema::table('contact_page_settings', function (Blueprint $table) {
            $table->json('additional_notification_emails')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_page_settings', function (Blueprint $table) {
            $table->dropColumn('additional_notification_emails');
        });
    }
};
