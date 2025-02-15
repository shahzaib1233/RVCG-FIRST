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
             $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to user
            $table->decimal('amount', 10, 2); // Amount paid
            $table->string('payment_status'); // Payment status (e.g., succeeded, failed)
            $table->string('transaction_id')->unique(); // Stripe transaction ID
            $table->timestamps();
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
