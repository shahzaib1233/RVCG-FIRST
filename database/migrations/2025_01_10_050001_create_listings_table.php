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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Property title
            $table->text('description'); // Property description
            $table->unsignedBigInteger('city_id'); // Foreign key to cities table
            $table->unsignedBigInteger('country_id'); // Foreign key to countries table
            $table->unsignedBigInteger('property_type_id'); // Foreign key to property_types table
            $table->unsignedBigInteger('property_status_id'); // Foreign key to property_statuses table
            $table->timestamp('listing_date')->useCurrent(); // Listing date (using current timestamp)
            $table->decimal('price', 10, 2); // Property price
            $table->decimal('square_foot', 10, 2)->nullable(); // Square Footage of the property
            $table->string('parking')->nullable(); // Number of parking spaces
            $table->integer('year_built')->nullable(); // Year the property was built
            $table->decimal('lot_size', 10, 2)->nullable(); // Lot size (in square feet or acres)
            $table->integer('bedrooms')->nullable(); // Number of bedrooms
            $table->integer('bathrooms')->nullable(); // Number of bathrooms
            $table->integer('half_bathrooms')->nullable(); // Number of half bathrooms
            $table->decimal('arv', 10, 2)->nullable(); // After Repair Value (ARV)
            $table->decimal('gross_margin', 10, 2)->nullable(); // Gross Margin (profit margin)
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->timestamps(); // Created at & Updated at columns

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Cascade on delete
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
            $table->foreign('property_status_id')->references('id')->on('property_statuses')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
