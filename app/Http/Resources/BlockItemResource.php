<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\EavContentResolver;

class BlockItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'key'  => $this->key,
            'name' => $this->name,

            'properties' => EavContentResolver::resolveItem($this->resource),
        ];
    }
}
