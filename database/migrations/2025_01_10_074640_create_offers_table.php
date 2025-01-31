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
        Schema::create('offers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('listing_id'); 
            $table->unsignedBigInteger('user_id');
            $table->decimal('offer_price', 10, 2); 
            $table->timestamp('offer_date')->useCurrent(); 
            $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->text('message')->nullable(); 
            $table->date('expiry_date')->nullable();
            $table->string('payment_method', 255)->nullable();
            $table->text('negotiation_comments')->nullable(); 
            $table->decimal('accepted_price', 10, 2)->nullable();
            $table->date('closing_date')->nullable();
            $table->timestamps(); 

            // Foreign key constraints
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
