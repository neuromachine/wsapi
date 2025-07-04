<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->attributesToArray(),

            [
/*                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),*/

                // Фильтруем items внутри каждого блока по текущей категории:
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks', function () {
                        return $this->blocks->map(function ($block) {
                            $filteredItems = $block
                                ->itemsForCategory($this->id)
                                ->get();

                            $block->setRelation('items', $filteredItems);

                            return $block;
                        });
                    })
                ),


                // рекурсивные под‑категории TODO: проверить на категориях у кот. есть вложенные!
                'children' => BlockCategoryResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),
//                // позиции (items) в категории TODO: оставлено для вариативного использования см. репозиторий (нужно загрузить данные при возможном использовании)
//                'items'    => BlockItemResource::collection(
//                    $this->whenLoaded('items')
//                ),
            ]
        );
    }
}
