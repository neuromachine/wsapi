<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    public function toArray($request): array
    {

        return array_merge(
            $this->attributesToArray(),
            [
                'properties'  => BlockPropertyResource::collection(
                    $this->whenLoaded('properties')
                ),
                'items'       => BlockItemResource::collection(
                    $this->whenLoaded('items')
                ),
            ]
        );
    }
}
