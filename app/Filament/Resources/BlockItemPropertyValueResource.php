<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockItemPropertyValueResource\Pages;
use App\Filament\Resources\BlockItemPropertyValueResource\RelationManagers;
use App\Models\BlockItemPropertyValue;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;

class BlockItemPropertyValueResource extends Resource
{
    protected static ?string $model = BlockItemPropertyValue::class;

    protected static ?string $navigationLabel = 'Значения свойств';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Имя позиции - достаем из таблицы содержащей позиции TODO: проверить/разобрать во всех случаях - тестирвоать
                Placeholder::make('item_name')
                    ->content(function (?array $state, Forms\Get $get) {
                        $itemId = $get('item_id');
                        if ($itemId) {
                            $property = \App\Models\BlockItem::find($itemId);
                            return $property ? $property->name : 'Неизвестное свойство';
                        }
                        return 'Выберите позицию';
                    })
                    ->label('Позиция'),

                Placeholder::make('item_id')
                    ->content(fn (?string $state, $record): string => $record?->item_id ?? 'N/A')
                    ->label('ID позиции - владельца'),

                // Имя свойства - достаем из таблицы содержащей сами свойства блоков TODO: проверить/разобрать - в т.ч в связке с пустым и т.п.; внедрил прямо из GPT
                Placeholder::make('property_name')
                    ->content(function (?array $state, Forms\Get $get) {
                        $propertyId = $get('property_id');
                        if ($propertyId) {
                            $property = \App\Models\BlockItemProperty::find($propertyId);
                            return $property ? $property->name : 'Неизвестное свойство';
                        }
                        return 'Выберите свойство';
                    })
                    ->label('Свойство'),


                Placeholder::make('property_id')
                    ->content(fn (?string $state, $record): string => $record?->property_id ?? 'N/A')
                    ->label('ID свойства - владельца'),

                // Дата создания
                Placeholder::make('created_at_display')
                    ->label('Дата создания')
                    ->content(fn (?string $state, $record): string => $record?->created_at?->format('d.m.Y H:i') ?? 'N/A'),



                // Отображение информации о связанном родительском Свойстве
                /*
                Placeholder::make('parent_property_info')
                    ->label('Информация о родительском блоке')
                    ->content(function (Forms\Get $get) {
                        $propertyId = $get('property_id');
                        if ($propertyId) {
                            $prop = \App\Models\BlockItemProperty::find($propertyId);
                            return $prop ? "Свойство: {$prop->name} (ID: {$prop->id})" : 'Блок не найден';
                        }
                        return 'Блок не выбран';
                    }),
                */


                Select::make('value_type')
                    ->label('Тип данных')
                    ->options([
                        'string' => 'string',
                        'json' => 'json',
                        'html' => 'html',
                    ])
                    ->reactive()
                    ->default('string')
                    ->required(),

                Forms\Components\TextInput::make('locale')
                    ->maxLength(255),

                Forms\Components\TextInput::make('version')
                    ->numeric(),

                RichEditor::make('value')
                    ->label('value_type: html')
                    ->disableGrammarly()
                    ->toolbarButtons([
                        'link',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h3',
                        'italic',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('value_type') === 'html'),

                Forms\Components\Textarea::make('value')
                    ->label('value_type: json')
                    ->columnSpanFull()
                    ->rows(12)
                    ->visible(fn($get) => $get('value_type') === 'json'),

                Forms\Components\Textarea::make('value')
                    ->label('value_type: string')
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('value_type') === 'string'),

                Forms\Components\Textarea::make('value')
                    ->label('value_type: OTHER')
                    ->columnSpanFull()
                    ->visible(fn($get) => ! in_array($get('value_type'), ['string', 'html', 'json'])),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlockItemPropertyValues::route('/'),
            'create' => Pages\CreateBlockItemPropertyValue::route('/create'),
            'edit' => Pages\EditBlockItemPropertyValue::route('/{record}/edit'),
        ];
    }
}
