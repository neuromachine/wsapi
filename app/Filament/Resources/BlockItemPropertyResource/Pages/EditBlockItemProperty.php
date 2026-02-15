<?php

namespace App\Filament\Resources\BlockItemPropertyResource\Pages;

use App\Filament\Resources\BlockItemPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockItemProperty extends EditRecord
{
    protected static string $resource = BlockItemPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
