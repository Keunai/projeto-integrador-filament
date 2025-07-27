<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Função';
    protected static ?string $pluralLabel = 'Funções';
    protected static ?string $navigationIcon = 'sui-hierarchy';

    protected static ?string $navigationGroup = 'Configurações';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Funções');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('permissions')
                    ->label('Permissões')
                    ->required()
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->pluralModelLabel('Funções')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->summarize(Count::make()),

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
                    ->options(Role::pluck('name', 'name')->toArray()),

                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('removed_by')
                    ->label('Removido Por')
                    ->options(User::pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function getModel(): string
    {
        return Role::class;
    }
}