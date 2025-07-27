<?php

namespace App\Filament\Admin\Resources\ColumnResource\Pages;

use App\Filament\Admin\Resources\ColumnResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateColumn extends CreateRecord
{
    protected static string $resource = ColumnResource::class;
}
