<?php

namespace App\Filament\Admin\Resources\ColumnResource\Pages;

use App\Filament\Admin\Resources\ColumnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColumn extends EditRecord
{
    protected static string $resource = ColumnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
