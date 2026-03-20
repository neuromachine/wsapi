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
            'number'  => is_numeric($value) ? $value + 0 : $value,
            default   => $value,
        };
    }

    private static function flattenItem($item): array
    {
        $result = [];

        foreach ($item->propertyValues as $pv) {
            $key        = $pv->property->key;
            $value      = self::castValue($pv->value, $pv->value_type);
            $isCollection = (bool) $pv->property->is_collection;

            if ($isCollection) {
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private static function sortIfNeeded(Collection $items): Collection
    {
        $first = $items->first();

        if (! $first) {
            return $items;
        }

        $hasSort = $first->propertyValues
            ->contains(fn($pv) => $pv->property->key === 'sort');

        if (! $hasSort) {
            return $items;
        }

        return $items->sortBy(function ($item) {
            $sortValue = $item->propertyValues
                ->first(fn($pv) => $pv->property->key === 'sort')
                ?->value;

            return is_numeric($sortValue) ? (int) $sortValue : 0;
        })->values();
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
        $items = self::sortIfNeeded($items);

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
