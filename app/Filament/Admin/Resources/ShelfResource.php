<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ShelfResource\Pages;
use App\Models\Shelf;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Zone;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShelfResource extends Resource
{
    protected static ?string $model = Shelf::class;

    protected static ?string $navigationIcon = 'gmdi-shelves';
    protected static ?string $navigationLabel = 'Prateleiras';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?string $pluralLabel = 'Prateleiras';
    protected static ?string $modelLabel = 'Prateleira';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Prateleiras');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('created_by')->default(fn () => auth()->id()),
                Hidden::make('updated_by')->default(fn () => auth()->id()),
                Hidden::make('deleted_by'),

                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Armazém')
                    ->required()
                    ->searchable(),

                Select::make('zone_id')
                    ->relationship('zone', 'name')
                    ->label('Zona')
                    ->nullable()
                    ->searchable(),

                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->label('Sala')
                    ->nullable()
                    ->searchable(),

                Select::make('streets')
                    ->relationship('streets', 'name')
                    ->label('Ruas')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                TextInput::make('levels')
                    ->label('Níveis')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Textarea::make('description')
                    ->label('Descrição')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->summarize(Count::make())
                    ->searchable(),

                TextColumn::make('levels')
                    ->label('Níveis')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('warehouse.name')
                    ->label('Armazém')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('zone.name')
                    ->label('Zona')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('room.name')
                    ->label('Sala')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('streets.name')
                    ->label('Ruas')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Alterado Por')
                    ->sortable()
                    ->placeholder('Não se aplica'),

                TextColumn::make('updated_at')
                    ->label('Última Alteração')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Criado Por')
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleter.name')
                    ->label('Removido Por')
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Data de Remoção')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->options(Shelf::pluck('name', 'name')->toArray()),

                SelectFilter::make('warehouse_id')
                    ->label('Armazém')
                    ->options(Warehouse::pluck('name', 'id')->toArray())
                    ->searchable(),

                SelectFilter::make('zone_id')
                    ->label('Zona')
                    ->options(Zone::pluck('name', 'id')->toArray())
                    ->searchable(),

                SelectFilter::make('streets')
                    ->label('Ruas')
                    ->multiple()
                    ->relationship('streets', 'name'),

                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('deleted_by')
                    ->label('Removido Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
                Tables\Actions\RestoreAction::make()->label('Restaurar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Excluir em massa'),
                Tables\Actions\RestoreBulkAction::make()->label('Restaurar em massa'),
                Tables\Actions\ForceDeleteBulkAction::make()->label('Exclusão definitiva em massa'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShelves::route('/'),
            'create' => Pages\CreateShelf::route('/create'),
            'edit' => Pages\EditShelf::route('/{record}/edit'),
        ];
    }
}