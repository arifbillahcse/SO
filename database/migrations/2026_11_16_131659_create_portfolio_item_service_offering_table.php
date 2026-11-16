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
        Schema::create('portfolio_item_service_offering', function (Blueprint $table) {
            $table->foreignId('service_offering_id')->constrained()->cascadeOnDelete();
            $table->foreignId('portfolio_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->primary(['service_offering_id', 'portfolio_item_id'], 'offering_portfolio_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_item_service_offering');
    }
};
