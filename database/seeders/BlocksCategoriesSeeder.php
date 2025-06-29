<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Database\Seeders\Helpers\BlockContentHelper;

class BlocksCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Отключаем FK и очищаем таблицу
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('blocks_categories')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Стартовая корневая категория "Услуги"
        DB::table('blocks_categories')->insert([
            [
                'id'         => 1,
                'key'        => 'services',
                'name'       => 'Услуги',
                'description'=> 'Услуги WS',
                'content'=> '-',
                'parent_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Загружаем JSON с категориями
        $jsonPath = storage_path('app/tree.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        $id = 2;
        $categories = [];
        $usedKeys = ['services'];

        // Функция для генерации уникального ключа
        $makeUniqueKey = function(string $base) use (&$usedKeys) {
            $key = Str::slug($base);
            $original = $key;
            $i = 1;
            while (in_array($key, $usedKeys)) {
                $key = $original . '-' . $i;
                $i++;
            }
            $usedKeys[] = $key;
            return $key;
        };

        // Обработка top-level групп
        foreach ($data as $groupKey => $groupValue) {
            $groupName = preg_replace('/^[\d\.\s]+/', '', $groupKey);
            $groupId = $id++;
            $slug = $makeUniqueKey($groupName);
            $catDirData = BlockContentHelper::getCatData($slug);
            $categories[] = [
                'id'         => $groupId,
                'key'        => $slug,
                'name'       => $groupName,
//                'description'=> $groupValue['description'] ?? 'Раздел «'.$groupName.'»',
                'description'=> $catDirData['descr'],
                'content'=> $catDirData['content'],
                'parent_id'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Обработка подгрупп
            foreach ($groupValue as $subKey => $subItems) {
                if (!is_array($subItems) || !preg_match('/^[\d]+\./', $subKey)) continue;

                $subName = preg_replace('/^[\d\.\s]+/', '', $subKey);
                $subId = $id++;
                $subSlug = $makeUniqueKey($subName);
                $catClassData = BlockContentHelper::getCatData($subSlug);
                $categories[] = [
                    'id'         => $subId,
                    'key'        => $subSlug,
                    'name'       => $subName,
                    'description'=> $catClassData['descr'],
                    'content'=> $catClassData['content'],
                    'parent_id'  => $groupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Обработка листовых элементов внутри subItems
                foreach ($subItems as $leaf) {
                    if (!isset($leaf['название'])) continue;
                    $leafName = $leaf['название'];
                    $leafDesc = $leaf['описание'] ?? 'Описание отсутствует';
                    $leafSlug = $makeUniqueKey($leafName);
                    $catGroupData = BlockContentHelper::getCatData($leafSlug);
                    $categories[] = [
                        'id'         => $id++,
                        'key'        => $leafSlug,
                        'name'       => $leafName,
                        'description'=> $leafDesc,
                        'content'=> $catGroupData['content'],
                        'parent_id'  => $subId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Вставляем все категории
        DB::table('blocks_categories')->insert($categories);

        DB::table('blocks_categories')->insert([
            [ 'id' => 100, 'key' => 'portfolio', 'name' => 'Портфолио', 'description' => 'Наши работы', 'content' => '-', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 200, 'key' => 'pages', 'name' => 'ws-pro.ru', 'description' => 'Карта сайта', 'content' => '<ul><li><a href="/">Главная</a></li></ul>', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), ]
        ]);
    }
}
