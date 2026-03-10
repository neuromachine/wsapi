<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockCategoryResource extends JsonResource
{
    private function resolveContent(): array
    {
        if (! $this->relationLoaded('blocks')) {
            return [];
        }

        $descrBlock = $this->blocks->firstWhere('key', 'descr_data');

        if (! $descrBlock || ! $descrBlock->relationLoaded('items')) {
            return [];
        }

        return \App\Support\EavContentResolver::resolve($descrBlock->items, single: true);
    }

    public function toArray(Request $request): array
    {
        return array_merge(
            $this->attributesToArray(),
            [
                'content' => $this->resolveContent(),
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),
                // рекурсивные под‑категории TODO: проверить на категориях у кот. есть вложенные!
                'children' => BlockCategoryResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),
            ]
        );
    }
}
