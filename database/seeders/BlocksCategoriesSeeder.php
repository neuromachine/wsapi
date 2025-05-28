<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class BlocksCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        //DB::table('blocks_categories')->truncate();

        DB::table('blocks_categories')->insert([
            [
                'id' => 1,
                'key' => 'root',
                'name' => 'Нет категории',
                'description' => 'корень - резервный владелец, для всего что не имеет явно назначенного',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Услуги',
                'key' => 'services',
                'description' => null,
                'parent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'key' => 'development',
                'name' => 'Разработка',
                'description' => null,
                'parent_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'key' => 'base',
                'name' => 'Базовые решения',
                'description' => null,
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'key' => 'typical',
                'name' => 'Типивые предложения',
                'description' => null,
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'key' => 'backend',
                'name' => 'Интеграции и Backend-решения',
                'description' => null,
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'key' => 'ui',
                'name' => 'UI/UX и Прототипирование',
                'description' => null,
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'key' => 'landing',
                'name' => 'Посадочная страница',
                'description' => null,
                'parent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

