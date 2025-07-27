<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StatusResource\Pages;
use App\Models\Status;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;

    protected static ?string $navigationIcon = 'hugeicons-loading-04';
    protected static ?string $navigationLabel = 'Status de Produtos';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 4;
    protected static ?string $pluralLabel = 'Status';
    protected static ?string $modelLabel = 'Status';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Status');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descrição')
                    ->maxLength(65535)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->options(Status::pluck('name', 'name')->toArray()),

                SelectFilter::make('created_by')
                    ->label('Criado Por')
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('updated_by')
                    ->label('Alterado Por')
                    ->placeholder('Não se aplica')
                    ->options(User::pluck('name', 'id')->toArray()),

            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatuses::route('/'),
            'create' => Pages\CreateStatus::route('/create'),
            'edit' => Pages\EditStatus::route('/{record}/edit'),
        ];
    }
}