<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Support\Facades\Log;

class ServicesBlockSeeder extends Seeder
{
    public function run(): void
    {
        // В тех. целях — отключаем проверки FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Можно очистить предыдущие данные, если нужно
        // DB::table('block_items')->where('block_id', $blockId)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Начинаем транзакцию
        DB::transaction(function () {
            // Ручной список категорий для обработки
            $categoryKeys = [
                'posadocnaia-stranica',
                // 'druzhba-sosedey', …
            ];

            foreach ($categoryKeys as $catKey) {
                $cat = DB::table('blocks_categories')
                    ->where('key', $catKey)
                    ->first();

                if (!$cat) {
                    Log::warning("Категория {$catKey} не найдена — пропускаем");
                    continue;
                }

                // Получаем данные из JSON
                $data = BlockContentHelper::getCategoryItemsData($catKey);
                $items = $data['items'] ?? [];

                foreach ($items as $itemDef) {
                    // Вставляем или обновляем позицию
                    $itemId = DB::table('block_items')
                        ->updateOrInsert(
                            ['block_id' => $cat->block_id, 'category_id' => $cat->id, 'key' => $itemDef['key']],
                            [
                                'name'       => $itemDef['name'],
                                'slug'       => $itemDef['key'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        ) ?
                        DB::table('block_items')->where('block_id', $cat->block_id)->where('key', $itemDef['key'])->value('id')
                        : null;

                    if (!$itemId) {
                        Log::error("Не удалось вставить item {$itemDef['key']} в категорию {$catKey}");
                        continue;
                    }

                    // Свойства
                    $props = $itemDef['properties'] ?? [];
                    foreach ($props as $propKey => $propValue) {
                        // Найдём property_id заранее заведённого свойства
                        $propertyId = DB::table('block_item_properties')
                            ->where('block_id', $cat->block_id)
                            ->where('key', $propKey)
                            ->value('id');

                        if (!$propertyId) {
                            Log::warning("Свойство {$propKey} не найдено для блока {$cat->block_id}");
                            continue;
                        }

                        // Вставка или обновление значения
                        DB::table('block_item_property_values')
                            ->updateOrInsert(
                                ['item_id' => $itemId, 'property_id' => $propertyId],
                                ['value' => is_array($propValue) ? json_encode($propValue) : $propValue,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                    }
                }
            }
        });
    }
}
