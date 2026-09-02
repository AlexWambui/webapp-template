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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name', 200);
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();

            $table->string('type')->default('goods');

            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            // Inventory Tracking
            $table->decimal('current_stock', 10, 2)->default(0.00); // For services, default is 0
            $table->boolean('track_inventory')->default(false);
            $table->decimal('low_stock_threshold', 10, 2)->default(0.00);

            $table->string('unit_of_measure')->default('pcs');

            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
