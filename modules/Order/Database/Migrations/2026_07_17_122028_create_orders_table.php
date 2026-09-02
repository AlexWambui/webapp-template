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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('order_number')->unique();
            $table->string('order_channel')->default('website');
            $table->string('order_status')->default('pending')->comment('pending, confirmed, processing, ready_for_pickup, completed, cancelled, refunded'); // current status for quick access

            // Fiscals
            $table->string('currency', 3)->default('KES');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_cost_price', 10, 2)->default(0);
            $table->decimal('total_selling_price', 10, 2)->default(0); // subtotal + delivery
            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();

            $table->string('delivery_method')->default('shop');
            $table->string('delivery_location')->nullable();
            $table->string('delivery_area')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('delivery_status')->nullable()->comment('pending, picked_up, in_transit, out_for_delivery, delivered, delivery_failed, returned');

            $table->text('additional_info')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('cashier_notes')->nullable();

            // Snapshots for when user gets anonymized incase of account deletion
            // Customer information snapshot
            $table->string('customer_name_snapshot')->nullable();
            $table->string('customer_email_snapshot')->nullable();
            $table->string('customer_phone_snapshot')->nullable();
            
            // Delivery details snapshot
            $table->string('shipping_location_snapshot')->nullable();
            $table->string('shipping_area_snapshot')->nullable();
            $table->string('shipping_address_snapshot')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('current_order_status_id')->nullable()->constrained('order_statuses')->nullOnDelete();
            $table->foreignId('current_delivery_status_id')->constrained('order_statuses')->nullable()->nullOnDelete();
            $table->timestamp('sold_at');
            $table->timestamps();
            
            // Indexes for fast lookups
            $table->index(['sold_at']);
            $table->index(['customer_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
