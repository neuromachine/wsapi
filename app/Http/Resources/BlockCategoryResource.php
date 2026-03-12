<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\EavContentResolver;
use App\Support\BlockAttachMap;

class BlockCategoryResource extends JsonResource
{
    private function resolveContent(): array
    {
        if (! $this->relationLoaded('blocks')) {
            return [];
        }

        $descrBlock = $this->blocks->first(
            fn($b) => BlockAttachMap::is($b->key, 'content')
        );

        if (! $descrBlock || ! $descrBlock->relationLoaded('items')) {
            return [];
        }

        return \App\Support\EavContentResolver::resolve($descrBlock->items, single: true);
    }

    private function resolveSections(): array
    {
        if (! $this->relationLoaded('blocks')) {
            return [];
        }

        $sections = [];

        foreach ($this->blocks as $block) {
            if (! $block->relationLoaded('items')) {
                continue;
            }

            if (! BlockAttachMap::is($block->key, 'sections')) {
                continue;
            }

            $sections[$block->key] = EavContentResolver::resolve(
                $block->items,
                single: BlockAttachMap::isSingle($block->key)
            );
        }

        return $sections;
    }

    public function toArray(Request $request): array
    {
        return array_merge(
            $this->attributesToArray(),
            [
                'content' => $this->resolveContent(),
                'sections' => $this->resolveSections(),
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
