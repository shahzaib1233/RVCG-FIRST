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
            // First, check if longitude and latitude exist, if not, add them.
            $table->decimal('longitude', 10, 7)->nullable()->after('lot_size'); // Longitude
            $table->decimal('latitude', 10, 7)->nullable()->after('longitude'); // Latitude
            
            // Now add other missing columns
            $table->string('school_district')->nullable()->after('latitude'); // School district information
            $table->decimal('walkability_score', 5, 2)->nullable()->after('school_district'); // Walkability score
            $table->decimal('crime_rate', 5, 2)->nullable()->after('walkability_score'); // Crime rate (scale of 1-10 or other)
            $table->decimal('roi', 5, 2)->nullable()->after('crime_rate'); // Return on investment (ROI)
            $table->decimal('monthly_rent', 10, 2)->nullable()->after('roi'); // Monthly rent (if applicable)
            $table->decimal('cap_rate', 5, 2)->nullable()->after('monthly_rent'); // Capitalization rate (Cap rate)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 
                'longitude', 
                'school_district', 
                'walkability_score', 
                'crime_rate', 
                'roi', 
                'monthly_rent', 
                'cap_rate'
            ]);
        });
    }
};
