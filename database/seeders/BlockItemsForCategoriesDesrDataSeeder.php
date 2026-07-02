<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockItemsForCategoriesDesrDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            $blockId = ImportHelper::getBlockId('descr_data');
            if (!$blockId) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $block_properties = [
                ['id' => 110, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string'],
                ['id' => 111, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text'],
                ['id' => 112, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html'],
                ['id' => 113, 'key' => 'metadata', 'name' => 'Json с массивом используемым в целях продвижения', 'type' => 'json'],
                ['id' => 1000, 'key' => 'priority', 'name' => 'Приоритет', 'type' => 'number'],
            ];

            foreach ($block_properties as $property) {
                $propId = ImportHelper::upsertProperty($blockId, $property['key'], $property['name'], $property['type'], false, $property['id']);
                $this->command->info("Property {$propId} with key: {$property['key']} was created or updated");
            }

            $categories = DB::table('blocks_categories')->get();

            foreach ($categories as $category) {
                $itemId = ImportHelper::upsertItem($blockId, $category->id, $category->key, $category->name);

                $sections = ['ru','en','vi'];

                foreach ($sections as $section) {
                    $data = BlockContentHelper::getBlockContent('blocks/items/descr_data', $category->key,'items_descr_data');

                    if (!empty($data)) {
                        $props = ($section !== 'ru') ? ($data[$section]['properties'] ?? []) : ($data['properties'] ?? []);

                        foreach ($props as $propKey => $propValue) {
                            $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                            if (!$propertyId) {
                                $message = "Property {$propKey} not found for Block ID={$blockId}, continue";
                                Log::warning($message);
                                $this->command->error($message);
                                continue;
                            }

                            $this->command->info("Seeding property id: {$propertyId} for item with id: {$itemId}, at section: {$section}");
                            ImportHelper::upsertPropertyValue($itemId, $propertyId, $section, $propValue);
                        }
                        continue;
                    }

                    $catJsonData = BlockContentHelper::getCatData($category->key, $section);

                    ImportHelper::upsertPropertyValue($itemId, 110, $section, $category->name);
                    ImportHelper::upsertPropertyValue($itemId, 111, $section, $catJsonData['descr']);
                    ImportHelper::upsertPropertyValue($itemId, 112, $section, $catJsonData['content']);
                    ImportHelper::upsertPropertyValue($itemId, 113, $section, "{}");
                }
            }
        });
    }
}
