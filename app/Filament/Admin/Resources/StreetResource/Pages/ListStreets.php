<?php

namespace App\Filament\Admin\Resources\StreetResource\Pages;

use App\Filament\Admin\Resources\StreetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStreets extends ListRecords
{
    protected static string $resource = StreetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
