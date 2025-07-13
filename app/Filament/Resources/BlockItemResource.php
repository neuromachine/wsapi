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

use Filament\Forms\Components\Section; // Добавь этот импорт для группировки полей


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

                Repeater::make('propertyValues') // Имя метода отношения в модели BlockItem (например, public function propertyValues())
                ->relationship() // Указывает, что Repeater работает с Eloquent отношением
                ->label('Значения свойств')
                    ->schema([
                        Select::make('property_id')
                            ->hidden()
                            ->required()
                            ->preload()
                            ->options(fn () => \App\Models\BlockItemProperty::pluck('name', 'id')->toArray())
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Forms\Components\Textarea::make('value')
                            ->label('JSON-значение')
                            ->rows(5)
                            ->autosize()
                            ->required()
                            ->helperText('Редактируй JSON вручную, если нет специальной формы'),
                    ])

                /*
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
                            ->options(fn () => \App\Models\BlockItemProperty::pluck('name', 'id')->toArray()) // Загружаем названия свойств из BlockItemProperty
                            ->live() // <-- ОЧЕНЬ ВАЖНО: Делает поле реактивным. При изменении property_id, форма будет перерисовываться.
                            ->afterStateUpdated(function (Forms\Set $set) {
                                // При изменении свойства, очищаем поле 'value_parsed'
                                // чтобы избежать путаницы, если меняем тип JSON
                                $set('value_parsed', null);
                            }),

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

                        // --- Поле value (скрытое, так как мы будем использовать отдельные поля) ---
                        TextInput::make('value')
                            ->hidden() // Скрываем основное JSON-поле, оно будет заполняться программно
                            ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                                // <-- СОБИРАЕМ JSON ПРИ СОХРАНЕНИИ
                                $propertyId = $get('property_id');
                                $property = $propertyId ? \App\Models\BlockItemProperty::find($propertyId) : null;

                                if ($property && $property->type === 'json') {
                                    // Если это JSON-адрес, берем данные из value_parsed
                                    $parsedData = $get('value_parsed');
                                    return json_encode($parsedData);
                                }
                                // Если это не JSON-поле или другой тип, просто возвращаем state (который будет пустым)
                                return $state; // Или можно вернуть $get('value_text') если есть текстовое поле
                            })
                            ->mutateStateUsing(function ($state, Forms\Get $get) {
                                // <-- РАЗБИРАЕМ JSON ПРИ ЗАГРУЗКЕ
                                $propertyId = $get('property_id');
                                $property = $propertyId ? \App\Models\BlockItemProperty::find($propertyId) : null;

                                if ($property && $property->type === 'json' && is_string($state)) {
                                    // Если это JSON-адрес, парсим его
                                    return json_decode($state, true);
                                }
                                return null; // Возвращаем null, если не JSON или другой тип
                            })
                            // Это поле "value" теперь будет промежуточным,
                            // хранящим либо JSON-строку, либо разобранный массив.
                            // Его важно правильно мутировать.
                            // Мы будем использовать вложенное поле 'value_parsed' для отдельных полей.
                            ->statePath('value_parsed'), // <-- Указываем, что данные для 'value' будут браться из 'value_parsed'
                        // Это позволяет нам использовать одно имя для полей,
                        // но физически хранить их в другом ключе внутри формы.


                        // --- Динамический блок полей для JSON ---
                        // Используем Section, чтобы сгруппировать динамические поля
                        Section::make('Детали свойства')
                            ->columns(2) // 2 колонки внутри секции
                            ->hidden(fn (Forms\Get $get) =>
                                // Скрываем секцию, если свойство не выбрано или не является JSON-типом
                                !($propertyId = $get('property_id')) ||
                                !\App\Models\BlockItemProperty::find($propertyId)?->type ||
                                !\in_array(\App\Models\BlockItemProperty::find($propertyId)?->type, ['json'])
                            )
                            ->schema(function (Forms\Get $get): array {
                                // <-- Здесь генерируем поля динамически в зависимости от property_id
                                $propertyId = $get('property_id');
                                $property = $propertyId ? \App\Models\BlockItemProperty::find($propertyId) : null;

                                if ($property) {
                                    switch ($property->type) {
                                        case 'json':
                                            return [
                                                TextInput::make('value_parsed.street') // value_parsed.street - это путь к полю внутри 'value_parsed'
                                                ->label('Улица')
                                                    ->required(),
                                                TextInput::make('value_parsed.city')
                                                    ->label('Город')
                                                    ->required(),
                                                TextInput::make('value_parsed.zip')
                                                    ->label('Индекс')
                                                    ->required(),
                                                // Добавь другие поля для адреса
                                            ];
                                        // Добавь другие case для разных типов JSON-структур
                                        // case 'sizes_json':
                                        //     return [
                                        //         TextInput::make('value_parsed.width')->numeric(),
                                        //         TextInput::make('value_parsed.height')->numeric(),
                                        //     ];
                                    }
                                }
                                // Если тип не JSON или не распознан, возвращаем пустой массив полей
                                return [];
                            }),

                        // --- Обычное текстовое поле для значения (если не JSON) ---
                        // Это поле будет использоваться, если свойство НЕ является JSON-типом.
                        // Например, если свойство "Цвет" и его значение - просто текст.
                        TextInput::make('value_text') // Используем другое имя, чтобы не конфликтовать с 'value'
                        ->label('Значение')
                            ->required(fn (Forms\Get $get) =>
                                // Поле обязательно, если это не JSON-тип
                                !($propertyId = $get('property_id')) ||
                                !\App\Models\BlockItemProperty::find($propertyId)?->type ||
                                !\in_array(\App\Models\BlockItemProperty::find($propertyId)?->type, ['json'])
                            )
                            ->hidden(fn (Forms\Get $get) =>
                                // Скрываем, если это JSON-тип
                                ($propertyId = $get('property_id')) &&
                                \App\Models\BlockItemProperty::find($propertyId)?->type &&
                                \in_array(\App\Models\BlockItemProperty::find($propertyId)?->type, ['json'])
                            )
                            ->dehydrateStateUsing(function (?string $state, Forms\Get $get) {
                                // Сохраняем как обычный текст, если не JSON
                                $propertyId = $get('property_id');
                                $property = $propertyId ? \App\Models\BlockItemProperty::find($propertyId) : null;
                                if ($property && !\in_array($property->type, ['json'])) {
                                    return $state;
                                }
                                return null; // Не сохраняем, если это JSON-тип
                            })
                            ->mutateStateUsing(function (?string $state, Forms\Get $get) {
                                // Загружаем как обычный текст, если не JSON
                                $propertyId = $get('property_id');
                                $property = $propertyId ? \App\Models\BlockItemProperty::find($propertyId) : null;
                                if ($property && !\in_array($property->type, ['json'])) {
                                    return $state;
                                }
                                return null; // Не загружаем, если это JSON-тип
                            })
                            ->statePath('value') // <-- Важно: это поле также использует 'value' как источник/получатель данных
                        // Но оно будет работать только когда секция JSON скрыта.
                        // Мы переименовали его в 'value_text' в схеме, но оно все равно пишет в 'value' в БД.
                        ,

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
                */

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
