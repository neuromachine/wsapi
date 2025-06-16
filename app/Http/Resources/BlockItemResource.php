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
                    $values = $group->pluck('value')->all();
                    return [$key => count($values) === 1 ? $values[0] : $values];
                }),
        ];
    }
}
