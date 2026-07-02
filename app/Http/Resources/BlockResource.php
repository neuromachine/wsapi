<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\EavContentResolver;
use App\Support\BlockAttachMap;

class BlockResource extends JsonResource
{
    public function toArray($request): array
    {
        $items = $this->relationLoaded('items') ? $this->items : collect();
        $isSingleton = BlockAttachMap::isSingle($this->key);

        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'laravel_through_key' => $this->laravel_through_key ?? null,
            'content' => EavContentResolver::resolve($items, single: $isSingleton),
            'attach'  => BlockAttachMap::get($this->key),
            'properties'  => BlockPropertyResource::collection(
                $this->whenLoaded('properties')
            ),
            'items'       => BlockItemResource::collection(
                $this->whenLoaded('items')
            ),
        ];
    }
}
