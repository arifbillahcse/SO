<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->string('tracking_mode', 32)->default('ga4');
            $table->string('google_tag_manager_id', 100)->nullable();
            $table->string('google_site_verification', 255)->nullable();
            $table->boolean('custom_code_enabled')->default(false);
            $table->longText('custom_head_code')->nullable();
            $table->longText('custom_body_start_code')->nullable();
            $table->longText('custom_body_end_code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_mode', 'google_tag_manager_id', 'google_site_verification',
                'custom_code_enabled', 'custom_head_code', 'custom_body_start_code', 'custom_body_end_code',
            ]);
        });
    }
};
