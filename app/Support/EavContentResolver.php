<?php

namespace App\Support;

use Illuminate\Support\Collection;

class EavContentResolver
{
    private static function castValue(mixed $value, ?string $type): mixed
    {
        return match($type) {
            'json'    => json_decode($value, associative: true) ?? $value,
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'float'   => (float) $value,
            default   => $value,
        };
    }

    private static function flattenItem(mixed $item): array
    {
        return $item->propertyValues->mapWithKeys(function ($pv) {
            return [$pv->property->key => self::castValue($pv->value, $pv->value_type)];
        })->all();
    }

    /**
     * Разворачивает items блока с EAV-значениями в плоский массив content.
     *
     * Для single-item блоков (descr_data и подобных) возвращает
     * единственный объект: ['title' => '...', 'body' => '...']
     *
     * Для multi-item блоков возвращает массив таких объектов.
     *
     * @param Collection $items  — block->items (с загруженным propertyValues.property)
     * @param bool $single       — true: вернуть первый элемент, false: вернуть массив
     */
    public static function resolve(Collection $items, bool $single = true, bool $keyed = false): array
    {
        if ($single) {
            $item = $items->first();
            return $item ? self::flattenItem($item) : [];
        }

        if ($keyed) {
            return $items->mapWithKeys(function ($item) {
                return [$item->key => self::flattenItem($item)];
            })->all();
        }

        return $items->map(fn($item) => self::flattenItem($item))->values()->all();
    }
}
