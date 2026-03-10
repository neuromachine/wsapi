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
    public static function resolve(Collection $items, bool $single = true): array
    {
        $resolved = $items->map(function ($item) {
            return $item->propertyValues->mapWithKeys(function ($pv) {
                return [$pv->property->key => self::castValue($pv->value, $pv->value_type)];
            })->all();
        });

        return $single ? ($resolved->first() ?? []) : $resolved->values()->all();
    }
}
