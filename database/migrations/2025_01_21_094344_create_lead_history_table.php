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
        Schema::create('lead_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id'); // FK to leads table
            $table->date('contact_date');
            $table->text('note');
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['contacted', 'no_response', 'interested', 'rejected'])->default('contacted');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_history');
    }
};
