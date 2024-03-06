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
        Schema::create('car_services', function (Blueprint $table) {
            $table->id();
            $table->integer('car_servicing_cost_id')->constrained()->references('id')->on('car_servicing_costs')->cascadeOnDelete();
            $table->enum('type',['services','parts','accessories']);
            $table->string('particular');
            $table->enum('condition',['new','used'])->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('price');
            $table->integer('quantity')->default(1);
            $table->bigInteger('amount');
            $table->integer('guarantee_value')->nullable();
            $table->string('guarantee_type')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_services');
    }
};
