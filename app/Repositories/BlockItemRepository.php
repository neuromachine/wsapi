<?php
namespace App\Repositories;

use App\Models\BlockItem;
use Illuminate\Database\Eloquent\Collection;

class BlockItemRepository
{

    public function getItem(string $key)
    {

        return BlockItem::with(
                [
                    'propertyValues.property',
                ]
            )
            ->where('key', $key)
            ->firstOrFail();
    }


}
