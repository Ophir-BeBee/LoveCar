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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->constrained()->references('id')->on('users')->cascadeOnDelete();
            $table->integer('brand_id')->constrained()->references('id')->on('car_brands')->cascadeOnDelete();
            $table->integer('model_id')->constrained()->references('id')->on('car_models')->cascadeOnDelete();
            $table->string('mileage')->nullable();
            $table->enum('usage', ['Taxi','Private','Company']);
            $table->string('plate_no');
            $table->enum('fuel_type',['Petrol','Disel','92','95','CNG','EVs']);
            $table->string('color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
