<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MovementResource\Pages\ListMovements;
use App\Models\Bin;
use App\Models\Movement;
use App\Enums\MovementTypes;
use App\Models\RoomLocation;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;

class MovementResource extends Resource
{
    protected static ?string $model = Movement::class;

    protected static ?string $navigationIcon = 'carbon-asset-movement';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Operações';
    protected static ?string $modelLabel = 'Movimentação';
    protected static ?string $navigationLabel = 'Movimentações';
    protected static ?string $pluralModelLabel = 'Movimentações';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Movimentações');
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Hidden::make('created_by')->default(fn () => auth()->id()),
            Hidden::make('updated_by')->default(fn () => auth()->id()),
            Hidden::make('origin_loc_type'),
            Hidden::make('origin_loc_id'),

            Select::make('product_id')
                ->relationship('product', 'code')
                ->label('Produto')
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    $product = \App\Models\Product::find($state);

                    if ($product) {
                        $set('origin_loc_type', $product->locationable_type);
                        $set('origin_loc_id', $product->locationable_id);
                    }
                }),

            Select::make('type')
                ->label('Tipo de Movimentação')
                ->options([
                    'intern' => 'Interna',
                    'exit' => 'Baixa',
                ])
                ->required()
                ->reactive(),

            MorphToSelect::make('destinyLoc')
                ->types([
                    MorphToSelect\Type::make(Bin::class)
                        ->label('Bloco')
                        ->titleAttribute('name')
                        ->getOptionsUsing(fn () => \App\Models\Bin::query()->where('is_full', false)->pluck('name', 'id')),

                    MorphToSelect\Type::make(RoomLocation::class)
                        ->label('Local de Sala')
                        ->titleAttribute('description'),
                ])
                ->label('Localização de Destino')
                ->visible(fn (callable $get) => $get('type') !== 'exit')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.code')
                    ->label('Código do Produto')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('originLoc.name')
                    ->label('Loc. Origem')
                    ->placeholder('Não se aplica')
                    ->sortable(),

                TextColumn::make('destinyLoc.name')
                    ->label('Loc. Destino')
                    ->sortable()
                    ->placeholder('Não se aplica'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'entry' => 'success',
                        'intern' => 'warning',
                        'exit' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => MovementTypes::getDescriptiveValues()[$state] ?? $state),

                TextColumn::make('creator.name')
                    ->label('Criado Por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Alterado Por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovements::route('/'),
        ];
    }
}