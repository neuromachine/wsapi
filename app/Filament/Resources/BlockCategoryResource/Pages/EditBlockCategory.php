<?php

namespace App\Filament\Resources\BlockCategoryResource\Pages;

use App\Filament\Resources\BlockCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockCategory extends EditRecord
{
    protected static string $resource = BlockCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
