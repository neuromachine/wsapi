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
            BlockItemPropertiesSeeder::class,
            BlockItemsSeeder::class,
            //BlockItemPropertyValuesSeeder::class,//Legacy
            BlocksForPortfolioPropertyValuesSeeder::class,
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
