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
            $table->json('referrers')->nullable(); // Store up to 3 referral codes (JSON format)
            $table->json('social_profiles')->nullable(); // Store up to 3 social media links (JSON format)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referrers');
            $table->dropColumn('social_profiles');
        });
    }
};
