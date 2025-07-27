<?php

namespace App\Filament\Admin\Resources\RoomLocationResource\Pages;

use App\Filament\Admin\Resources\RoomLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomLocation extends EditRecord
{
    protected static string $resource = RoomLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
