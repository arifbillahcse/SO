<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_tag');
            $table->string('hero_title');
            $table->text('hero_description');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('map_url', 2048)->nullable();
            $table->text('business_hours')->nullable();
            $table->string('form_title');
            $table->text('form_description');
            $table->string('submit_label');
            $table->text('success_message');
            $table->string('notification_email')->nullable();
            $table->json('next_steps')->nullable();
            $table->json('faqs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_page_settings');
    }
};
