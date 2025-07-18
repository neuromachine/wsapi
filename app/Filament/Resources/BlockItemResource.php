<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockItemResource\Pages;
use App\Filament\Resources\BlockItemResource\RelationManagers;
use App\Models\BlockItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput as TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;


use Filament\Forms\Components\Section; // Добавь этот импорт для группировки полей


class BlockItemResource extends Resource
{
    protected static ?string $model = BlockItem::class;


    protected static ?string $navigationLabel = 'Позиции';
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('block_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('category_id')
                    ->numeric(),
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Select::make('block_id') // внешний ключ
                ->relationship('block', 'name') // 'block' - метод отношения в BlockItem модели, 'name' - поле для отображения из Block
                ->required()
                ->label('Категория'),

                Repeater::make('properties')
                    ->relationship()
                    ->label('Repeater : properties')
                    ->schema([
                        Group::make([
                            TextInput::make('name'),
                            TextInput::make('key'),
                            /*
                            KeyValue::make('properties')
                                ->label('Свойства')
                                ->keyLabel('Ключ')
                                ->valueLabel('Значение'),
                            */
                        ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),


                Repeater::make('propertyValues')
                    ->relationship()
                    ->label('Значения свойств')
                    ->schema([
                        Select::make('property_id')
                            ->label('Свойство')
                            ->options(fn() => \App\Models\BlockItemProperty::pluck('name', 'id')->toArray())
                            ->preload()
                            ->required(),

                        Select::make('value_type')
                            ->label('Тип данных')
                            ->options([
                                'string'  => 'string',
                                'html'  => 'html',
                                'json'  => 'json',
                                // добавишь сюда новые типы
                            ])
                            ->required()
                            ->reactive(),

                        TextInput::make('value')
                            ->label('value_type: string')
                            ->visible(fn($get) => $get('value_type') === 'string'),

                        // WYSIWYG‑редактор для HTML
                        RichEditor::make('value')
                            ->label('value_type: html')
                            ->toolbarButtons([
                                'bold',
                                'undo',
                            ])
                            ->visible(fn($get) => $get('value_type') === 'html'),

                        // 3) JSON/code — с подсветкой
                        Textarea::make('value')
                            ->label('value_type: json')
                            ->visible(fn($get) => $get('value_type') === 'json'),

                        // ──────────────────────────────────────────────────────────────
                        // СЫРОЕ JSON-поле (для всех типов, просто посмотреть)
                        Textarea::make('value')
                            ->label('Raw')
                            ->rows(6)
                            ->visible(fn($get) => ! in_array($get('value_type'), ['string', 'html', 'json'])),
                        // ──────────────────────────────────────────────────────────────




                    ])




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('block_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('block.name') // Отображаем имя связанного блока в таблице BlockItems
                ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListBlockItems::route('/'),
            'create' => Pages\CreateBlockItem::route('/create'),
            'edit' => Pages\EditBlockItem::route('/{record}/edit'),
        ];
    }
}
