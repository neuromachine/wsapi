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
        Schema::create('block_item_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');

            //$table->string('key')->unique();
            $table->string('key');
            $table->string('name');
            $table->string('type'); // Тип данных: string, number, bool, enum и т.д.

            $table->boolean('is_required')->default(false);
            $table->boolean('is_collection')->default(false); // Массив значений?
            $table->boolean('is_unique')->default(false);

            $table->json('meta')->nullable(); // Дополнительная структура или параметры

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_item_properties');
    }
};
