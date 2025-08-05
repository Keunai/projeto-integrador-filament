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

    protected function afterCreate(): void
    {
        /** @var Movement $movement */
        $movement = $this->record;

        $product = Product::find($movement->product_id);

        if (!$movement->origin_loc_type || !$movement->origin_loc_id) {
            $movement->origin_loc_type = $product->locationable_type;
            $movement->origin_loc_id = $product->locationable_id;
            $movement->save();
        }

        if ($movement->type === 'exit') {
            $product->amount = max(0, $product->amount - ($movement->amount ?? 1));
            $product->save();
        }

        if ($movement->type === 'intern' && $movement->destinyLoc) {
            $product->locationable_type = get_class($movement->destinyLoc);
            $product->locationable_id = $movement->destinyLoc->id;
            $product->save();
        }
    }
}
