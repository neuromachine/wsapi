<?php
namespace App\Repositories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Collection;

class BlockRepository
{

    public function getBlock(string $locale, string $key)
    {

        return Block::with(
            [
                'items' => function ($q) use ($locale) {
                    $q->whereHas('propertyValues', function ($sub) use ($locale) {
                        $sub->where('locale', $locale);
                    });
                },
                'properties',
                'items.propertyValues.property',
            ]
        )
            ->where('key', $key)
            ->whereHas('items.propertyValues', function ($q) use ($locale) {
                $q->where('locale', $locale);
            })
            ->firstOrFail();

/*
        dd(
            Block::with(
                [
                    'items' => function ($q) use ($locale) {
                        $q->whereHas('propertyValues', function ($sub) use ($locale) {
                                $sub->where('locale', $locale);
                            });
                    },
                    'properties',
                    'items.propertyValues.property',
                ]
            )
                ->where('key', $key)
                ->whereHas('items.propertyValues', function ($q) use ($locale) {
                    $q->where('locale', $locale);
                })
                ->firstOrFail()
        );
*/

/*
        return Block::with(
                [
                    'properties',
                    'items.propertyValues.property',
                ]
            )
            ->where('key', $key)
            ->firstOrFail();
*/
    }


}
