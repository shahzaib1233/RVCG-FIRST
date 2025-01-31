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
             $table->decimal('estimated_roi', 10, 2)->nullable()->after('gross_margin'); // Estimated ROI
            // $table->decimal('cap_rate', 10, 2)->nullable()->after('monthly_rent'); // Capitalization rate
            $table->string('geolocation_coordinates')->nullable()->after('cap_rate'); // Geolocation coordinates
            $table->string('zip_code', 20)->nullable()->after('geolocation_coordinates'); // Zip/Postal code
            $table->string('area')->nullable()->after('zip_code'); // Area (could be a neighborhood or region)
            $table->string('gdrp_agreement')->nullable()->after('area'); // GDPR Agreement (Yes/No or a link to acceptance)
       
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
