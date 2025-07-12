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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;

class BlockItemResource extends Resource
{
    protected static ?string $model = BlockItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                ->label('Родительский Блок'),

                // --- НАЧАЛО БЛОКА REPEATER ДЛЯ СВОЙСТВ ---
                Repeater::make('propertyValues') // Имя метода отношения в модели BlockItem (например, public function propertyValues())
                ->relationship() // Указывает, что Repeater работает с Eloquent отношением
                ->label('Значения свойств')
                    ->schema([
                        // Поле для выбора BlockItemProperty (самого свойства)
                        // Это поле будет скрытым, так как мы хотим показывать название свойства, а не ID
                        Select::make('property_id')
                            ->hidden() // Скрываем его, т.к. значение будет подставляться автоматически
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems() // Предотвращает выбор одного и того же свойства несколько раз
                            ->preload() // Предзагрузка всех опций
                            ->options(fn () => \App\Models\BlockItemProperty::pluck('name', 'id')->toArray()), // Загружаем названия свойств из BlockItemProperty

                        // Поле для отображения названия свойства (не редактируемое)
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

                        // Поле для самого значения свойства
                        TextInput::make('value')
                            ->required()
                            ->label('Значение')
                            ->columnSpan(2), // Чтобы это поле занимало больше места

                    ])
                    ->columns(3) // Количество колонок для полей внутри одного элемента Repeatere
                    ->collapsible() // Позволяет сворачивать/разворачивать каждый элемент Repeater
                    ->itemLabel(fn (array $state): ?string =>
                        // Отображает название свойства как заголовок элемента в свернутом виде
                    (\App\Models\BlockItemProperty::find($state['property_id'] ?? null)?->name ?: 'Новое значение')
                    )
                    ->defaultItems(0) // Не добавлять элементы по умолчанию
                    ->cloneable() // Позволяет клонировать элементы
                    ->addActionLabel('Добавить новое значение свойства')
                    ->reorderable() // Позволяет менять порядок
                    ->grid(2), // Отображает элементы Repeater в 2 колонки, если есть место
                // --- КОНЕЦ БЛОКА REPEATER ---

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
