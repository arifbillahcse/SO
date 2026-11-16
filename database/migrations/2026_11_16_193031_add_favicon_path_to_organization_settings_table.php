<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            $table->string('favicon_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table): void {
            $table->dropColumn('favicon_path');
        });
    }
};
