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
        Schema::create('offer_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade'); // Foreign key for Offer
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key for User (Buyer)
            $table->foreignId('listing_owner_id')->constrained('users')->onDelete('cascade'); // Foreign key for Listing Owner (Seller)
            $table->text('negotiation_comments'); // Comments during negotiation
            $table->decimal('negotiated_price', 10, 2); // Price after negotiation (if any)
            $table->enum('status', ['pending', 'accepted', 'rejected']); // Current status of the negotiation
            $table->timestamp('negotiation_date')->useCurrent(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_history');
    }
};
