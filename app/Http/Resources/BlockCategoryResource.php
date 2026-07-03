<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\CategoryPayloadAssembler;

class BlockCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $assembler = new CategoryPayloadAssembler($this->resource, $request->locale);

        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'content' => $assembler->resolveContent(),
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'section' => $request->locale, // TODO: refactor to explicit scope or separate mapping
            'sections' => $assembler->resolveSections(),
            'subcategories' => $assembler->resolveSubitems(),
            'blocks' => BlockResource::collection(
                $this->whenLoaded('blocks')
            ),
            'children' => BlockCategoryResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),
        ];
    }
}
