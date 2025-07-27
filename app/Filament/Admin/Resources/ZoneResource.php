<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ZoneResource\Pages;
use App\Models\User;
use App\Models\Zone;
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

class ZoneResource extends Resource
{
    protected static ?string $model = Zone::class;

    protected static ?string $navigationIcon = 'phosphor-map-pin-simple-area';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?string $pluralLabel = 'Zonas';
    protected static ?string $modelLabel = 'Zona';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Zonas');
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
                    ->relationship('parentZone', 'name')
                    ->label('Zona Mãe')
                    ->nullable()
                    ->searchable(),

                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

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

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('warehouse.name')
                    ->label('Armazém')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('parentZone.name')
                    ->label('Zona Mãe')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Não se aplica')
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

                TextColumn::make('remover.name')
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
                    ->options(Zone::pluck('name', 'name')->toArray()),

                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('removed_by')
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
            'index' => Pages\ListZones::route('/'),
            'create' => Pages\CreateZone::route('/create'),
            'edit' => Pages\EditZone::route('/{record}/edit'),
        ];
    }
}