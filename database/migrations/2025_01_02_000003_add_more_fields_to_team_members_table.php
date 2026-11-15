<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('short_role')->nullable()->after('role');
            $table->string('email')->nullable()->after('photo_path');
            $table->string('website_url')->nullable()->after('email');
            $table->json('stats')->nullable()->after('skills'); // [{"value": "300+", "label": "Websites Delivered"}, ...]
            $table->string('credentials_heading')->default('Recognition & Roles')->after('credentials');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['short_role', 'email', 'website_url', 'stats', 'credentials_heading']);
        });
    }
};
