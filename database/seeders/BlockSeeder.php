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
            [
                'id' => 2,
                'key' => 'works',
                'name' => 'Работы',
                'description' => 'Выполненные работы',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
