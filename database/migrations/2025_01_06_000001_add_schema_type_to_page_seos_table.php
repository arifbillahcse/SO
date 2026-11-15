<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->string('schema_type')->default('WebPage')->after('robots_advanced');
        });
    }

    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->dropColumn('schema_type');
        });
    }
};
