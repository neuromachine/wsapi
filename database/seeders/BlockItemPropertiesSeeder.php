<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockItemPropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('block_item_properties')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('block_item_properties')->insert([
            ['id' => 1, 'block_id' => 1, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'block_id' => 1, 'key' => 'url', 'name' => 'Ссылка', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'block_id' => 1, 'key' => 'price', 'name' => 'Цена', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'block_id' => 1, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'block_id' => 1, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'block_id' => 2, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'block_id' => 2, 'key' => 'url', 'name' => 'Ссылка', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'block_id' => 2, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'block_id' => 2, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'block_id' => 2, 'key' => 'type', 'name' => 'Тип работ', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'block_id' => 2, 'key' => 'thumb', 'name' => 'Миниатюра', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'block_id' => 2, 'key' => 'image', 'name' => 'Изображение', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
