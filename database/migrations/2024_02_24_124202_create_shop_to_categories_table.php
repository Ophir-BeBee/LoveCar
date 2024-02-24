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
        Schema::create('shop_to_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->constrained()->references('id')->on('shops')->cascadeOnDelete();
            $table->integer('shop_category_id')->constrained()->references('id')->on('categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_to_categories');
    }
};
