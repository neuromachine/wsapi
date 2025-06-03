<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /*
        $properties = $this->propertyValues->mapWithKeys(function ($val) {
            return [$val->property->key => $val->value];
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'properties' => $properties,
            // добавь при необходимости: 'created_at', 'key', и т.д.
        ];
        */

        return [
            'id'         => $this->id,
            'block_id'   => $this->block_id,
            'position'   => $this->position,
            'visible'    => $this->visible,
            'properties' => BlockItemPropertyResource::collection(
                $this->whenLoaded('itemProperties')
            ),
        ];
    }
}
