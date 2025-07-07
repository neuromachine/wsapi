<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Support\Facades\Log;

class ServicesBlockSeeder extends Seeder
{
    public function run(): void
    {
        // Отключаем проверки FK, если нужно очистить существующие данные:
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('block_items')->where('block_id', $blockId)->delete();
        // DB::table('block_item_property_values')->whereIn('item_id', function($q) use ($blockId) {
        //     $q->select('id')->from('block_items')->where('block_id', $blockId);
        // })->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Получаем ID блока «Услуги» (предполагается, что key = 'services')
        $block = DB::table('blocks')
            ->where('key', 'offers')
            ->first();

        if (!$block) {
            Log::error("Block with key 'services' not found, abort seeding ServicesBlockSeeder.");
            return;
        }

        $blockId = $block->id;

        DB::transaction(function () use ($blockId) {
            // Фиксированный список категорий
            $categoryKeys = [
                'posadocnaia-stranica',
                'korporativnyi-sait',
                'internet-katalog',
                'internet-magazin',
                'posadocnaia-stranica-1',
                'korporativnyi-sait-1',
                'internet-katalog-1',
                'internet-magazin-1',
                'marketingovaia-platforma',
                'portal-socialnaia-set',
                'saas-servis-veb-prilozenie',

                'integraciia-s-crm-amo-bitrix24-i-dr',
                'individualnye-backend-reseniia-laravel-nodejs-i-pr',
                'integracii-s-plateznymi-sistemami-api-1s',
                'avtomatizaciia-biznes-processov',

                'proektirovanie-interfeisov',
                'adaptivnyi-dizain',
                'dizain-koncepcii-i-brendbuk',
                'mobilnaia-versiia-pwa',
                // … другие категории
            ];

            foreach ($categoryKeys as $catKey) {
                // Забираем только ID категории
                $cat = DB::table('blocks_categories')
                    ->where('key', $catKey)
                    ->first(['id']);

                if (!$cat) {
                    Log::warning("Категория {$catKey} не найдена — пропускаем");
                    continue;
                }

                $categoryId = $cat->id;

                // Достаём JSON-данные
                $data = BlockContentHelper::getCategoryItemsData($catKey);
                $items = $data['items'] ?? [];

                foreach ($items as $itemDef) {
                    // Вставляем или обновляем запись в block_items
                    DB::table('block_items')
                        ->updateOrInsert(
                            [
                                'block_id'    => $blockId,
                                'category_id' => $categoryId,
                                'key'         => $itemDef['key'],
                            ],
                            [
                                'name'       => $itemDef['name'],
                                'key'       => $itemDef['key'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                    // Получаем ID только что вставленной (или существующей) позиции
                    $itemId = DB::table('block_items')
                        ->where('block_id', $blockId)
                        ->where('key', $itemDef['key'])
                        ->value('id');

                    if (!$itemId) {
                        Log::error("Не удалось получить ID item {$itemDef['key']} в категории {$catKey}");
                        continue;
                    }

                    // Проходим по свойствам позиции
                    $props = $itemDef['properties'] ?? [];
                    foreach ($props as $propKey => $propValue) {
                        $propertyId = DB::table('block_item_properties')
                            ->where('block_id', $blockId)
                            ->where('key', $propKey)
                            ->value('id');

                        if (!$propertyId) {
                            Log::warning("Свойство {$propKey} не найдено для блока ID={$blockId}");
                            continue;
                        }

                        DB::table('block_item_property_values')
                            ->updateOrInsert(
                                ['item_id'     => $itemId, 'property_id' => $propertyId],
                                [
                                    'value'      => is_array($propValue) ? json_encode($propValue) : $propValue,
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
