<?php

namespace App\Filament\Resources\BlockItemResource\Pages;

use App\Filament\Resources\BlockItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockItem extends EditRecord
{
    protected static string $resource = BlockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
