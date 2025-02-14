<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
            Schema::create('skiptrace', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('owner_name');
                $table->string('owner_contact');
                $table->string('owner_email');
                $table->boolean('is_paid')->default(false);
                $table->timestamps();
            });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skiptrace');
    }
};
