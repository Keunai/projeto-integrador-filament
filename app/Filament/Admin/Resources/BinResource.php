<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BinResource\Pages;
use App\Models\Bin;
use App\Models\User;
use App\Models\Column;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BinResource extends Resource
{
    protected static ?string $model = Bin::class;

    protected static ?string $navigationIcon = 'css-box';
    protected static ?string $navigationLabel = 'Blocos';
    protected static ?string $pluralLabel = 'Blocos';
    protected static ?string $modelLabel = 'Bloco';
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Funções');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('created_by')->default(fn () => auth()->id()),
                Hidden::make('updated_by')->default(fn () => auth()->id()),
                Hidden::make('deleted_by'),

                Select::make('column_id')
                    ->relationship('column', 'name')
                    ->label('Coluna')
                    ->required()
                    ->searchable(),

                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->unique()
                    ->maxLength(255),

                TextInput::make('level')
                    ->label('Nível')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Toggle::make('is_full')
                    ->label('Está cheio?')
                    ->inline(false),

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

                TextColumn::make('level')
                    ->label('Nível')
                    ->sortable(),

                IconColumn::make('is_full')
                    ->label('Cheio?')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('column.name')
                    ->label('Coluna')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
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
                    ->options(Bin::pluck('name', 'name')->toArray()),

                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('deleted_by')
                    ->label('Removido Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('column_id')
                    ->label('Coluna')
                    ->options(Column::pluck('name', 'id')->toArray()),

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
            'index' => Pages\ListBins::route('/'),
            'create' => Pages\CreateBin::route('/create'),
            'edit' => Pages\EditBin::route('/{record}/edit'),
        ];
    }
}