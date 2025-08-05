<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Imports\ProductImporter;
use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Bin;
use App\Models\Category;
use App\Models\Product;
use App\Models\RoomLocation;
use App\Models\Status;
use App\Models\User;
use App\Enums\ProductTypes;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'bi-box';
    protected static ?string $navigationLabel = 'Produtos';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Operações';
    protected static ?string $pluralLabel = 'Produtos';
    protected static ?string $modelLabel = 'Produto';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('Gerenciar Produtos');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Hidden::make('created_by')->default(fn() => auth()->id()),
                Hidden::make('updated_by')->default(fn() => auth()->id()),
                Hidden::make('deleted_by'),

                Select::make('type')
                    ->label('Tipo de Registro')
                    ->options(ProductTypes::getDescriptiveValues())
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === \App\Enums\ProductTypes::UNIT) {
                            $set('amount', 1);
                        }
                    }),

                MorphToSelect::make('locationable')
                    ->types([
                        MorphToSelect\Type::make(\App\Models\Bin::class)
                            ->label('Bloco')
                            ->titleAttribute('name')
                            ->getOptionsUsing(fn () => \App\Models\Bin::query()->where('is_full', 0)->pluck('name', 'id')),

                        MorphToSelect\Type::make(\App\Models\RoomLocation::class)
                            ->label('Local de Sala')
                            ->titleAttribute('description'),
                    ])
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Localização da Unidade' : 'Localização do Lote')
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Categoria da Unidade' : 'Categoria do Lote')
                    ->required()
                    ->searchable(),

                TextInput::make('code')
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Código Único da Unidade' : 'Código Único do Lote')
                    ->placeholder(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Ex: UN12345' : 'Ex: LOTE-2025-001')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('name')
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Nome da Unidade' : 'Nome do Lote')
                    ->placeholder('Opcional')
                    ->maxLength(255),

                TextInput::make('amount')
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Quantidade' : 'Quantidade Total de Unidades no Lote')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 1 : null)
                    ->disabled(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT)
                    ->default(1),

                Textarea::make('description')
                    ->label(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::UNIT ? 'Descrição da Unidade' : 'Descrição do Lote')
                    ->placeholder('Descreva características adicionais...')
                    ->maxLength(65535),

                Repeater::make('units')
                    ->label('Detalhes das Unidades')
                    ->relationship('units')
                    ->rules([
                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                            $amount = request()->input('amount') ?? 1;
                            if (is_array($value) && count($value) > (int) $amount) {
                                $fail("Você não pode adicionar mais unidades do que o total definido no campo Quantidade Total de Unidades no Lote");
                            }
                        },
                    ])
                    ->schema([
                        Hidden::make('created_by')->default(fn () => auth()->id()),
                        Hidden::make('updated_by')->default(fn () => auth()->id()),
                        Hidden::make('deleted_by'),

                        Grid::make(2)->schema([
                            Select::make('status_id')
                                ->label('Status da Unidade')
                                ->relationship('status', 'name')
                                ->placeholder('Opcional')
                                ->searchable(),

                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->label('Categoria da Unidade')
                                ->required()
                                ->searchable()
                                ->afterStateHydrated(function (callable $get, callable $set) {
                                    if (! $get('category_id')) {
                                        $set('category_id', $get('../../category_id'));
                                    }
                                })
                        ]),

                        TextInput::make('name')
                            ->label('Nome da Unidade')
                            ->placeholder('Opcional')
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Código Único da Unidade')
                            ->required()
                            ->unique(
                                table: 'products',
                                column: 'code',
                                ignoreRecord: true
                            )
                            ->maxLength(255),

                        MorphToSelect::make('locationable')
                            ->types([
                                MorphToSelect\Type::make(Bin::class)
                                    ->label('Bloco')
                                    ->titleAttribute('name')
                                    ->getOptionsUsing(fn () => \App\Models\Bin::query()->where('is_full', 0)->pluck('name', 'id')),

                                MorphToSelect\Type::make(RoomLocation::class)
                                    ->label('Local de Sala')
                                    ->titleAttribute('description'),
                            ])
                            ->label('Localização da Unidade')
                            ->afterStateHydrated(function (callable $get, callable $set) {
                                if (!$get('locationable_type') && !$get('locationable_id')) {
                                    $set('locationable_type', $get('../../locationable_type'));
                                    $set('locationable_id', $get('../../locationable_id'));
                                }
                            }),

                        Textarea::make('description')
                            ->label('Descrição da Unidade')
                            ->placeholder('Descreva características adicionais...')
                            ->maxLength(65535),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->defaultItems(fn (callable $get) => (int) ($get('amount') ?? 0))
                    ->reorderable(false)
                    ->visible(fn (callable $get) => $get('type') === \App\Enums\ProductTypes::BATCH),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(ProductImporter::class)
                    ])
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo de Registro')
                    ->sortable()
                    ->formatStateUsing(fn($state) => ProductTypes::getDescriptiveValues()[$state] ?? $state),

                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('code')
                    ->label('Código')
                    ->sortable()
                    ->summarize(Count::make())
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Qtd. (Lote)')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) =>
                    $record->type === ProductTypes::BATCH ? $state : 'Não se aplica'
                    ),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->visible(fn($record) => $record && $record->type === ProductTypes::BATCH)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('locationable.name')
                    ->label('Localização')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->placeholder('Vazio')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

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

                TextColumn::make('deleter.name')
                    ->label('Removido Por')
                    ->placeholder('Não se aplica')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Data de Remoção')
                    ->placeholder('Não se aplica')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo de Registro')
                    ->default(null)
                    ->options([
                        ProductTypes::BATCH => 'Apenas Lotes',
                        ProductTypes::UNIT => 'Apenas Unidades',
                    ])
                    ->indicator('Tipo'),

                SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->options(Category::pluck('name', 'id')->toArray()),

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
                Tables\Actions\ForceDeleteBulkAction::make()->label('Exclusão definitiva'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
