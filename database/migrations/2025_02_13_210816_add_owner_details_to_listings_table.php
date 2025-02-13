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
        Schema::table('listings', function (Blueprint $table) {
            $table->string('Owner_Full_Name')->nullable();
            $table->integer('Owner_Age')->nullable();
            $table->string('Owner_Contact_Number')->nullable();
            $table->string('Owner_Email_Address')->nullable();
            $table->string('Owner_Government_ID_Proof')->nullable();
            $table->string('Owner_Property_Ownership_Proof')->nullable();
            $table->enum('Owner_Ownership_Type', ['Freehold', 'Leasehold', 'Joint Ownership'])->nullable();
            $table->json('Owner_Property_Documents')->nullable(); // For storing multiple documents
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            //
        });
    }
};
