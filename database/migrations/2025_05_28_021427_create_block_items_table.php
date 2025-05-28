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
        Schema::create('block_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade');

            // Убираем FK, просто оставляем колонку TODO: <-
            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_items');
    }
};
