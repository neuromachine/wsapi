<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockCategoryStructureResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge(
            $this->attributesToArray(),
            [
                'children' => BlockCategoryStructureResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),
            ]
        );
    }
}
