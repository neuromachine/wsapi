<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlocksMainCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('blocks_categories')->delete();
//        DB::table('blocks_categories')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::transaction(function () {
            $catJsonData = BlockContentHelper::getEntityData('categories');
            foreach ($catJsonData as $entity) {
                DB::table('blocks_categories')->insert(
                    [ 'key' => $entity['slug'], 'name' => $entity['title']]
                );
            }
        });
    }
}
