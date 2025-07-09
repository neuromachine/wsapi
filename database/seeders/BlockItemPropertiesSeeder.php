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
            ['id' => 3, 'block_id' => 1, 'key' => 'price', 'name' => 'Цена', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'block_id' => 1, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'block_id' => 1, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'block_id' => 1, 'key' => 'image', 'name' => 'Изображение', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'block_id' => 1, 'key' => 'files', 'name' => 'Файлы', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'block_id' => 1, 'key' => 'timeline', 'name' => 'Таймлайн', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'block_id' => 1, 'key' => 'features', 'name' => 'функции', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'block_id' => 1, 'key' => 'icon', 'name' => 'Иконка', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'block_id' => 2, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'block_id' => 2, 'key' => 'url', 'name' => 'Ссылка', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'block_id' => 2, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'block_id' => 2, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'block_id' => 2, 'key' => 'thumb', 'name' => 'Миниатюра', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'block_id' => 2, 'key' => 'image', 'name' => 'Изображение', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'block_id' => 2, 'key' => 'workclass', 'name' => 'Класс работ', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'block_id' => 2, 'key' => 'price', 'name' => 'Цена', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'block_id' => 2, 'key' => 'date', 'name' => 'Дата', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 50, 'block_id' => 3, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 100, 'block_id' => 4, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 101, 'block_id' => 4, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 102, 'block_id' => 4, 'key' => 'acticle', 'name' => 'Статья (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 103, 'block_id' => 4, 'key' => 'items', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
