<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('client')->nullable()->change();
            $table->string('project_type', 40)->default('website_project');
            $table->string('attribution')->nullable();
            $table->string('video_url', 2048)->nullable();
            $table->string('repository_url', 2048)->nullable();
            $table->string('evidence_status', 64)->default('not_reviewed');
            $table->text('evidence_notes')->nullable();
            $table->timestamp('evidence_checked_at')->nullable();
            $table->string('source_key', 160)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations once all client values can satisfy the old schema.
     */
    public function down(): void
    {
        if (DB::table('portfolio_items')->whereNull('client')->exists()) {
            throw new RuntimeException('Cannot restore required clients while portfolio entries have no client. Populate those client values before rolling back.');
        }

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('client')->nullable(false)->change();
            $table->dropUnique(['source_key']);
            $table->dropColumn([
                'project_type', 'attribution', 'video_url', 'repository_url',
                'evidence_status', 'evidence_notes', 'evidence_checked_at', 'source_key',
            ]);
        });
    }
};
