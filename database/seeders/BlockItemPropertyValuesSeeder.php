<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockItemPropertyValuesSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем таблицу значений
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Настройка позиций (item_id, key, name)
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

        // Свойства блока (property_id => генератор значения)
        $generators = [
            20 => fn($k) => "{$k}", // title
            21 => fn($k) => "https://{$k}.example.com", // url
            22 => fn($k) => "Краткое описание для {$k}", // descr
            23 => fn($k) => "<p>Полноценное описание для {$k}.</p>", // content
            24 => fn($k) => "thumb_{$k}.png", // thumb
            25 => fn($k) => [
                "{$k}/desktop_main.png",
                "{$k}/desktop_1.png",
                "{$k}/desktop_2.png",
            ], // images
            26 => fn($k) => json_encode([['key' => 'develop', 'label' => 'Разработка']]), // workclass
        ];

        $rows = [];
        $id = 101;

        foreach ($items as [$itemId, $key, $name]) {
            // комментарий для визуализации
            $rows[] = ['comment' => "// {$itemId} // {$key}, // {$name}"];

            foreach ($generators as $pid => $gen) {
                $value = $gen($key);
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $rows[] = [
                            'id' => $id++,
                            'item_id' => $itemId,
                            'property_id' => $pid,
                            'value' => $val,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } else {
                    $rows[] = [
                        'id' => $id++,
                        'item_id' => $itemId,
                        'property_id' => $pid,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Фильтруем комментарии и формируем для вставки
        $insert = array_filter($rows, fn($r) => !isset($r['comment']));

// Дополнительные статические значения (пример для позиций 1 и 2)
        $manual = [
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
            ['id' => 52, 'item_id' => 2, 'property_id' => 3, 'value' => '45000', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 53, 'item_id' => 2, 'property_id' => 4, 'value' => 'Максимум возможностей и глубокой кастомизации', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 54, 'item_id' => 2, 'property_id' => 5, 'value' => '<p>Подходит для зрелого бизнеса с высокими требованиями.</p>', 'created_at' => now(), 'updated_at' => now()],
        ];

// Объединяем автоматические и ручные записи
        $insert = array_merge($insert, $manual);

        DB::table('block_item_property_values')->insert($insert);
    }
}
