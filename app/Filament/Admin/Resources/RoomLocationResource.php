<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoomLocationResource\Pages;
use App\Models\RoomLocation;
use App\Models\User;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoomLocationResource extends Resource
{
    protected static ?string $model = RoomLocation::class;

    protected static ?string $navigationIcon = 'iconoir-closet';
    protected static ?string $navigationLabel = 'Locais de Sala';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?string $pluralLabel = 'Locais de Sala';
    protected static ?string $modelLabel = 'Local de Sala';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Localizações de Salas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('created_by')->default(fn () => auth()->id()),
                Hidden::make('updated_by')->default(fn () => auth()->id()),
                Hidden::make('deleted_by'),

                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->label('Sala')
                    ->nullable()
                    ->searchable(),

                Textarea::make('description')
                    ->label('Descrição')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.name')
                    ->label('Sala')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Não se aplica'),

                TextColumn::make('name')
                    ->label('Nome')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater_by')
                    ->label('Alterado Por')
                    ->sortable()
                    ->placeholder('Não se aplica'),

                TextColumn::make('updated_at')
                    ->label('Última Alteração')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Não se aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator_by')
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

                TextColumn::make('deleter_by')
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
                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('deleted_by')
                    ->label('Removido Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('room_id')
                    ->label('Sala')
                    ->options(Room::pluck('name', 'id')->toArray()),

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
            'index' => Pages\ListRoomLocations::route('/'),
            'create' => Pages\CreateRoomLocation::route('/create'),
            'edit' => Pages\EditRoomLocation::route('/{record}/edit'),
        ];
    }
}