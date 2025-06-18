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
            ['id' => 501, 'block_id' => 2, 'category_id' => 100, 'key' => 'rayaray', 'name' => 'RayaRay', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 502, 'block_id' => 2, 'category_id' => 100, 'key' => 'sante', 'name' => 'Шале-сантэ', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 503, 'block_id' => 2, 'category_id' => 100, 'key' => 'mankor', 'name' => 'Mankor', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 504, 'block_id' => 2, 'category_id' => 100, 'key' => 'autobed', 'name' => 'Авто-Кроватка', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 505, 'block_id' => 2, 'category_id' => 100, 'key' => 'tff', 'name' => 'TFF', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 506, 'block_id' => 2, 'category_id' => 100, 'key' => 'jolie', 'name' => 'Jolies Boutique', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 507, 'block_id' => 2, 'category_id' => 100, 'key' => 'mia', 'name' => 'Миа мебель', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 508, 'block_id' => 2, 'category_id' => 100, 'key' => 'mvconsalt', 'name' => 'MvConsalt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 509, 'block_id' => 2, 'category_id' => 100, 'key' => 'kkkagarant', 'name' => 'Kkkagarant', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 510, 'block_id' => 2, 'category_id' => 100, 'key' => 'barma', 'name' => 'Barma', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 511, 'block_id' => 2, 'category_id' => 100, 'key' => 'brutality', 'name' => 'BrutalityGame', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
