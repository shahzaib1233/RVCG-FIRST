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
        Schema::table('property_kpis', function (Blueprint $table) {
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('cascade'); // Adding users_id column

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_kpis', function (Blueprint $table) {
            $table->dropColumn('users_id'); // Removing users_id column if rolling back

        });
    }
};
