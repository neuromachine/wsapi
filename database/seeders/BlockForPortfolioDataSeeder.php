<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForPortfolioDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {




        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::transaction(function () {
            // TODO: заменить на поступающие из ? источника
            $settings['block'] = array(
                'id' => 2,
                'key' => 'works',
                'name' => 'Работы',
                'description' => 'Выполненные работы',
            );
            $settings['sections'] = ['ru','en'];
            $settings['category_key'] = 'portfolio';

            // TODO: заменить на поступающий из json
            DB::table('blocks')
                ->updateOrInsert(
                    [
                        'id' => $settings['block']['id'],
                        'key' => $settings['block']['key'],
                    ],
                    [
                        'name' => $settings['block']['name'],
                        'description' => $settings['block']['description'],
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

            // TODO: заменить на сбор всех возможных вариантов из json
            // TODO: заменить принцип назначения идентификатора
            $block_properties = [
                ['id' => 20, 'block_id' => 2, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 21, 'block_id' => 2, 'key' => 'url', 'name' => 'Ссылка', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 22, 'block_id' => 2, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 23, 'block_id' => 2, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 24, 'block_id' => 2, 'key' => 'thumb', 'name' => 'Миниатюра', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                // Коллекция
                ['id' => 25, 'block_id' => 2, 'key' => 'image', 'name' => 'Изображение2', 'type' => 'string', 'is_collection' => 1, 'created_at' => now(), 'updated_at' => now()],

                ['id' => 26, 'block_id' => 2, 'key' => 'workclass', 'name' => 'Класс работ', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 27, 'block_id' => 2, 'key' => 'price', 'name' => 'Цена', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 28, 'block_id' => 2, 'key' => 'date', 'name' => 'Дата', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 29, 'block_id' => 2, 'key' => 'workdescr', 'name' => 'Описание работы', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 30, 'block_id' => 2, 'key' => 'targets', 'name' => 'Задачи', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 31, 'block_id' => 2, 'key' => 'tech', 'name' => 'Тех.', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ];

            foreach ($block_properties as $property) {

                DB::table('block_item_properties')
                    ->updateOrInsert(
                        [
                            'id' => $property['id'],
                            'block_id' => $block->id,
                        ],
                        [
                            'key' => $property['key'],
                            'name' => $property['name'],
                            'is_collection' => !empty($property['is_collection']) && $property['is_collection'] === 1 ? 1 : 0,
                            'type' => $property['type'],
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
                ->where('key', $settings['category_key'])
                ->first();
            if (!$category) {
                $message = "Category not found, abort seeding";
                Log::warning($message);
                $this->command->error($message);
                return;
            }

            $itemsKeys = BlockContentHelper::getBlockKeys($category->key);

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file wiith Item data: {$itemKey}, will try to write");

                $data = BlockContentHelper::getBlockContent($category->key, $itemKey);

                if(empty($data['name']) || empty($data['key']) || empty($data['block']))
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
                            'description'       => '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $item = DB::table('block_items')
                    ->where('block_id', $block->id)
                    ->where('category_id', $category->id)
                    ->where('key', $data['key'])
                    ->first();


                if (!$item->id) {
                    $message = "Не удалось получить ID item {$data['key']} в категории {$category->key} - пропускаем создание свойств, продолжаем создание позиций";
                    $this->command->error($message);
                    Log::error($message);
                    continue;
                }

                foreach ($settings['sections'] as $section) {

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
                            $message = "Свойство {$propKey} не найдено для блока ID={$block->id} - пропускаем создание данного значения для позиции";
                            $this->command->error($message);
                            Log::warning($message);
                            continue;
                        }

                        $valueType = is_array($propValue) ? 'json' : 'string';

                        // TODO: refactor - make for images
                        if(is_array($propValue) && $propKey === 'image')
                        {

                            // TODO: если есть - обновлять, если нет - создавать
                            /*
                            $pValueDB = DB::table('block_item_property_values')
                                ->where('item_id', $item->id)
                                ->where('property_id', $propertyId)
                                ->where('locale', $section)
                                ->first();
                            */

                            $rows = [];
                            foreach ($propValue as $imgIndex => $imagePath) {

                                /*
                                if($pValueDB->id)
                                {
                                    DB::table('block_item_property_values')
                                        ->updateOrInsert(
                                            ['item_id'     => $item->id, 'property_id' => $propertyId,'locale' => $section],
                                            [
                                                'value'      => $item->key.'/'.$propValueValue,
                                                'value_type'      => $valueType,
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ]
                                        );
                                }
                                */

                                $rows[] = [
                                    'item_id' => $item->id, 'property_id' => $propertyId, // images
                                    'value' => $imagePath,
                                    'locale' => $section,
                                    'created_at' => now(), 'updated_at' => now()
                                ];
                            }

                            $insert = array_filter($rows, fn($r) => !isset($r['comment']));

                            $defaults = [
                                'item_id'     => null,
                                'property_id' => null,
                                'value'       => null,
                                'value_type'  => 'string',
                                'locale'      => 'ru',
                                'version'     => null,
                                'created_at'  => null,
                                'updated_at'  => null,
                            ];

                            $normalized = array_map(function ($row) use ($defaults) {
                                return array_merge($defaults, $row);
                            }, $insert);
                            //dd($normalized);


                            DB::table('block_item_property_values')->insert($normalized);

                        }
                        else
                        {
                            DB::table('block_item_property_values')
                                ->updateOrInsert(
                                    ['item_id'     => $item->id, 'property_id' => $propertyId,'locale' => $section],
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

            }


        });
    }
}
