<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForPagesDataSeeder extends Seeder
{
    public function run(): void
    {
        define("BLOCK", array(
            'id' => 3,
            'key' => 'pages',
            'name' => 'Страницы',
            'description' => 'Структурные элементы - страницы и т.п.',
        ));
        define("SECTIONS", ['ru','en','vi']);
        define("CATEGORY_KEY", 'pages');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            // Create block
            $blockId = ImportHelper::upsertBlock(BLOCK['key'], BLOCK['name'], BLOCK['description']);
            $this->command->info("Block with {$blockId} was created or updated");

            // Create properties
            $block_properties = [
                ['id' => 50, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html'],
                ['id' => 51, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string'],
                ['id' => 52, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text'],
                ['id' => 53, 'key' => 'metadata', 'name' => 'Json с массивом используемым в целях продвижения', 'type' => 'json'],
                ['id' => 54, 'key' => 'priority', 'name' => 'Приоритетя', 'type' => 'number'],
            ];

            foreach ($block_properties as $property) {
                $propId = ImportHelper::upsertProperty($blockId, $property['key'], $property['name'], $property['type'], false, $property['id']);
                $this->command->info("Property {$propId} with key: {$property['key']} was created or updated");
            }

            $categoryId = ImportHelper::getCategoryId(CATEGORY_KEY);
            if (!$categoryId) {
                $message = "Category not found, abort seeding";
                Log::warning($message);
                $this->command->error($message);
                return;
            }

            $itemsKeys = BlockContentHelper::getBlockKeys('blocks/items/'.CATEGORY_KEY, 'pages');

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file with Item data: {$itemKey}, will try to write");
                $data = BlockContentHelper::getBlockContent('blocks/items/'.CATEGORY_KEY, $itemKey,'pages');

                if (empty($data['name']) || empty($data['key']) || empty($data['block']) || empty($data['properties']) || !is_array($data['properties'])) {
                    $message = "Item with key {$itemKey}, do not have full data - continue";
                    Log::warning($message);
                    $this->command->error($message);
                    continue;
                }

                $itemId = ImportHelper::upsertItem($blockId, $categoryId, $data['key'], $data['name']);

                foreach (SECTIONS as $section) {
                    $props = ($section !== 'ru') ? ($data[$section]['properties'] ?? []) : ($data['properties'] ?? []);

                    foreach ($props as $propKey => $propValue) {
                        $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                        if (!$propertyId) {
                            $message = "Свойство {$propKey} не найдено для блока ID={$blockId} - пропускаем создание данного значений для позиции";
                            $this->command->error($message);
                            Log::warning($message);
                            continue;
                        }

                        ImportHelper::upsertPropertyValue($itemId, $propertyId, $section, $propValue);
                    }
                }
            }
        });
    }
}
