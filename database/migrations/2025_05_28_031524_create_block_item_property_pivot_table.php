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
        Schema::create('block_item_property_pivot', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('property_id');

            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('block_items')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('block_item_properties')->onDelete('cascade');

            $table->unique(['item_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_item_property_pivot');
    }
};
