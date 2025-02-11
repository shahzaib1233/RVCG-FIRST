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
            $table->decimal('repair_cost', 15, 2)->nullable()->after('estimated_roi');
            $table->decimal('wholesale_fee', 15, 2)->nullable()->after('repair_cost');
            $table->decimal('price_per_square_feet', 15, 2)->nullable()->after('wholesale_fee');
     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['repair_cost', 'wholesale_fee', 'price_per_square_feet']);

        });
    }
};
