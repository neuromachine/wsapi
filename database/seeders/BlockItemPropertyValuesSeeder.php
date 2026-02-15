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
        DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = [
            [500, 'shincenter', 'Шинцентр.рф'],
            [501, 'rayaray', 'RayaRay'],
            [502, 'sante', 'Шале-сантэ'],
            [503, 'mankor', 'Mankor'],
            [504, 'autobed', 'Авто-Кроватка'],
            [505, 'tff', 'TFF'],
            [506, 'jolie', 'Jolies Boutique'],
            [507, 'mia', 'Миа мебель'],
            [508, 'mvconsalt', 'MvConsalt'],
            [509, 'kkkagarant', 'Kkkagarant'],
            [510, 'barma', 'Barma'],
            [511, 'brutality', 'BrutalityGame'],
        ];

        $rows = [];
        $id = 101;

        foreach ($items as [$itemId, $key, $name]) {
            $rows[] = ['comment' => "// {$itemId} // {$key}, // {$name}"];

            $data = BlockContentHelper::getData($key);

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 20, // title
                'value' => $data['title'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 21, // url
                'value' => $data['url'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 22, // descr
                'value' => $data['descr'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 23, // content
                'value' => $data['content']['head'] . $data['content']['body'] . $data['content']['footer'],
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 24, // thumb
                'value' => 'thumb_'.$key.'.png', 'created_at' => now(), 'updated_at' => now()
            ];

            foreach ($data['image'] as $imgIndex => $imagePath) {
                $rows[] = [
                    'id' => $id++, 'item_id' => $itemId, 'property_id' => 25, // images
                    'value' => $key.'/'.$imagePath, 'created_at' => now(), 'updated_at' => now()
                ];
            }

            if(!empty($data['workclass']) && is_array($data['workclass']))
            {
                foreach ($data['workclass'] as $wClass) {
                    $rows[] = [
                        'id' => $id++, 'item_id' => $itemId, 'property_id' => 26, // workclass
                        'value' => json_encode($wClass), 'created_at' => now(), 'updated_at' => now()
                    ];
                }
            }
            else
            {
                $rows[] = [
                    'id' => $id++, 'item_id' => $itemId, 'property_id' => 26, // workclass (static)
                    'value' => json_encode([['key' => 'develop', 'label' => 'Разработка']]),
                    'created_at' => now(), 'updated_at' => now()
                ];
            }


                /*
            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 26, // workclass (static)
                'value' => json_encode($data['workclass']),
                'created_at' => now(), 'updated_at' => now()
            ];
                */

/*            foreach ($data['files'] as $file) {
                $rows[] = [
                    'id' => $id++, 'item_id' => $itemId, 'property_id' => 27, // files
                    'value' => json_encode($file), 'created_at' => now(), 'updated_at' => now()
                ];
            }*/

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 27, // price
                'value' => $data['price'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 28, // date
                'value' => $data['date'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 29, // workdescr
                'value' => $data['content']['body'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 30, // targets
                'value' => $data['content']['head'], 'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $itemId, 'property_id' => 31, // tech
                'value' => $data['content']['footer'], 'created_at' => now(), 'updated_at' => now()
            ];
        }

        $insert = array_filter($rows, fn($r) => !isset($r['comment']));

        // Дополнительные статические значения (пример для позиций 1 и 2)
        /**/
        $manual = [
            /*
            // Старт (item_id = 1)
            ['id' => 1, 'item_id' => 1, 'property_id' => 1, 'value' => 'Стартовое предложение', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'item_id' => 1, 'property_id' => 2, 'value' => 'https://example.com/start', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'item_id' => 1, 'property_id' => 3, 'value' => '15000', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'item_id' => 1, 'property_id' => 4, 'value' => 'Для быстрого запуска и теста ниши', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'item_id' => 1, 'property_id' => 5, 'value' => '<p>Включает базовые функции и адаптивность.</p>', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'item_id' => 1, 'property_id' => 6, 'value' => 'start/start_1.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'item_id' => 1, 'property_id' => 6, 'value' => 'start/start_2.png', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'item_id' => 1, 'property_id' => 7, 'value' => json_encode([
                ['src' => 'start/kp_landing.pdf', 'title' => 'Коммерческое предложение'],
                ['src' => 'start/tz_landing.pdf', 'title' => 'Техническое задание'],
            ]), 'created_at' => now(), 'updated_at' => now()],

            // Премиум (item_id = 2)
            ['id' => 50, 'item_id' => 2, 'property_id' => 1, 'value' => 'Премиум-решение', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 51, 'item_id' => 2, 'property_id' => 2, 'value' => 'https://example.com/premium', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 52, 'item_id' => 2, 'property_id' => 3, 'value' => '45000', 'created_at' =>now(), 'updated_at' => now()],
            ['id' => 53, 'item_id' => 2, 'property_id' => 4, 'value' => 'Максимум возможностей и глубокой кастомизации', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 54, 'item_id' => 2, 'property_id' => 5, 'value' => '<p>Подходит для зрелого бизнеса с высокими требованиями.</p>', 'created_at' => now(), 'updated_at' => now()],

            */

            ['id' => 10000, 'item_id' => 1000, 'property_id' => 50, 'value' => BlockContentHelper::getData('contacts')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10001, 'item_id' => 1001, 'property_id' => 50, 'value' => BlockContentHelper::getData('about')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10002, 'item_id' => 1002, 'property_id' => 50, 'value' => BlockContentHelper::getData('price')['content']['body'], 'created_at' => now(), 'updated_at' => now()],
        ];

        // Объединяем автоматические и ручные записи
        $insert = array_merge($insert, $manual);


        DB::table('block_item_property_values')->insert($insert);
    }
}
