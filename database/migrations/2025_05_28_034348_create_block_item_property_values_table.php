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
        Schema::create('block_item_property_values', function (Blueprint $table) {
            $table->id();

            // Связь со свойством (property)
            $table->foreignId('property_id')
                ->constrained('block_item_properties') // Ссылается на таблицу свойств
                ->onDelete('cascade');

            // Связь с элементом (item)
            $table->foreignId('item_id')
                ->constrained('block_items') // Ссылается на элементы (позиции блока)
                ->onDelete('cascade');

            // Само значение свойства
            $table->longText('value')->nullable();

            // Тип значения (если нужно кастовать, проверять, рендерить и т.п.)
            $table->string('value_type')->default('string'); // string, int, bool, json, file, ref, etc.

            // Локализация значения
            $table->string('locale')->nullable(); // e.g., 'en', 'ru'

            // Версия значения (если потребуется история изменений)
            $table->integer('version')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_item_property_values');
    }
};
