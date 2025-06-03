<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'name'        => $this->name,
            'position'    => $this->position,
            'properties'  => BlockPropertyResource::collection(
                $this->whenLoaded('properties')
            ),
            'items'       => BlockItemResource::collection(
                $this->whenLoaded('items')
            ),
        ];
    }
}
