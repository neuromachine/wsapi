<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockCategoryStructureResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = app()->getLocale(); // например, 'ru' или 'en'

        return [
            'id'    => $this->id,
            'slug'  => $this->slug,
            'title' => $this->title,
            'children' => BlockCategoryStructureResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),
        ];
    }
}
