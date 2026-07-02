<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\ImportHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Helpers\BlockContentHelper;
use Illuminate\Support\Facades\Log;

class ServicesBlockSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $blockId = ImportHelper::getBlockId('offers');

        if (!$blockId) {
            Log::error("Block with key 'offers' not found, abort seeding ServicesBlockSeeder.");
            return;
        }

        DB::transaction(function () use ($blockId) {
            $categoryKeys = [
                'posadocnaia-stranica',
                'korporativnyi-sait',
                'internet-katalog',
                'internet-magazin',
                'posadocnaia-stranica-1',
                'korporativnyi-sait-1',
                'internet-katalog-1',
                'internet-magazin-1',
                'marketingovaia-platforma',
                'portal-socialnaia-set',
                'saas-servis-veb-prilozenie',

                'integraciia-s-crm-amo-bitrix24-i-dr',
                'individualnye-backend-reseniia-laravel-nodejs-i-pr',
                'integracii-s-plateznymi-sistemami-api-1s',
                'avtomatizaciia-biznes-processov',

                'proektirovanie-interfeisov',
                'adaptivnyi-dizain',
                'dizain-koncepcii-i-brendbuk',
                'mobilnaia-versiia-pwa',

                'audit-i-semanticeskoe-iadro',
                'vnutrenniaia-i-vnesniaia-optimizaciia',
                'prodvizenie-po-trafiku-i-poziciiam',

                'google-ads-iandeksdirekt',
                'retargeting-i-dinamiceskie-kampanii',
                'mediinaia-i-bannernaia-reklama',

                'smm-instagram-vk-telegram-i-dr',
                'targetirovannaia-reklama',
                'kontent-strategiia-i-vedenie-akkauntov',

                'podpisnye-voronki',
                'avtomatizaciia-rassylok',
                'personalizirovannye-cepocki',

                'sozdanie-utp-i-pozicionirovanie',
                'rabota-s-otzyvami-reputaciei',
                'publikacii-i-stati-v-smi',

                'obnovleniia-i-bekapy',
                'monitoring-dostupnosti',
                'zashhita-i-bezopasnost',

                'analitika-i-ab-testy',
                'optimizaciia-skorosti-i-ux',
                'ulucsenie-konversii',

                'novye-moduli-i-stranicy',
                'integracii-dopolnitelnyx-servisov',
                'migraciia-na-novye-texnologii',

                'seo-teksty-opisaniia-tovarov',
                'napolnenie-blogov-statei',
                'ux-i-texnicnyi-kopiraiting',

                'promo-i-korporativnoe-video',
                'video-dlia-socsetei-reels',
                'animaciia-i-mousn-dizain',

                'predmetnaia-i-reportaznaia-sieemka',
                'obrabotka-i-retus',
                'illiustracii-infografika',

                'analiz-konkurentov',
                'customer-journey-map',
                'planirovanie-voronki-i-kpi',

                'obucenie-sotrudnikov',
                'vorksopy-po-prodvizeniiu',
                'soprovozdenie-startapov',
            ];

            foreach ($categoryKeys as $catKey) {
                $categoryId = ImportHelper::getCategoryId($catKey);

                if (!$categoryId) {
                    Log::warning("Категория {$catKey} не найдена — пропускаем");
                    continue;
                }

                $data = BlockContentHelper::getCategoryItemsData($catKey);
                $items = $data['items'] ?? [];

                foreach ($items as $itemDef) {
                    $itemId = ImportHelper::upsertItem($blockId, $categoryId, $itemDef['key'], $itemDef['name']);

                    $props = $itemDef['properties'] ?? [];
                    foreach ($props as $propKey => $propValue) {
                        $propertyId = ImportHelper::getPropertyId($blockId, $propKey);

                        if (!$propertyId) {
                            Log::warning("Свойство {$propKey} не найдено для блока ID={$blockId}");
                            continue;
                        }

                        ImportHelper::upsertPropertyValue($itemId, $propertyId, 'ru', $propValue);
                    }
                }
            }
        });
    }
}
