<?php

namespace App\Filament\Resources\BlocksCategoriesResource\Pages;

use App\Filament\Resources\BlocksCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlocksCategories extends EditRecord
{
    protected static string $resource = BlocksCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
