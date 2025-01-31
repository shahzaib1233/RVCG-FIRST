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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();  // Add phone_number column
            $table->string('address')->nullable();  // Add address column
            $table->json('social_media_profiles')->nullable();  // Add social media profiles column
            $table->text('bankruptcy_details')->nullable();  // Add bankruptcy details column
            $table->text('liens_details')->nullable();  // Add liens details column
            $table->string('contact_email')->nullable();  // Add contact email column
            $table->date('dob')->nullable();  // Add date of birth column instead of age
            $table->string('income_level')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
