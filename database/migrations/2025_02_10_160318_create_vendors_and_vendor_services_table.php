<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Creating vendors table
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to users table
            $table->text('address')->nullable();
            $table->string('city')->nullable(); // City of vendor's physical location
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();
        });

        // Creating vendor_services table
        Schema::create('vendor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Links to vendors table
            $table->string('service_name'); // e.g., "Home Inspection", "Property Cleaning"
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable(); // Optional service price
            $table->string('service_city')->nullable(); // City where service is offered
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Dropping tables in reverse order to maintain foreign key integrity
        Schema::dropIfExists('vendor_services');
        Schema::dropIfExists('vendors');
    }
};
