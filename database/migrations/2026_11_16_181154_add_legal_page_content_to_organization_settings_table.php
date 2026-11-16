<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            foreach (['tos', 'privacy'] as $page) {
                $table->string("{$page}_title")->nullable();
                $table->text("{$page}_intro")->nullable();
                $table->longText("{$page}_body")->nullable();
                $table->date("{$page}_effective_date")->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            foreach (['tos', 'privacy'] as $page) {
                $table->dropColumn(["{$page}_title", "{$page}_intro", "{$page}_body", "{$page}_effective_date"]);
            }
        });
    }
};
