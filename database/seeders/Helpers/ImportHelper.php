<?php

namespace Database\Seeders\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportHelper
{
    /**
     * Get or create a block and return its ID.
     */
    public static function upsertBlock(string $key, string $name, string $description = ''): int
    {
        DB::table('blocks')->updateOrInsert(
            ['key' => $key],
            [
                'name'        => $name,
                'description' => $description,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        return DB::table('blocks')->where('key', $key)->value('id');
    }

    /**
     * Get block ID by key.
     */
    public static function getBlockId(string $key): ?int
    {
        return DB::table('blocks')->where('key', $key)->value('id');
    }

    /**
     * Get category ID by key.
     */
    public static function getCategoryId(string $key): ?int
    {
        return DB::table('blocks_categories')->where('key', $key)->value('id');
    }

    /**
     * Upsert a property and return its ID.
     */
    public static function upsertProperty(int $blockId, string $key, string $name, string $type, bool $isCollection = false, ?int $id = null): int
    {
        $attributes = [
            'block_id' => $blockId,
            'key'      => $key,
        ];
        if ($id !== null) {
            $attributes['id'] = $id;
        }

        DB::table('block_item_properties')->updateOrInsert(
            $attributes,
            [
                'name'          => $name,
                'type'          => $type,
                'is_collection' => $isCollection ? 1 : 0,
            ]
        );

        return DB::table('block_item_properties')
            ->where('block_id', $blockId)
            ->where('key', $key)
            ->value('id');
    }

    /**
     * Get property ID by block ID and key.
     */
    public static function getPropertyId(int $blockId, string $key): ?int
    {
        return DB::table('block_item_properties')
            ->where('block_id', $blockId)
            ->where('key', $key)
            ->value('id');
    }

    /**
     * Upsert a block item and return its ID.
     */
    public static function upsertItem(int $blockId, ?int $categoryId, string $key, string $name, string $description = ''): int
    {
        DB::table('block_items')->updateOrInsert(
            [
                'block_id'    => $blockId,
                'category_id' => $categoryId,
                'key'         => $key,
            ],
            [
                'name'        => $name,
                'description' => $description,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        return DB::table('block_items')
            ->where('block_id', $blockId)
            ->where('category_id', $categoryId)
            ->where('key', $key)
            ->value('id');
    }

    /**
     * Upsert a property value.
     */
    public static function upsertPropertyValue(int $itemId, int $propertyId, string $locale, mixed $value): void
    {
        $valueType = is_array($value) ? 'json' : 'string';
        $encodedValue = is_array($value) ? json_encode($value) : $value;

        DB::table('block_item_property_values')->updateOrInsert(
            [
                'item_id'     => $itemId,
                'property_id' => $propertyId,
                'locale'      => $locale,
            ],
            [
                'value'      => $encodedValue,
                'value_type' => $valueType,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
