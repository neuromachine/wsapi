<?php
namespace App\Repositories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Collection;

class BlockRepository
{

    public function getBlock(string $key)
    {

        return Block::with(
                [
                    'properties',
                    'items.propertyValues.property',
                ]
            )
            ->where('key', $key)
            ->firstOrFail();
    }


}
