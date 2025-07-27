<?php

namespace App\Filament\Admin\Resources\ShelfResource\Pages;

use App\Filament\Admin\Resources\ShelfResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShelves extends ListRecords
{
    protected static string $resource = ShelfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
