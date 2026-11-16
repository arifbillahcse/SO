<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_page_settings', function (Blueprint $table): void {
            $table->boolean('whatsapp_chat_enabled')->default(true);
        });

        DB::table('contact_page_settings')->where(function ($query): void {
            $query->whereNull('whatsapp_number')->orWhere('whatsapp_number', '');
        })->update(['whatsapp_number' => '+8801779440297']);
    }

    public function down(): void
    {
        Schema::table('contact_page_settings', function (Blueprint $table): void {
            $table->dropColumn('whatsapp_chat_enabled');
        });
    }
};
