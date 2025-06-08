<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('block_items')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('block_items')->insert([
            ['id' => 1, 'block_id' => 1, 'category_id' => 4, 'key' => 'start', 'name' => 'Старт', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'block_id' => 1, 'category_id' => 4, 'key' => 'premium', 'name' => 'Премиум', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 500, 'block_id' => 2, 'category_id' => 100, 'key' => 'shincenter', 'name' => 'Шинцентр.рф', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 501, 'block_id' => 2, 'category_id' => 100, 'key' => 'iziiti', 'name' => 'Изи-ити', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
