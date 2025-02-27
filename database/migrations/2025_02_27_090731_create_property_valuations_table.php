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
        Schema::create('property_valuations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('property_type');
            $table->decimal('price', 10, 2);
            $table->integer('square_foot');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->json('property_images')->nullable(); // To store image paths
            $table->string('owner_name');
            $table->integer('owner_age');
            $table->string('ownership_type');
            $table->string('owner_email');
            $table->string('govt_id_proof');
            $table->string('owner_contact');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_valuations');
    }
};
