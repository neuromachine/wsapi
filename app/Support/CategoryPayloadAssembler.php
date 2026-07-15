<?php

namespace App\Support;

use App\Models\BlocksCategories;

class CategoryPayloadAssembler
{
    protected BlocksCategories $category;
    protected string $locale;

    public function __construct(BlocksCategories $category, string $locale)
    {
        $this->category = $category;
        $this->locale = $locale;
    }

    public function resolveContent(): array
    {
        if (! $this->category->relationLoaded('blocks')) {
            return [];
        }

        $descrBlock = $this->category->blocks->first(
            fn($b) => BlockAttachMap::is($b->key, 'content')
        );

        if (! $descrBlock || ! $descrBlock->relationLoaded('items')) {
            return [];
        }

        return EavContentResolver::resolve($descrBlock->items, single: true);
    }

    public function resolveSections(): array
    {
        if (! $this->category->relationLoaded('blocks')) {
            return [];
        }

        $sections = [];

        foreach ($this->category->blocks as $block) {
            if (! $block->relationLoaded('items')) {
                continue;
            }

            if (! BlockAttachMap::is($block->key, 'sections')) {
                continue;
            }

            $sections[$block->key] = EavContentResolver::resolve(
                $block->items,
                single: BlockAttachMap::isSingle($block->key),
                keyed:  BlockAttachMap::isKeyed($block->key)
            );

            // Preserve keys for keyed objects
            uasort($sections[$block->key], [$this, 'comparePriority']);
        }

        return $sections;
    }

    public function resolveSubitems(): array
    {
        if (! $this->category->relationLoaded('children')) {
            return [];
        }

        $result = [];

        foreach ($this->category->children as $child) {
            $native = [
                'id' => $child->id,
                'slug' => $child->key,
                'childs' => []
            ];

            if ($child->relationLoaded('items')) {
                $result[] = array_merge($native, EavContentResolver::resolve($child->items, single: true));
            } else {
                $result[] = $native;
            }
        }

        // Re-index array for JSON list output
        usort($result, [$this, 'comparePriority']);

        return $result;
    }

    private function comparePriority(array $a, array $b): int
    {
        $prioA = isset($a['priority']) && $a['priority'] !== '' ? (int)$a['priority'] : null;
        $prioB = isset($b['priority']) && $b['priority'] !== '' ? (int)$b['priority'] : null;

        if ($prioA === null && $prioB === null) {
            return 0;
        }
        if ($prioA === null) {
            return 1;
        }
        if ($prioB === null) {
            return -1;
        }

        return $prioA <=> $prioB;
    }
}
