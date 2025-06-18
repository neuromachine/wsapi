<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'key'  => $this->key,
            'name' => $this->name,

            'properties' => $this->propertyValues
                ->groupBy(fn($v) => $v->property->key)
                ->mapWithKeys(function ($group, $key) {
                    $type = $group->first()->property->type;
                    $raw  = $group->pluck('value')->all();

                    if ($type === 'json') {
                        // декодируем JSON-данные из value
                        $decoded = array_map(fn($v) => json_decode($v, true), $raw);
                        return [$key => count($decoded) === 1 ? $decoded[0] : $decoded];
                    }

                    // обычные строковые значения или файлы
                    $values = $raw;
                    return [$key => count($values) === 1 ? $values[0] : $values];
                }),
        ];
    }
}
