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
        Schema::create('service_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('catalogue_key')->nullable()->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description');
            $table->json('scope')->nullable();
            $table->string('icon')->nullable();
            $table->string('action_label')->nullable();
            $table->string('action_route')->nullable();
            $table->string('action_fragment')->nullable();
            $table->string('action_url', 2048)->nullable();
            $table->string('availability')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->index(['service_id', 'is_published', 'sort_order']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_offerings');
    }
};
