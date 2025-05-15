<?php

namespace App\Services;

class TransformService
{
    public function transform(array $dictionary): array
    {
        return [
            'key' => $dictionary['key'],
            'name' => $dictionary['name'],
            'items' => collect($dictionary['dictionaryitems'] ?? [])
                ->map(function ($item) {
                    $transformedItem = [
                        'id' => $item['id'],
                        'key' => $item['key'],
                        'name' => $item['name'],
                        'properties' => [],
                    ];

                    foreach ($item['property_values'] ?? [] as $value) {
                        $propertyKey = $value['property']['key'] ?? null;
                        if (!$propertyKey) {
                            continue;
                        }

                        // Преобразуем select с params, если есть
                        $propertyType = $value['property']['type'] ?? null;
                        $rawValue = $value['value'];

                        if ($propertyType === 'select' && !empty($value['property']['params'])) {
                            $options = json_decode($value['property']['params'], true);
                            $rawValue = [
                                'key' => $value['value'],
                                'label' => $options[$value['value']] ?? $value['value'],
                            ];
                        }

                        $transformedItem['properties'][$propertyKey] = $rawValue;
                    }

                    return $transformedItem;
                })->toArray(),
        ];
    }

    public function transformItem(array $item): array
    {
        $dataProp = (object)[];
        foreach ($item['properties'] as $arrProp)
        {
            $arrProp = array_merge($arrProp,$arrProp['pivot']);
            unset($arrProp['pivot']);
            $dataProp->{$arrProp['key']} = $arrProp;
        }
        $item['props'] = $dataProp;
        unset($item['properties']);
        return $item;
    }
}
