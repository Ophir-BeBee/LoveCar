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
        Schema::create('hide_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->constrained()->references('id')->on('users')->cascadeOnDelete();
            $table->integer('notification_id')->constrained()->references('id')->on('notifications')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hide_notifications');
    }
};
