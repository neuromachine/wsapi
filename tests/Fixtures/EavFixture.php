<?php

namespace Tests\Fixtures;

use App\Models\Block;
use App\Models\BlockItem;
use App\Models\BlockItemProperty;
use App\Models\BlockItemPropertyValue;
use App\Models\BlocksCategories;

class EavFixture
{
    public static function createServicesCategory(): BlocksCategories
    {
        $category = BlocksCategories::create([
            'key' => 'services',
            'name' => 'Services',
            'parent_id' => null
        ]);

        $subCategory = BlocksCategories::create([
            'key' => 'sub-service',
            'name' => 'Sub Service',
            'parent_id' => $category->id
        ]);

        $contentBlock = Block::create(['key' => 'descr_data', 'name' => 'Content']);
        $sectionsBlock = Block::create(['key' => 'slide', 'name' => 'Slide']);

        self::createItemWithProperties($contentBlock, $category, [
            ['key' => 'title', 'value' => 'Main Title', 'type' => 'string', 'is_collection' => false],
            ['key' => 'description', 'value' => 'Some description', 'type' => 'string', 'is_collection' => false],
        ]);

        self::createItemWithProperties($sectionsBlock, $category, [
            ['key' => 'slide_image', 'value' => 'image.jpg', 'type' => 'string', 'is_collection' => false],
        ], 'slide_1');

        self::createItemWithProperties($contentBlock, $subCategory, [
            ['key' => 'title', 'value' => 'Sub Title', 'type' => 'string', 'is_collection' => false],
        ]);

        return $category;
    }

    public static function createOffersCategory(): BlocksCategories
    {
        $category = BlocksCategories::create([
            'key' => 'test-offers',
            'name' => 'Test Offers',
            'parent_id' => null
        ]);

        $offersBlock = Block::create(['key' => 'offers', 'name' => 'Offers']);

        self::createItemWithProperties($offersBlock, $category, [
            ['key' => 'offer_title', 'value' => 'Special Offer', 'type' => 'string', 'is_collection' => false],
            ['key' => 'offer_price', 'value' => '100', 'type' => 'integer', 'is_collection' => false],
        ], 'offer_1');

        return $category;
    }

    public static function createItemWithProperties(Block $block, BlocksCategories $category, array $propertiesData, string $itemKey = null): BlockItem
    {
        $item = BlockItem::create([
            'block_id' => $block->id,
            'category_id' => $category->id,
            'key' => $itemKey ?? 'item_' . uniqid()
        ]);

        foreach ($propertiesData as $propData) {
            $property = BlockItemProperty::firstOrCreate([
                'block_id' => $block->id,
                'key' => $propData['key']
            ], [
                'name' => ucfirst($propData['key']),
                'is_collection' => $propData['is_collection'] ?? false
            ]);

            BlockItemPropertyValue::create([
                'item_id' => $item->id,
                'property_id' => $property->id,
                'locale' => 'en',
                'value' => $propData['value'],
                'value_type' => $propData['type'] ?? 'string',
                'version' => 1
            ]);
        }

        return $item;
    }
}
