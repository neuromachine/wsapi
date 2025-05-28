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
        Schema::table('block_items', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('blocks_categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_items', function (Blueprint $table) {
            //
        });
    }
};
