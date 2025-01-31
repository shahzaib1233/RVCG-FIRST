<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // Unsigned BigInteger
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade'); // User who added the lead
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade'); // User assigned to the lead
            $table->text('special_notes')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed', 'rejected'])->default('open');
            $table->foreignId('lead_type_id')->constrained('lead_types')->onDelete('cascade'); // FK to lead_types table

            // Additional fields
            $table->string('source')->nullable(); // Lead source (e.g., Website, Referral)
            $table->json('tags')->nullable(); // Tags for the lead
            $table->string('position')->nullable(); // Position of the lead
            $table->string('email')->nullable(); // Email address
            $table->string('website')->nullable(); // Website URL
            $table->string('phone')->nullable(); // Phone number
            $table->decimal('lead_value', 10, 2)->nullable(); // Lead's value
            $table->string('company')->nullable(); // Company name
            $table->string('address')->nullable(); // Street address
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('default_language')->nullable(); // Preferred language
            $table->text('description')->nullable(); // Additional notes
            $table->boolean('contacted_today')->default(false); // Whether contacted today

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('leads');
    }
};
