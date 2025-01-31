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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Notification type (e.g., reminder, announcement)
            $table->string('title'); // Notification title
            $table->text('message'); // Notification content
            $table->boolean('send_to_all')->default(false); // If the notification is for all users
            $table->timestamp('scheduled_at')->nullable(); // Time to send the notification
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
