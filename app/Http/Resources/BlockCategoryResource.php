<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\EavContentResolver;
use App\Support\BlockAttachMap;
use App\Models\BlocksCategories;

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
                single: BlockAttachMap::isSingle($block->key),
                keyed:  BlockAttachMap::isKeyed($block->key)
            );
        }

        return $sections;
    }

    private function resolveSubitems($locale): array
    {
        if (! $this->relationLoaded('children')) {
            return [];
        }

        $result = [];

        foreach ($this->children as $category) {

            $newCat = BlocksCategories::where('key', $category->key)->firstOrFail();

            $subCat =  BlocksCategories::with([
                'items' => function ($q) use ($locale,$newCat) {
                    $q->where('category_id', $newCat->id)
                        ->whereHas('propertyValues', function ($sub) use ($locale) {
                            $sub->where('locale', $locale);
                        });
                },
                'items.propertyValues' => function ($q) use ($locale) {
                    $q->where('locale', $locale);
                },
            ])
                ->where('id', $category->id)
                ->first();

            $native = array(
                'id' => $subCat->id,
                'slug' => $subCat->key,
                'childs' => []
            );

            $result[] = array_merge($native,EavContentResolver::resolve($subCat->items, single: true));
        }

        // TODO: sort elements in db ))
        usort($result, function ($a, $b) {
            if(empty($a['priority']) && empty($b['priority'])) return -1;
            if(!empty($a['priority']) && empty($b['priority'])) return 0;
            if(empty($a['priority']) && !empty($b['priority'])) return 1;
            if(!empty($a['priority']) && !empty($b['priority'])) return intval($a['priority']) <=> intval($b['priority']);
            return 0;
        });

        return $result;
    }

    public function toArray(Request $request): array
    {
        return array_merge(
            $this->attributesToArray(),
            [
                'section' => $request->locale, // TODO: refactor
                'content' => $this->resolveContent(),
                'sections' => $this->resolveSections(),
                'subcategories' => $this->resolveSubitems($request->locale),
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),
                // рекурсивные под‑категории TODO: проверить на категориях у кот. есть вложенные! не верное место? см. ресурс
                'children' => BlockCategoryResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),
            ]
        );
    }
}
