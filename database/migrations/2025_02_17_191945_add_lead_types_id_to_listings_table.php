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
            $table->unsignedBigInteger('lead_types_id')->nullable();

            // Adding the foreign key constraint
            $table->foreign('lead_types_id')->references('id')->on('lead_types')->onDelete('set null');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            //
            $table->dropForeign(['lead_types_id']);
            $table->dropColumn('lead_types_id');
        });
    }
};
