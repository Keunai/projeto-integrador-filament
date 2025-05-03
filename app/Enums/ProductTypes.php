<?php

namespace App\Enums;

use App\Concerns\Enum;

class ProductTypes implements Enum
{
    public const UNIT = 'unit';
    public const BATCH = 'batch';

    public static function getDescriptiveValues(): array
    {
        return [
            self::UNIT => 'Unidade',
            self::BATCH => 'Lote',
        ];
    }

}