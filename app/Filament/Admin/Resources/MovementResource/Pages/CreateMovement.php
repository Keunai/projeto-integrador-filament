<?php

namespace App\Filament\Admin\Resources\MovementResource\Pages;

use App\Filament\Admin\Resources\MovementResource;
use App\Models\Movement;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMovement extends CreateRecord
{
    protected static string $resource = MovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::find($data['product_id']);

        dd($product->locationable_type, $product->locationable_id);

        $data['origin_loc_type'] = $product->locationable_type;
        $data['origin_loc_id'] = $product->locationable_id;

        return $data;
    }
}
