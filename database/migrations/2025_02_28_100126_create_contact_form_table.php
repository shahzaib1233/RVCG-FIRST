<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('contact_form', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email');
            $table->enum('help_type', ['Buying', 'Selling', 'Both Buying and Selling']);
            $table->enum('timeline', ['0-3 Months', '3-6 Months', '6-12 Months']);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('contact_form');
    }
};
