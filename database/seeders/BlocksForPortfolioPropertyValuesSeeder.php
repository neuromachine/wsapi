<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Support\Facades\Log;

class BlocksForPortfolioPropertyValuesSeeder extends Seeder
{
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rootCategory = DB::table('blocks_categories')
            ->where('key', 'portfolio')
            ->first();
        if (!$rootCategory) {
            Log::error("Root category key not found, abort seeding");
            return;
        }

        $items = DB::table('block_items')->where('category_id', $rootCategory->id)->get(); // TODO: check to empty cat data


        $rows = [];
        $id = 101;

        $locale = 'ru';

        foreach ($items as $item) {

            //if($item->key !== 'rayaray_en') continue;

            $rows[] = ['comment' => "// {$item->id} // {$item->key}, // {$item->name}"];

            $data = BlockContentHelper::getData($item->key);
            if(!empty($data['locale']))
            {
                $locale = $data['locale'];
            }

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 20, // title
                'value' => $data['title'],
                'locale' => $locale,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 21, // url
                'value' => $data['url'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 22, // descr
                'value' => $data['descr'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 23, // content
                'value' => $data['content']['head'] . $data['content']['body'] . $data['content']['footer'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 24, // thumb
                'value' => 'thumb_'.$item->key.'.png',
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            foreach ($data['image'] as $imgIndex => $imagePath) {
                $rows[] = [
                    'id' => $id++, 'item_id' => $item->id, 'property_id' => 25, // images
                    'value' => $item->key.'/'.$imagePath,
                    'locale' => $locale,
                    'created_at' => now(), 'updated_at' => now()
                ];
            }

            if(!empty($data['workclass']) && is_array($data['workclass']))
            {
                foreach ($data['workclass'] as $wClass) {
                    $rows[] = [
                        'id' => $id++, 'item_id' => $item->id, 'property_id' => 26, // workclass
                        'value' => json_encode($wClass),
                        'locale' => $locale,
                        'created_at' => now(), 'updated_at' => now()
                    ];
                }
            }
            else
            {
                $rows[] = [
                    'id' => $id++, 'item_id' => $item->id, 'property_id' => 26, // workclass (static)
                    'value' => json_encode([['key' => 'develop', 'label' => 'Разработка']]),
                    'locale' => $locale,
                    'created_at' => now(), 'updated_at' => now()
                ];
            }

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 27, // price
                'value' => $data['price'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 28, // date
                'value' => $data['date'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 29, // workdescr
                'value' => $data['content']['body'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 30, // targets
                'value' => $data['content']['head'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];

            $rows[] = [
                'id' => $id++, 'item_id' => $item->id, 'property_id' => 31, // tech
                'value' => $data['content']['footer'],
                'locale' => $locale,
                'created_at' => now(), 'updated_at' => now()
            ];
        }

        $insert = array_filter($rows, fn($r) => !isset($r['comment']));


        $defaults = [
            'id'          => null,
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


        DB::table('block_item_property_values')->insert($normalized);
    }
}
