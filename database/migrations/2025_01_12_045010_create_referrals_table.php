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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // the referred user
            $table->foreignId('referrer_id')->constrained('users'); // the user who referred
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
    
};
