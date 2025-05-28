<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockItemPropertyValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('block_item_property_values')->insert([
                // Старт (item_id = 1)
                ['id' => 1, 'item_id' => 1, 'property_id' => 1, 'value' => 'Стартовое предложение'],
                ['id' => 2, 'item_id' => 1, 'property_id' => 2, 'value' => 'https://example.com/start'],
                ['id' => 3, 'item_id' => 1, 'property_id' => 3, 'value' => '15000'],
                ['id' => 4, 'item_id' => 1, 'property_id' => 4, 'value' => 'Для быстрого запуска и теста ниши'],
                ['id' => 5, 'item_id' => 1, 'property_id' => 5, 'value' => '<p>Включает базовые функции и адаптивность.</p>'],

                // Премиум (item_id = 2)
                ['id' => 6, 'item_id' => 2, 'property_id' => 1, 'value' => 'Премиум-решение'],
                ['id' => 7, 'item_id' => 2, 'property_id' => 2, 'value' => 'https://example.com/premium'],
                ['id' => 8, 'item_id' => 2, 'property_id' => 3, 'value' => '45000'],
                ['id' => 9, 'item_id' => 2, 'property_id' => 4, 'value' => 'Максимум возможностей и глубокой кастомизации'],
                ['id' => 10, 'item_id' => 2, 'property_id' => 5, 'value' => '<p>Подходит для зрелого бизнеса с высокими требованиями.</p>'],
            ]
        );

    }
}
