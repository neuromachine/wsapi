<?php
namespace App\Repositories;

use App\Models\BlockItem;
use Illuminate\Database\Eloquent\Collection;

class BlockItemRepository
{

    public function getItem(string $locale, string $key)
    {

        return BlockItem::with([
            'propertyValues' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'propertyValues.property',
        ])
            ->where('key', $key)
            ->whereHas('propertyValues', function ($q) use ($locale) {
                $q->where('locale', $locale);
            })
            ->firstOrFail();
    }


}
