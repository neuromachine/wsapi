<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OffersResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'category' => $this->resource['category']->name,
            'block' => $this->resource['block']->name,
            'items' => BlockItemResource::collection($this->resource['items']),
        ];
    }
}
