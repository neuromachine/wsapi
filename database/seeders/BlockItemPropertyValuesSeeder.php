<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Helpers\BlockContentHelper;

class BlockItemPropertyValuesSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем таблицу значений
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $insert = [
            ['id' => 10000, 'item_id' => 1000, 'property_id' => 50, 'value' => BlockContentHelper::getData('contacts')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10001, 'item_id' => 1001, 'property_id' => 50, 'value' => BlockContentHelper::getData('about')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10002, 'item_id' => 1002, 'property_id' => 50, 'value' => BlockContentHelper::getData('price')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
        ];

        $defaults = [
            'id'          => null,
            'item_id'     => null,
            'property_id' => null,
            'value'       => null,
            'value_type'  => 'string',
            'locale'      => 'ru',
            'version'     => null,
            'created_at'  => null,
            'updated_at'  => null,
        ];

        $normalized = array_map(function ($row) use ($defaults) {
            return array_merge($defaults, $row);
        }, $insert);

        DB::table('block_item_property_values')->insert($normalized);
    }
}
