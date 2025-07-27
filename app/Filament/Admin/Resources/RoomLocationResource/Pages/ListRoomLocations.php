<?php

namespace App\Filament\Admin\Resources\RoomLocationResource\Pages;

use App\Filament\Admin\Resources\RoomLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomLocations extends ListRecords
{
    protected static string $resource = RoomLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
