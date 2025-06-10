<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**/
        $properties = $this->propertyValues->mapWithKeys(function ($val) {
            return [$val->property->key => $val->value];
        });


        return [
            'id' => $this->id,
            'name' => $this->name,
            'properties' => $properties,
            // общие поля позиции
            'block' => new BlockResource(
                $this->whenLoaded('block')
            ),


            // добавь при необходимости: 'created_at', 'key', и т.д.

//            // реальные значения свойств этой позиции TODO: разобраться в предложенных подходах (см. $properties)
//            'property_values' => ItemPropertyValueResource::collection(
//                $this->whenLoaded('propertyValues')
//            ),
        ];


/*        return [
            'id'         => $this->id,
            'block_id'   => $this->block_id,
            'position'   => $this->position,
            'visible'    => $this->visible,
            'properties' => BlockItemPropertyResource::collection(
                $this->whenLoaded('itemProperties')
            ),
        ];*/
    }
}
