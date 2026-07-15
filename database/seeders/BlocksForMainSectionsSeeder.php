<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlocksForMainSectionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            $sections = ['ru','en','vi'];
            $categoryKey = 'main';

            $categoryId = ImportHelper::getCategoryId($categoryKey);
            if (!$categoryId) {
                Log::warning("Category not found, abort seeding");
                return;
            }

            $keys = BlockContentHelper::getBlockKeys($categoryKey);

            foreach ($keys as $key) {
                $data = BlockContentHelper::getBlockContent($categoryKey, $key);

                if (empty($data['name']) || empty($data['key']) || empty($data['block'])) continue;

                $blockId = ImportHelper::getBlockId($data['block']);
                if (!$blockId) {
                    Log::error("Block with key not found, abort seeding");
                    return;
                }

                $itemId = ImportHelper::upsertItem($blockId, $categoryId, $data['key'], $data['name']);

                foreach ($sections as $section) {
                    $props = ($section !== 'ru') ? ($data[$section]['properties'] ?? []) : ($data['properties'] ?? []);

                    foreach ($props as $propKey => $propValue) {
                        $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                        if (!$propertyId) {
                            Log::warning("Свойство {$propKey} не найдено для блока ID={$blockId}");
                            continue;
                        }

                        ImportHelper::upsertPropertyValue($itemId, $propertyId, $section, $propValue);
                    }
                }
            }
        });
    }
}
