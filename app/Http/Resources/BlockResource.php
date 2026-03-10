<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Support\EavContentResolver;

class BlockResource extends JsonResource
{
    private const SINGLETON_KEYS = ['descr_data'];
    public function toArray($request): array
    {
        $items = $this->whenLoaded('items', fn() => $this->items, collect());
        $isSingleton = in_array($this->key, self::SINGLETON_KEYS, strict: true);

        return array_merge(
            $this->attributesToArray(), // TODO: перечислить поля явно
            [
                'content' => EavContentResolver::resolve($items, single: $isSingleton),
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
