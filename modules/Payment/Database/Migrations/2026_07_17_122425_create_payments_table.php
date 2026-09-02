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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('payment_method'); // [stripe, paypal, mpesa, card, bank_transfer]
            $table->string('transaction_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('payment_status', 30)->default('pending'); // pending, completed, failed, refunded

            // Metadata block to avoid massive dedicated columns for card and mpesa specific details
            $table->json('payment_metadata')->nullable(); // Saves raw card details or callback payloads

            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            $table->timestamp('paid_at');
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('transaction_reference');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
