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
        Schema::create('fuel_costs', function (Blueprint $table) {
            $table->id();
            $table->integer('car_id');
            $table->dateTime('date');
            $table->bigInteger('price');
            $table->integer('liter');
            $table->bigInteger('cost');
            $table->bigInteger('mileage')->nullable();
            $table->enum('fuel_type',['Petrol','Disel','92','95','CNG','EVs']);
            $table->string('station_name')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_costs');
    }
};
