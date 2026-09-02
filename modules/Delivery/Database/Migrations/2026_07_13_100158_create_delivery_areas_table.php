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
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('estimated_days')->default(2);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->foreignId('delivery_location_id')->constrained('delivery_locations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['name', 'delivery_location_id']);
            $table->index(['delivery_location_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_areas');
    }
};
