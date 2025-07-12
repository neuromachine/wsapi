<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockItemPropertyValueResource\Pages;
use App\Filament\Resources\BlockItemPropertyValueResource\RelationManagers;
use App\Models\BlockItemPropertyValue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlockItemPropertyValueResource extends Resource
{
    protected static ?string $model = BlockItemPropertyValue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('property_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('item_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('value')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('value_type')
                    ->required()
                    ->maxLength(255)
                    ->default('string'),
                Forms\Components\TextInput::make('locale')
                    ->maxLength(255),
                Forms\Components\TextInput::make('version')
                    ->numeric(),
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
