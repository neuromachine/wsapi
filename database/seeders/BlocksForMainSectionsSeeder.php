<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlocksForMainSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {

            $sections = ['ru','en'];
            $categoryKey = 'main';

            $cat = DB::table('blocks_categories')
                ->where('key', $categoryKey)
                ->first();
            if (!$cat) {
                Log::warning("Category not found, abort seeding");
                return;
            }

            $keys = BlockContentHelper::getBlockKeys($cat->key);

            foreach ($keys as $key) {

                $data = BlockContentHelper::getBlockContent($cat->key, $key);

                if(empty($data['name']) || empty($data['key']) || empty($data['block'])) continue;

                $block = DB::table('blocks')
                    ->where('key', $data['block'])
                    ->first();

                if (!$block) {
                    Log::error("Block with key not found, abort seeding");
                    return;
                }


                DB::table('block_items')
                    ->updateOrInsert(
                        [
                            'block_id'    => $block->id,
                            'category_id' => $cat->id,
                            'key'         => $data['key'],
                        ],
                        [
                            'name'       => $data['name'],
                            'key'       => $data['key'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $itemId = DB::table('block_items')
                    ->where('block_id', $block->id)
                    ->where('category_id', $cat->id)
                    ->where('key', $data['key'])
                    ->value('id');

                if (!$itemId) {
                    Log::error("Не удалось получить ID item {$data['key']} в категории {$cat->key}");
                    continue;
                }

                foreach ($sections as $section) {

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
                            Log::warning("Свойство {$propKey} не найдено для блока ID={$block->id}");
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
