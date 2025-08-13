<?php

namespace App\Filament\Imports;

use App\Models\Bin;
use App\Models\Category;
use App\Models\Product;
use App\Enums\ProductTypes;
use App\Models\RoomLocation;
use App\Models\Status;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('type')->requiredMapping()->label('Tipo'),
            ImportColumn::make('code')->requiredMapping()->label('Código'),
            ImportColumn::make('name')->requiredMapping()->label('Nome'),
            ImportColumn::make('amount')->numeric()->label('Quantidade'),
            ImportColumn::make('description')->label('Descrição'),
            ImportColumn::make('category')
                ->label('Categoria')
                ->relationship('category', 'name'),
            ImportColumn::make('locationable')
                ->label('Localização')
                ->requiredMapping()
                ->relationship('locationable', 'name'),
            ImportColumn::make('status')
                ->label('Status')
                ->relationship('status', 'name'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        try {
            $category = Category::where('name', $this->data['category'] ?? null)->first();
            $status = Status::where('name', $this->data['status'] ?? null)->first();

            $locationable = Bin::where('name', $this->data['locationable'] ?? null)->first()
                ?? RoomLocation::where('name', $this->data['locationable'] ?? null)->first();

            if (! $category) {
                throw new RowImportFailedException("Categoria '{$this->data['category']}' não encontrada.");
            }

            if (! $locationable) {
                throw new RowImportFailedException("Localização '{$this->data['locationable']}' não encontrada.");
            }

            $product = Product::firstOrNew([
                'code' => $this->data['code'],
            ]);

            $product->type = $this->data['type'];
            $product->name = $this->data['name'];
            $product->amount = $this->data['amount'] ?? 1;
            $product->description = $this->data['description'] ?? null;
            $product->category_id = $category->id;
            $product->locationable_type = get_class($locationable);
            $product->locationable_id = $locationable->id;
            $product->status_id = $status?->id ?? null;
            $product->created_by = auth()->id();
            $product->updated_by = auth()->id();

            return $product;

        } catch (\Throwable $e) {
            throw new RowImportFailedException(
                "Erro ao importar produto '{$this->data['code']}': " . $e->getMessage()
            );
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Foram importados {$import->successful_rows} produtos com sucesso.";
    }
}