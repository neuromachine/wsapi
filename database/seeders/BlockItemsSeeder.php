<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//        DB::table('block_items')->truncate();
        //DB::table('block_items')->delete();


        /*

        $rootCategory = DB::table('blocks_categories')
            ->where('key', 'main')
            ->first();
        if (!$rootCategory) {
            Log::error("Root category key not found, abort seeding");
            return;
        }

        DB::table('block_items')->insert([
            ['id' => 100, 'block_id' => 6, 'category_id' => $rootCategory->id, 'key' => 'hero', 'name' => 'Блок HERO на главной', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 101, 'block_id' => 7, 'category_id' => $rootCategory->id, 'key' => 'section_service', 'name' => 'Блок Услуги на главной', 'created_at' => now(), 'updated_at' => now()],
        ]);
        */



        $rootCategory = DB::table('blocks_categories')
            ->where('key', 'portfolio')
            ->first();
        if (!$rootCategory) {
            Log::error("Root category key not found, abort seeding");
            return;
        }

        DB::table('block_items')->insert([
            //['id' => 500, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'shincenter', 'name' => 'Шинцентр.рф', 'created_at' => now(), 'updated_at' => now()],
            //['id' => 501, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'rayaray', 'name' => 'RayaRay', 'created_at' => now(), 'updated_at' => now()],
            //['id' => 502, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'sante', 'name' => 'Шале-сантэ', 'created_at' => now(), 'updated_at' => now()],
            //['id' => 503, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'mankor', 'name' => 'Mankor', 'created_at' => now(), 'updated_at' => now()],
            //['id' => 504, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'autobed', 'name' => 'Авто-Кроватка', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 505, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'tff', 'name' => 'TFF', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 506, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'jolie', 'name' => 'Jolies Boutique', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 507, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'mia', 'name' => 'Миа мебель', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 508, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'mvconsalt', 'name' => 'MvConsalt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 509, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'kkkagarant', 'name' => 'Kkkagarant', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 510, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'barma', 'name' => 'Barma', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 511, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'brutality', 'name' => 'BrutalityGame', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 512, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'rayaray_en', 'name' => 'RayaRay', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 513, 'block_id' => 2, 'category_id' => $rootCategory->id, 'key' => 'shincenter_en', 'name' => 'Topshina', 'created_at' => now(), 'updated_at' => now()],
        ]);


/*
        $rootCategoryP = DB::table('blocks_categories')
            ->where('key', 'pages')
            ->first();
        if (!$rootCategoryP) {
            Log::error("Root category key not found, abort seeding");
            return;
        }

        DB::table('block_items')->insert([
            ['id' => 1000, 'block_id' => 3, 'category_id' => $rootCategoryP->id, 'key' => 'contacts', 'name' => 'Контакты', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1001, 'block_id' => 3, 'category_id' => $rootCategoryP->id, 'key' => 'about', 'name' => 'О нас', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1002, 'block_id' => 3, 'category_id' => $rootCategoryP->id, 'key' => 'price', 'name' => 'Цены', 'created_at' => now(), 'updated_at' => now()],
        ]);*/
    }
}
