<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockItemPropertyValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('block_item_property_values')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('block_item_property_values')->insert([
                // Старт (item_id = 1)
                ['id' => 1, 'item_id' => 1, 'property_id' => 1, 'value' => 'Стартовое предложение'],
                ['id' => 2, 'item_id' => 1, 'property_id' => 2, 'value' => 'https://example.com/start'],
                ['id' => 3, 'item_id' => 1, 'property_id' => 3, 'value' => '15000'],
                ['id' => 4, 'item_id' => 1, 'property_id' => 4, 'value' => 'Для быстрого запуска и теста ниши'],
                ['id' => 5, 'item_id' => 1, 'property_id' => 5, 'value' => '<p>Включает базовые функции и адаптивность.</p>'],
                ['id' => 6, 'item_id' => 1, 'property_id' => 6, 'value' => 'start/start_1.png'],
                ['id' => 7, 'item_id' => 1, 'property_id' => 6, 'value' => 'start/start_2.png'],
                ['id' => 8, 'item_id' => 1, 'property_id' => 7, 'value' => json_encode([
                    ['src'   => 'start/kp_landing.pdf', 'title' => 'Коммерческое предложение'],
                    ['src'   => 'start/tz_landing.pdf', 'title' => 'Техническое задание'],
                ])],

                // Премиум (item_id = 2)
                ['id' => 50, 'item_id' => 2, 'property_id' => 1, 'value' => 'Премиум-решение'],
                ['id' => 51, 'item_id' => 2, 'property_id' => 2, 'value' => 'https://example.com/premium'],
                ['id' => 52, 'item_id' => 2, 'property_id' => 3, 'value' => '45000'],
                ['id' => 53, 'item_id' => 2, 'property_id' => 4, 'value' => 'Максимум возможностей и глубокой кастомизации'],
                ['id' => 54, 'item_id' => 2, 'property_id' => 5, 'value' => '<p>Подходит для зрелого бизнеса с высокими требованиями.</p>'],


                // 500
                // shincenter
                // Шинцентр.рф
                ['id' => 101, 'item_id' => 500, 'property_id' => 20, 'value' => 'Магазин шин Шинцентр'],
                ['id' => 102, 'item_id' => 500, 'property_id' => 21, 'value' => 'https://шинцентр.рф'],
                ['id' => 103, 'item_id' => 500, 'property_id' => 22, 'value' => 'Интернет магазин крупнейшего в ЮФО поставщика шин и дисков для бизнеса'],
                ['id' => 104, 'item_id' => 500, 'property_id' => 23, 'value' => '<p>Интернет магазин крупнейшего в ЮФО поставщика шин и дисков для бизнеса</p>'],
                ['id' => 105, 'item_id' => 500, 'property_id' => 24, 'value' => 'thumb_tyres.png'],
                ['id' => 106, 'item_id' => 500, 'property_id' => 25, 'value' => 'tyres/desctop_main.png'],
                ['id' => 107, 'item_id' => 500, 'property_id' => 25, 'value' => 'tyres/desctop_1.png'],
                ['id' => 108, 'item_id' => 500, 'property_id' => 26, 'value' => json_encode([
                    ['key'   => 'develop', 'label' => 'Разработка'],
                ])],

                // 501
                // rayaray
                // RayaRay
                ['id' => 151, 'item_id' => 501, 'property_id' => 20, 'value' => 'RayaRay'],
                ['id' => 152, 'item_id' => 501, 'property_id' => 21, 'value' => 'https://rayaray.store'],
                ['id' => 153, 'item_id' => 501, 'property_id' => 22, 'value' => 'Интернет магазин одежды для девушек'],
                ['id' => 154, 'item_id' => 501, 'property_id' => 23, 'value' => '<p>Интернет магазин ...</p>'],
                ['id' => 155, 'item_id' => 501, 'property_id' => 24, 'value' => 'thumb_raya.png'],
                ['id' => 156, 'item_id' => 501, 'property_id' => 25, 'value' => 'rayaray/desctop_main.png'],
                ['id' => 157, 'item_id' => 501, 'property_id' => 25, 'value' => 'rayaray/desctop_1.png'],
                ['id' => 158, 'item_id' => 501, 'property_id' => 26, 'value' => json_encode([
                    ['key'   => 'develop', 'label' => 'Разработка'],
                ])],
            ]
        );

    }
}
