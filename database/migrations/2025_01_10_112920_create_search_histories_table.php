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
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->decimal('price_min', 10, 2)->nullable();  
            $table->decimal('price_max', 10, 2)->nullable();  
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null'); // Changed to foreign key
            $table->decimal('area_min')->nullable(); 
            $table->decimal('area_max')->nullable(); 
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};
