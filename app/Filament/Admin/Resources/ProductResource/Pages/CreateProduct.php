<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Bin;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Movement;
use App\Enums\MovementTypes;
use Illuminate\Validation\ValidationException;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $product = $this->record->fresh('locationable');


        if ($product->type === \App\Enums\ProductTypes::BATCH && $product->units()->exists()) {
            foreach ($product->units as $unit) {
                if (
                    $unit->locationable_type !== $product->locationable_type ||
                    $unit->locationable_id !== $product->locationable_id
                ) {
                    Movement::create([
                        'product_id'       => $unit->id,
                        'type'             => MovementTypes::ENTRY,
                        'origin_loc_type'  => null,
                        'origin_loc_id'    => null,
                        'destiny_loc_type' => $unit->locationable_type,
                        'destiny_loc_id'   => $unit->locationable_id,
                        'created_by'       => auth()->id(),
                        'updated_by'       => auth()->id(),
                    ]);
                }
            }
        } else {
            Movement::create([
                'product_id'       => $product->id,
                'type'             => MovementTypes::ENTRY,
                'origin_loc_type'  => null,
                'origin_loc_id'    => null,
                'destiny_loc_type' => $product->locationable_type,
                'destiny_loc_id'   => $product->locationable_id,
                'created_by'       => auth()->id(),
                'updated_by'       => auth()->id(),
            ]);
        }
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        if (
            $data['locationable_type'] === Bin::class &&
            !empty($data['locationable_id'])
        ) {
            $bin = Bin::find($data['locationable_id']);

            if ($bin?->is_full) {
                throw ValidationException::withMessages([
                    'locationable_id' => 'Este bloco está cheio e não pode ser selecionado',
                    'locationable' => 'Este bloco está cheio e não pode ser selecionado',
                ]);
            }
        }
    }
}