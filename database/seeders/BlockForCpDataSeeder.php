<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockForCpDataSeeder extends Seeder
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
                'id' => 4,
                'key' => 'ind_offers',
                'name' => 'Коммерческие предложения',
                'description' => 'КП для разных секторов',
            );
            $settings['sections'] = ['ru','en'];
            $settings['category_key'] = 'ind_offers';

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
                ['id' => 100, 'block_id' => 4, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 101, 'block_id' => 4, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 102, 'block_id' => 4, 'key' => 'acticle', 'name' => 'Статья (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 103, 'block_id' => 4, 'key' => 'items', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 104, 'block_id' => 4, 'key' => 'hero', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 105, 'block_id' => 4, 'key' => 'benefits', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 106, 'block_id' => 4, 'key' => 'includes', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 107, 'block_id' => 4, 'key' => 'reelsSystem', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 108, 'block_id' => 4, 'key' => 'extras', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 109, 'block_id' => 4, 'key' => 'important', 'name' => 'Json с массивом позиций', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
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

            $itemsKeys = BlockContentHelper::getBlockKeys('blocks/items/'.$category->key, 'cp');

            foreach ($itemsKeys as $itemKey) {
                $this->command->info("Find file wiith Item data: {$itemKey}, will try to write");

                $data = BlockContentHelper::getBlockContent('blocks/items/'.$category->key, $itemKey,'cp');

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


        });
    }
}
