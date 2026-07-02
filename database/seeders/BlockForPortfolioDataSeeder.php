<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForPortfolioDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            $settings['block'] = array(
                'id' => 2,
                'key' => 'works',
                'name' => 'Работы',
                'description' => 'Выполненные работы',
            );
            $settings['sections'] = ['ru','en','vi'];
            $settings['category_key'] = 'portfolio';

            // Upsert block manually to keep ID
            DB::table('blocks')->updateOrInsert(
                [
                    'id' => $settings['block']['id'],
                    'key' => $settings['block']['key'],
                ],
                [
                    'name' => $settings['block']['name'],
                    'description' => $settings['block']['description'],
                ]
            );

            $blockId = ImportHelper::getBlockId($settings['block']['key']);
            if (!$blockId) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $this->command->info("Block with {$blockId} was created or updated");

            $block_properties = [
                ['id' => 20, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string'],
                ['id' => 21, 'key' => 'url', 'name' => 'Ссылка', 'type' => 'string'],
                ['id' => 22, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text'],
                ['id' => 23, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html'],
                ['id' => 24, 'key' => 'thumb', 'name' => 'Миниатюра', 'type' => 'string'],
                ['id' => 25, 'key' => 'image', 'name' => 'Изображение2', 'type' => 'string', 'is_collection' => 1],
                ['id' => 26, 'key' => 'workclass', 'name' => 'Класс работ', 'type' => 'json'],
                ['id' => 27, 'key' => 'price', 'name' => 'Цена', 'type' => 'number'],
                ['id' => 28, 'key' => 'date', 'name' => 'Дата', 'type' => 'string'],
                ['id' => 29, 'key' => 'workdescr', 'name' => 'Описание работы', 'type' => 'html'],
                ['id' => 30, 'key' => 'targets', 'name' => 'Задачи', 'type' => 'html'],
                ['id' => 31, 'key' => 'tech', 'name' => 'Тех.', 'type' => 'html'],
                ['id' => 32, 'key' => 'priority', 'name' => 'Приоритет', 'type' => 'number'],
            ];

            foreach ($block_properties as $property) {
                $isCollection = !empty($property['is_collection']) && $property['is_collection'] === 1;
                $propId = ImportHelper::upsertProperty($blockId, $property['key'], $property['name'], $property['type'], $isCollection, $property['id']);
                $this->command->info("Property {$propId} with key: {$property['key']} was created or updated");
            }

            $categoryId = ImportHelper::getCategoryId($settings['category_key']);
            if (!$categoryId) {
                $message = "Category not found, abort seeding";
                Log::warning($message);
                $this->command->error($message);
                return;
            }

            $itemsKeys = BlockContentHelper::getBlockKeys($settings['category_key']);

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file with Item data: {$itemKey}, will try to write");
                $data = BlockContentHelper::getBlockContent($settings['category_key'], $itemKey);

                if (empty($data['name']) || empty($data['key']) || empty($data['block'])) {
                    $message = "Item with key {$itemKey}, do not have full data - continue";
                    Log::warning($message);
                    $this->command->error($message);
                    continue;
                }

                $itemId = ImportHelper::upsertItem($blockId, $categoryId, $data['key'], $data['name']);

                foreach ($settings['sections'] as $section) {
                    $props = ($section !== 'ru') ? ($data[$section]['properties'] ?? []) : ($data['properties'] ?? []);

                    foreach ($props as $propKey => $propValue) {
                        $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                        if (!$propertyId) {
                            $message = "Свойство {$propKey} не найдено для блока ID={$blockId} - пропускаем создание данного значения для позиции";
                            $this->command->error($message);
                            Log::warning($message);
                            continue;
                        }

                        if (is_array($propValue) && $propKey === 'image') {
                            $rows = [];
                            foreach ($propValue as $imagePath) {
                                $rows[] = [
                                    'item_id'     => $itemId,
                                    'property_id' => $propertyId,
                                    'value'       => $imagePath,
                                    'value_type'  => 'string',
                                    'locale'      => $section,
                                    'created_at'  => now(),
                                    'updated_at'  => now()
                                ];
                            }
                            if (!empty($rows)) {
                                DB::table('block_item_property_values')->insert($rows);
                            }
                        } else {
                            ImportHelper::upsertPropertyValue($itemId, $propertyId, $section, $propValue);
                        }
                    }
                }
            }
        });
    }
}
