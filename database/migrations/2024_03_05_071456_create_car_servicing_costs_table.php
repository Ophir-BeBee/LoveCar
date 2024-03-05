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
        Schema::create('car_servicing_costs', function (Blueprint $table) {
            $table->id();
            $table->integer('car_id');
            $table->dateTime('date');
            $table->string('shop_name');
            $table->string('shop_phone')->nullable();
            $table->string('shop_address')->nullable();
            $table->bigInteger('total_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_servicing_costs');
    }
};
