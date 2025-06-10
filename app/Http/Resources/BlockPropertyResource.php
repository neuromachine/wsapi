<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockPropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'key'     => $this->key,
            'name'   => $this->name,
            'type'   => $this->type,
            'is_required'   => $this->is_required,
            'is_collection'   => $this->is_collection,
            'is_unique'   => $this->is_unique,
        ];
    }
}
