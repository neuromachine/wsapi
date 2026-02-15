<?php

namespace App\Filament\Resources\BlockItemPropertyResource\Pages;

use App\Filament\Resources\BlockItemPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlockItemProperties extends ListRecords
{
    protected static string $resource = BlockItemPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
