<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlocksForNavigationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            $settings['block']['key']='navigation';
            $settings['sections'] = ['ru','en','vi'];

            $blockJson = BlockContentHelper::getBlockContent('blocks', $settings['block']['key'],'block');

            $blockId = ImportHelper::upsertBlock($settings['block']['key'], $blockJson['name'], $blockJson['description']);
            if (!$blockId) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $this->command->info("Block with {$blockId} was created or updated");

            $block_properties = [
                ['key' => 'anchor', 'name' => 'Текст ссылки', 'type' => 'string'],
                ['key' => 'link', 'name' => 'Значение ссылки', 'type' => 'string'],
                ['key' => 'sort', 'name' => 'Сортировка - индекс приоритета', 'type' => 'number'],
            ];

            foreach ($block_properties as $property) {
                $propId = ImportHelper::upsertProperty($blockId, $property['key'], $property['name'], $property['type']);
                $this->command->info("Property {$propId} with key: {$property['key']} was created or updated");
            }

            $itemsKeys = BlockContentHelper::getBlockKeys('blocks/items/'.$settings['block']['key'], 'block');

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file with Item key: {$itemKey}, will try to write");
                $data = BlockContentHelper::getBlockContent('blocks/items/'.$settings['block']['key'], $itemKey,'block');

                if (empty($data['name']) || empty($data['scope']) || empty($data['properties']) || !is_array($data['properties']) || empty($data['properties']['anchor']) || empty($data['properties']['link'])) {
                    $message = "Item with key {$itemKey}, do not have full data - continue";
                    Log::warning($message);
                    $this->command->error($message);
                    continue;
                }

                $itemId = ImportHelper::upsertItem($blockId, null, $itemKey, $data['name']);

                foreach ($data['properties'] as $propKey => $propValue) {
                    $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                    if (!$propertyId) {
                        $message = "Свойство {$propKey} не найдено для блока ID={$blockId} - пропускаем создание данного значения для позиции";
                        $this->command->error($message);
                        Log::warning($message);
                        continue;
                    }

                    ImportHelper::upsertPropertyValue($itemId, $propertyId, $data['scope'], $propValue);
                }
            }
        });
    }
}
