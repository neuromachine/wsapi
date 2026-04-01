<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForPagesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: заменить на поступающие из ? источника
        define("BLOCK", array(
            'id' => 3,
            'key' => 'pages',
            'name' => 'Страницы',
            'description' => 'Структурные элементы - страницы и т.п.',
        ));
        define("SECTIONS", ['ru','en','vi']); // TODO: take scopes from config
        define("CATEGORY_KEY", 'pages');


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::transaction(function () {
            // TODO: заменить на поступающий из json
            DB::table('blocks')
                ->updateOrInsert(
                    [
                        'id' => BLOCK['id'],
                        'key' => BLOCK['key'],
                    ],
                    [
                        'name' => BLOCK['name'],
                        'description' => BLOCK['description'],
                    ]
                );

            $block = DB::table('blocks')
                ->where('key', BLOCK['key'])
                ->first();
            if (!$block) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $this->command->info("Block with {$block->id} was created or updated");

            // TODO: заменить на сбор всех возможных вариантов из json
            // TODO: заменить принцип назначения идентификатора
            $block_properties = [
                ['id' => 50, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html'],
                ['id' => 51, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string'],
                ['id' => 52, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text'],
                ['id' => 53, 'key' => 'metadata', 'name' => 'Json с массивом используемым в целях продвижения', 'type' => 'json'],
                ['id' => 54, 'key' => 'priority', 'name' => 'Приоритетя', 'type' => 'number'],
            ];

            foreach ($block_properties as $key => $property) {

                DB::table('block_item_properties')
                    ->updateOrInsert(
                        [
                            'id' => $property['id'],
                            'block_id' => $block->id,
                        ],
                        [
                            'key' => $property['key'],
                            'name' => $property['name'],
                            'type' => $property['type'],
                            // TODO: is collection
                        ]
                    );

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

            // TODO: создание или обновление категории на основе данных поступающих из json
            // TODO: один из возможных функционалов - обращаться к созданию и забору данных о категории из бд, уже при создании позиций
            $category = DB::table('blocks_categories')
                ->where('key', CATEGORY_KEY)
                ->first();
            if (!$category) {
                $message = "Category not found, abort seeding";
                Log::warning($message);
                $this->command->error($message);
                return;
            }

            $itemsKeys = BlockContentHelper::getBlockKeys('blocks/items/'.$category->key, 'pages');

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file wiith Item data: {$itemKey}, will try to write");

                $data = BlockContentHelper::getBlockContent('blocks/items/'.$category->key, $itemKey,'pages');

                if(empty($data['name']) || empty($data['key']) || empty($data['block']) || empty($data['properties']) || !is_array($data['properties']))
                {
                    $message = "Item with key {$itemKey}, do not have fill data - continue";
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
                            'category_id' => $category->id,
                            'key'         => $data['key'],
                        ],
                        [
                            'name'       => $data['name'],
                            'key'       => $data['key'],
                            // TODO: description field
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $itemId = DB::table('block_items')
                    ->where('block_id', $block->id)
                    ->where('category_id', $category->id)
                    ->where('key', $data['key'])
                    ->value('id');


                if (!$itemId) {
                    $message = "Не удалось получить ID item {$data['key']} в категории {$category->key} - пропускаем создание свойств, продолжаем создание позиций";
                    $this->command->error($message);
                    Log::error($message);
                    continue;
                }

                foreach (SECTIONS as $section) {

                    // TODO: change
                    if($section !== 'ru')
                    {
                        $props = $data[$section]['properties'] ?? [];
                    }
                    else
                    {
                        $props = $data['properties'] ?? [];
                    }


                    foreach ($props as $propKey => $propValue) {

                        $propertyId = DB::table('block_item_properties')
                            ->where('block_id', $block->id)
                            ->where('key', $propKey)
                            ->value('id');

                        if (!$propertyId) {
                            $message = "Свойство {$propKey} не найдено для блока ID={$block->id} - пропускаем создание данного значений для позиции";
                            $this->command->error($message);
                            Log::warning($message);
                            continue;
                        }

                        $valueType = is_array($propValue) ? 'json' : 'string';

                        DB::table('block_item_property_values')
                            ->updateOrInsert(
                                ['item_id'     => $itemId, 'property_id' => $propertyId,'locale' => $section],
                                [
                                    'value'      => is_array($propValue) ? json_encode($propValue) : $propValue,
                                    'value_type'      => $valueType,
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
