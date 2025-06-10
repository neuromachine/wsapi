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
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),

//                'blocks' => 'test',

                // рекурсивные под‑категории TODO: проверить на категориях у кот. есть вложенные!
                'children' => BlockCategoryResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),

                // позиции (items) в категории
//                'items'    => BlockItemResource::collection(
//                    $this->whenLoaded('items')
//                ),
            ]
        );
    }
}
