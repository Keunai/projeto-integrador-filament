<?php

namespace App\Filament\Admin\Resources\ShelfResource\Pages;

use App\Filament\Admin\Resources\ShelfResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShelf extends EditRecord
{
    protected static string $resource = ShelfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
