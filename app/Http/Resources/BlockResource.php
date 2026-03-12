<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\EavContentResolver;
use App\Support\BlockAttachMap;

class BlockResource extends JsonResource
{
    private function resolveAttach(): ?string
    {
        return BlockAttachMap::get($this->key);
    }

    public function toArray($request): array
    {
        $items = $this->whenLoaded('items', fn() => $this->items, collect());
        $isSingleton = BlockAttachMap::is($this->key, 'content');

        return array_merge(
            $this->attributesToArray(), // TODO: перечислить поля явно
            [
                'content' => EavContentResolver::resolve($items, single: $isSingleton),
                'attach'  => $this->resolveAttach(),
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
