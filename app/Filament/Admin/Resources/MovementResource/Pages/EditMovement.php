<?php

namespace App\Filament\Admin\Resources\MovementResource\Pages;

use App\Filament\Admin\Resources\MovementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovement extends EditRecord
{
    protected static string $resource = MovementResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
