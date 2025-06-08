<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->attributesToArray(),
            [
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),
            ]
        );
    }
}
