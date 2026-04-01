<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlocksForNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::transaction(function () {

            $settings['block']['key']='navigation';
            $settings['sections'] = ['ru','en','vi']; // TODO: take scopes from config

            $blockJson = BlockContentHelper::getBlockContent('blocks', $settings['block']['key'],'block');

            // TODO: проверить данные из json
            DB::table('blocks')
                ->updateOrInsert(
                    [
                        'key' => $settings['block']['key'],
                    ],
                    [
                        'name' => $blockJson['name'],
                        'description' => $blockJson['description'],
                    ]
                );

            $block = DB::table('blocks')
                ->where('key', $settings['block']['key'])
                ->first();
            if (!$block) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $this->command->info("Block with {$block->id} was created or updated");

            // TODO: заменить на сбор всех возможных вариантов из json, либо хранить в json блока
            $block_properties = [
                ['key' => 'anchor', 'name' => 'Текст ссылки', 'type' => 'string'],
                ['key' => 'link', 'name' => 'Значение ссылки', 'type' => 'string'],
                ['key' => 'sort', 'name' => 'Сортировка - индекс приоритета', 'type' => 'number'],
            ];

            foreach ($block_properties as $property) {

                DB::table('block_item_properties')
                    ->updateOrInsert(
                        [
                            'block_id' => $block->id,
                            'key' => $property['key']
                        ],
                        [
                            'name' => $property['name'],
                            'is_collection' => !empty($property['is_collection']) && $property['is_collection'] === 1 ? 1 : 0, // TODO: check
                            'type' => $property['type'],
                        ]
                    );

                // TODO: возможно - создать справочник свойств
                $dbProperty = DB::table('block_item_properties')
                    ->where('block_id', $block->id)
                    ->where('key', $property['key'])
                    ->first();

                if (!$dbProperty->id) {
                    $message = "Property {$property['key']} not found for Block ID={$block->id}, abort seeding";
                    Log::warning($message);
                    $this->command->error($message);
                    return;
                }
                $this->command->info("Property {$dbProperty->id} with key: {$dbProperty->key} was created or updated");
            }


            $itemsKeys = BlockContentHelper::getBlockKeys('blocks/items/'.$block->key, 'block');

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file wiith Item key: {$itemKey}, will try to write");

                $data = BlockContentHelper::getBlockContent('blocks/items/'.$block->key, $itemKey,'block');

                if(empty($data['name']) || empty($data['scope']) || empty($data['properties']) || !is_array($data['properties']) || empty($data['properties']['anchor']) || empty($data['properties']['link']))
                {
                    $message = "Item with key {$itemKey}, do not have full data - continue";
                    Log::warning($message);
                    $this->command->error($message);
                    continue;
                }

                // TODO: внедрить забор данных блока, в случае если подразумевается использование не статичного блока, а блока указанного в json
                /*
                $block = DB::table('blocks')
                    ->where('key', $data['block'])
                    ->first();
                */

                DB::table('block_items')
                    ->updateOrInsert(
                        [
                            'block_id'    => $block->id,
                            'key'         => $itemKey,
                        ],
                        [
                            'category_id' => null,
                            'name'       => $data['name'],
                            'description'       => '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $item = DB::table('block_items')
                    ->where('block_id', $block->id)
                    ->where('key', $itemKey)
                    ->first();


                if (!$item->id) {
                    $message = "Не удалось получить ID item {$itemKey} в блоке {$block->id} - пропускаем создание свойств, продолжаем создание позиций";
                    $this->command->error($message);
                    Log::error($message);
                    continue;
                }

                foreach ($data['properties'] as $propKey => $propValue) {

                    $propertyId = DB::table('block_item_properties')
                        ->where('block_id', $block->id)
                        ->where('key', $propKey)
                        ->value('id');

                    if (!$propertyId) {
                        $message = "Свойство {$propKey} не найдено для блока ID={$block->id} - пропускаем создание данного значения для позиции";
                        $this->command->error($message);
                        Log::warning($message);
                        continue;
                    }

                    $valueType = is_array($propValue) ? 'json' : 'string';

                    DB::table('block_item_property_values')
                        ->updateOrInsert(
                            ['item_id'     => $item->id, 'property_id' => $propertyId,'locale' => $data['scope']],
                            [
                                'value'      => is_array($propValue) ? json_encode($propValue) : $propValue,
                                'value_type'      => $valueType,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                }

            }


        });
    }
}
