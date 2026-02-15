<?php

namespace App\Filament\Resources\BlockItemPropertyValueResource\Pages;

use App\Filament\Resources\BlockItemPropertyValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlockItemPropertyValues extends ListRecords
{
    protected static string $resource = BlockItemPropertyValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
