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
        Schema::table('leads', function (Blueprint $table) {
            // Remove the old 'source' column
            $table->dropColumn('source');
            
            // Add a new 'lead_source_id' column
            $table->unsignedBigInteger('lead_source_id')->nullable();

            // Add foreign key constraint
            $table->foreign('lead_source_id')->references('id')->on('lead_sources')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_source_id']);
            $table->dropColumn('lead_source_id');
            $table->string('source')->nullable(); // Revert back
        });
    }
};
