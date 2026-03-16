<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            BlocksMainCategoriesSeeder::class,
            BlocksServicesCategoriesSeeder::class,

            BlockSeeder::class,
            BlockForPagesDataSeeder::class,
            BlockForPortfolioDataSeeder::class,
            BlockItemPropertiesSeeder::class,
//            BlockItemsSeeder::class,//Legacy
            //BlockItemPropertyValuesSeeder::class,//Legacy
//            BlocksForPortfolioPropertyValuesSeeder::class, //Legacy
            ServicesBlockSeeder::class,
            KpBlockSeeder::class,
            BlockItemsForCategoriesDesrDataSeeder::class,
            BlocksForMainSectionsSeeder::class,
        ]);

/*        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
    }
}
