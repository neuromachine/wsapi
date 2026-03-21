<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlocksCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {

            $entityKeys = BlockContentHelper::getBlockKeys('blocks/categories/', 'block');

            foreach ($entityKeys->toArray() as $entityKey) {

                $this->command->info("Find file category with key: {$entityKey}, will try to write");

                $data = BlockContentHelper::getBlockContent('blocks/categories/', $entityKey,'block');

                if(empty($data['key']) || empty($data['ownerkey']) || empty($data['name']) )
                {
                    $message = "Category with key {$entityKey}, do not have full data - continue";
                    Log::warning($message);
                    $this->command->error($message);
                    continue;
                }

                // TODO: проверять и нормализовать данные поступающие из json


                $parentDb = DB::table('blocks_categories')
                    ->where('key', $data['ownerkey'])
                    ->first();

                if (!$parentDb->id) {
                    $message = "Не удалось получить родителя категории с ключем {$data['ownerkey']}, пропускаем, продолжаем создание категорий";
                    $this->command->error($message);
                    Log::error($message);
                    continue;
                }

                DB::table('blocks_categories')
                    ->updateOrInsert(
                        [
                            'key'         => $entityKey,
                        ],
                        [

                            'name'       => $data['name'],
                            'description'       => $data['descr'] ? $data['descr'] : '',
                            'content'       => $data['content'] ? $data['content'] : '',
                            'parent_id' => $parentDb->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                $entityDb = DB::table('blocks_categories')
                    ->where('key', $entityKey)
                    ->first();


                if (!$entityDb->id) {
                    $message = "Не удалось получить ID item {$entityKey}, пропускаем,  продолжаем создание категорий";
                    $this->command->error($message);
                    Log::error($message);
                    continue;
                }
            }


        });
    }
}
