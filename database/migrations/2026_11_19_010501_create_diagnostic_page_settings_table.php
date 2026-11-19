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
        Schema::create('diagnostic_page_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('plans_enabled')->default(true);
            $table->string('plans_title');
            $table->text('plans_introduction')->nullable();
            $table->string('pricing_faq_question');
            $table->text('pricing_faq_answer');
            $table->string('demo_plan_label');
            $table->string('undecided_plan_label');
            $table->json('plans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_page_settings');
    }
};
