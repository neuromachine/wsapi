<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Database\Seeders\Helpers\BlockContentHelper;

class BlocksServicesCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //DB::table('blocks_categories')->delete();

        $rootCategory = DB::table('blocks_categories')
            ->where('key', 'services')
            ->first();
        if (!$rootCategory) {
            Log::error("Root category key not found, abort seeding");
            return;
        }

        // Загружаем JSON с категориями
        $jsonPath = storage_path('app/tree.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        $id = 1000;
        $categories = [];
        $usedKeys = ['services']; // TODO: ошибка - есть и другие, ниже дописать забор и сравнение с реально возможными

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
                'description'=> $catDirData['descr'],
                'content'=> $catDirData['content'],
                'parent_id'  => $rootCategory->id,
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

        DB::table('blocks_categories')->insert($categories);

    }
}
