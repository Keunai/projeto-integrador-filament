<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $navigationIcon = 'bi-people';
    protected static ?string $navigationGroup = 'Configurações';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Usuários');
    }

    public static function form(Form $form): Form
    {
        $isCreate = fn(string $context): bool => $context === 'create';
        $isEdit = fn(string $context): bool => $context === 'edit';

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('E‑mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('roles')
                    ->label('Funções')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->searchable(),

                Forms\Components\Checkbox::make('active')
                    ->label('Ativo'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label(fn(string $context): string => $isCreate($context) ? 'Senha' : 'Nova Senha')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required($isCreate)
                    ->visible(fn(string $context): bool => $isCreate($context) || $isEdit($context)),

                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label(fn(string $context): string => $isCreate($context) ? 'Confirmar Senha' : 'Confirmar Nova Senha')
                    ->same('password')
                    ->dehydrated(false)
                    ->required($isCreate)
                    ->visible(fn(string $context): bool => $isCreate($context) || $isEdit($context)),

                Forms\Components\Hidden::make('created_by'),
                Forms\Components\Hidden::make('updated_by'),
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

                TextColumn::make('roles.0.name')
                    ->label('Função')
                    ->badge(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->summarize(Count::make())
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\CheckboxColumn::make('active')
                    ->label('Ativo')
                    ->sortable(),

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
                    ->options(User::pluck('name', 'id')->toArray()),

                SelectFilter::make('roles.name')
                    ->label('Função')
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

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}