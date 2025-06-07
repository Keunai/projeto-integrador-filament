<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $navigationIcon = 'bi-people';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('Gerenciar Usuários');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Select::make('role_id')
                    ->label('Função')
                    ->required()
                    ->options(Role::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload(),

                Checkbox::make('active')
                    ->label('Ativo')
                    ->inline(false),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Senha')
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed()
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label('Confirme a Senha')
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(false),

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
                    ->sortable(),

                SelectColumn::make('role_id')
                    ->label('Função')
                    ->sortable()
                    ->options(Role::pluck('name', 'id')->toArray())
                    ->summarize(Count::make()),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                CheckboxColumn::make('active')
                    ->label('Ativo')
                    ->sortable(),

                TextColumn::make('updated_by')
                    ->label('Alterado Por')
                    ->placeholder('Ubiko')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Última Alteração')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_by')
                    ->label('Criado Por')
                    ->sortable()
                    ->placeholder('Ubiko')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->options(User::pluck('name', 'id')->toArray()),
                SelectFilter::make('role.name')
                    ->label('Função')
                    ->options(Role::pluck('name', 'id')->toArray()),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
