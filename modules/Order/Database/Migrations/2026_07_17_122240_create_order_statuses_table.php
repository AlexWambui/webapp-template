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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('type')->default('order'); // order or delivery
            $table->string('status'); // pending, processing, etc.
            $table->string('notes')->nullable(); // admin notes about the specific transition
            $table->json('metadata')->nullable(); // For additional data like tracking info, location, etc.
            
            // Who changed the status?
            $table->boolean('is_system')->default(false); // true if system auto-changed
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            $table->timestamp('changed_at'); // when it happened
            
            $table->timestamps();
            
            $table->index(['order_id', 'type']);
            $table->index(['order_id', 'changed_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
