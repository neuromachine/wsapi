<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('blocks')->delete();
        DB::table('block_items')->delete();
        DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('blocks')->insert([
            [
                'id' => 1,
                'key' => 'offers',
                'name' => 'Предложения',
                'description' => 'Готовые варианты для быстрого запуска',
                //'blocks_category_id' => 1, // или null, если не используется
                'created_at' => now(),
                'updated_at' => now(),
            ],
/*            [
                'id' => 2,
                'key' => 'works',
                'name' => 'Работы',
                'description' => 'Выполненные работы',
                'created_at' => now(),
                'updated_at' => now(),
            ],*/
            [
                'id' => 4,
                'key' => 'ind_offers',
                'name' => 'Коммерческие предложения',
                'description' => 'КП для разных секторов',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'key' => 'descr_data',
                'name' => 'Описательные данные',
                'description' => 'Непосредственное наполнение страниц - контент области, мета и т.п',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'key' => 'slide',
                'name' => 'Слайд',
                'description' => 'Набор для организации слайд шоу',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'key' => 'list',
                'name' => 'Список',
                'description' => 'Список заголовок и набор json для вывода позиций',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'key' => 'simplehtml',
                'name' => 'Простой html',
                'description' => 'минимальный блок содержащий любой html',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
