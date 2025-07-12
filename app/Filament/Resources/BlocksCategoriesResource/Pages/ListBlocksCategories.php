<?php

namespace App\Filament\Resources\BlocksCategoriesResource\Pages;

use App\Filament\Resources\BlocksCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlocksCategories extends ListRecords
{
    protected static string $resource = BlocksCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
