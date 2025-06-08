<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlocksCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('blocks_categories')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('blocks_categories')->insert([
            [ 'id' => 1, 'key' => 'root', 'name' => 'Категории', 'description' => 'Корневая категория для всех блоков', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 2, 'key' => 'development', 'name' => 'Разработка', 'description' => 'Категории решений для веб-разработки', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 3, 'key' => 'development_basic', 'name' => 'Базовые решения', 'description' => 'Типовые форматы сайтов для бизнеса', 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 4, 'key' => 'development_basic_landing', 'name' => 'Посадочная страница', 'description' => 'Одностраничный сайт для привлечения клиентов с конкретным предложением или акцией.', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 5, 'key' => 'development_basic_corporate', 'name' => 'Корпоративный сайт', 'description' => 'Многостраничный ресурс, представляющий компанию, её услуги и преимущества.', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 6, 'key' => 'development_basic_catalog', 'name' => 'Интернет-каталог', 'description' => 'Структурированная презентация товаров или услуг компании с детальными описаниями.', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 7, 'key' => 'development_basic_store', 'name' => 'Интернет-магазин', 'description' => 'Полноценная платформа для онлайн-продаж с каталогом, корзиной и оформлением заказов.', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 8, 'key' => 'development_typical', 'name' => 'Типовые предложения', 'description' => 'Наиболее востребованные решения в готовом виде', 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 9, 'key' => 'development_typical_landing', 'name' => 'Посадочная страница', 'description' => 'Эффективное решение для проведения рекламных кампаний и привлечения целевой аудитории.', 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 10, 'key' => 'development_typical_corporate', 'name' => 'Корпоративный сайт', 'description' => 'Полноценное представительство компании в интернете с информацией о деятельности, услугах и контактах.', 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 11, 'key' => 'development_typical_catalog', 'name' => 'Интернет-каталог', 'description' => 'Удобный способ продемонстрировать ассортимент продукции с подробными характеристиками.', 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 12, 'key' => 'development_typical_store', 'name' => 'Интернет-магазин', 'description' => 'Комплексное решение для онлайн-торговли с функциями заказа, оплаты и отслеживания доставки.', 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 13, 'key' => 'development_integrations', 'name' => 'Интеграции', 'description' => 'Варианты подключения внешних платформ и сервисов к сайту', 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 14, 'key' => 'promotion', 'name' => 'Продвижение', 'description' => 'Категории маркетинговых и рекламных решений', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 15, 'key' => 'promotion_seo', 'name' => 'SEO и органический трафик', 'description' => 'Оптимизация сайта и рост органической видимости в поиске', 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 16, 'key' => 'promotion_ads', 'name' => 'Контекстная реклама', 'description' => 'Привлечение клиентов через платные объявления в поисковых системах и соцсетях', 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 17, 'key' => 'promotion_smm', 'name' => 'SMM и контент', 'description' => 'Развитие присутствия компании в соцсетях, блогах и на медийных площадках', 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now(), ],
            [ 'id' => 100, 'key' => 'portfolio', 'name' => 'Портфолио', 'description' => 'Наши работы', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now(), ]
        ]);
    }
}
