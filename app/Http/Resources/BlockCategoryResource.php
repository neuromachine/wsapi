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

        return array_merge(
            $this->attributesToArray(),
            [
                'section' => $request->locale, // TODO: refactor to explicit scope or separate mapping
                'content' => $assembler->resolveContent(),
                'sections' => $assembler->resolveSections(),
                'subcategories' => $assembler->resolveSubitems(),
                'blocks' => BlockResource::collection(
                    $this->whenLoaded('blocks')
                ),
                'children' => BlockCategoryResource::collection(
                    $this->whenLoaded('childrenRecursive')
                ),
            ]
        );
    }
}
