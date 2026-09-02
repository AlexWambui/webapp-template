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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            
            // Snapshot of product at time of purchase
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('product_type')->default('goods'); // [goods, service]

            // Used decimals in case of time-based items (e.g. 2.5 hours)
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('returned_quantity', 10, 2)->default(0.00); // in case a product gets returned

            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->string('discount_type')->nullable(); // 'bulk', 'promo', 'clearance', 'manual'
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
