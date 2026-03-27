<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockItemsForCategoriesDesrDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');



        DB::transaction(function () {

            $block = DB::table('blocks')
                ->where('key', 'descr_data')
                ->first();
            if (!$block) {
                Log::error("Block with key not found, abort seeding");
                return;
            }

            $blockId = $block->id;

            // TODO: заменить на сбор всех возможных вариантов из json
            // TODO: заменить принцип назначения идентификатора
            $block_properties = [
            ['id' => 110, 'block_id' => $block->id, 'key' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 111, 'block_id' => $block->id, 'key' => 'descr', 'name' => 'Краткое описание', 'type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 112, 'block_id' => $block->id, 'key' => 'content', 'name' => 'Контент (HTML)', 'type' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 113, 'block_id' => $block->id, 'key' => 'metadata', 'name' => 'Json с массивом используемым в целях продвижения', 'type' => 'json', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 1000,'block_id' => $block->id, 'key' => 'priority', 'name' => 'Приоритет', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ];

            foreach ($block_properties as $property) {

                DB::table('block_item_properties')
                    ->updateOrInsert(
                        [
                            'id'=>$property['id'],
                            'block_id' => $block->id,
                            'key' => $property['key'],
                        ],
                        [
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

            $categories = DB::table('blocks_categories')->get();

            // TODO: check to empty cat data
            foreach ($categories as $category) {
                DB::table('block_items')
                    ->updateOrInsert(
                        [
                            'block_id'    => $blockId,
                            'category_id' => $category->id,
                            'key'         => $category->key,
                        ],
                        [
                            'name'       => $category->name,
                            'key'       => $category->key,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $itemId = DB::table('block_items')
                    ->where('block_id', $blockId)
                    ->where('key', $category->key)
                    ->value('id');


                $sections = ['ru','en'];

                foreach ($sections as $section) {


                    $data = BlockContentHelper::getBlockContent('blocks/items/'.$block->key, $category->key,'items_descr_data');

                    if(!empty($data))
                    {
                        // TODO: change
                        if($section !== 'ru')
                        {
                            $props = $data[$section]['properties'] ?? [];
                        }
                        else
                        {
                            $props = $data['properties'] ?? [];
                        }

                        // TODO: check data

                        foreach ($props as $propKey => $propValue) {

                            $propertyId = DB::table('block_item_properties')
                                ->where('block_id', $block->id)
                                ->where('key', $propKey)
                                ->value('id');

                            if (!$propertyId) {

                                // TODO: создавать поля автоматически
                                /*
                                dd($propKey,$propValue);

                                DB::table('block_item_properties')->insert(
                                    [
                                        'key'    => ,
                                        'key'    => ,
                                        'key'    => ,
                                    ]
                                );*/

                                $message = "Property {$propKey} not found for Block ID={$block->id}, continue";
                                Log::warning($message);
                                $this->command->error($message);
                            }

                            $valueType = is_array($propValue) ? 'json' : 'string';

//                            dd($itemId,$propertyId,$section,$propValue,$valueType);
                            $this->command->info("Seeding property id: {$propertyId} for item with id: {$itemId}, at section: {$section}");

                            $result = DB::table('block_item_property_values')
                                ->updateOrInsert(
                                    ['item_id'     => $itemId, 'property_id' => $propertyId,'locale' => $section],
                                    [
                                        'value'      => is_array($propValue) ? json_encode($propValue) : $propValue,
                                        'value_type'      => $valueType,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]
                                );

                            //dd($result);

                        }

                        continue;
                    }

                    //dd($category->key);

                    /**/
                    $catJsonData = BlockContentHelper::getCatData($category->key,$section);

                    DB::table('block_item_property_values')
                        ->updateOrInsert(
                            ['item_id'     => $itemId, 'property_id' => 110, 'locale' => $section], // title
                            [
                                'value'      => $category->name,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                    DB::table('block_item_property_values')
                        ->updateOrInsert(
                            ['item_id'     => $itemId, 'property_id' => 111, 'locale' => $section], // descr
                            [
                                'value'      => $catJsonData['descr'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                    DB::table('block_item_property_values')
                        ->updateOrInsert(
                            ['item_id'     => $itemId, 'property_id' => 112, 'locale' => $section], // content
                            [
                                'value'      => $catJsonData['content'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                    DB::table('block_item_property_values')
                        ->updateOrInsert(
                            ['item_id'     => $itemId, 'property_id' => 113, 'locale' => $section], // metadata
                            [
                                'value'      => '{}',
                                'value_type'      => 'json',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                }
            }
        });

    }
}
