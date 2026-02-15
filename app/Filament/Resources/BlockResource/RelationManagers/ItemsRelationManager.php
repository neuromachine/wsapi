<?php

namespace App\Filament\Resources\BlockResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components;
use Filament\Tables\Columns;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Добавь поля для BlockItem, которые ты хочешь редактировать
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Components\Textarea::make('description')
                    ->rows(5)
                    ->cols(10),
                // Добавь другие поля из твоей модели BlockItem
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name') // Укажи поле, которое будет использоваться как заголовок записи в RelationManager
            ->columns([
                // Добавь колонки для отображения BlockItem
                Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                // Добавь другие колонки из твоей модели BlockItem
            ])
            ->filters([
                // Опционально: добавь фильтры для BlockItem
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Добавить кнопку "Создать"
            ])
            ->actions([
                Tables\Actions\EditAction::make(),   // Добавить кнопку "Редактировать"
                Tables\Actions\DeleteAction::make(), // Добавить кнопку "Удалить"
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
