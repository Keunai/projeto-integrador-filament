<?php

namespace App\Enums;

use App\Concerns\Enum;

class MovementTypes implements Enum
{
    public const ENTRY = 'entry';
    public const EXIT = 'exit';
    public const INTERN = 'intern';

    public static function getDescriptiveValues(): array
    {
        return [
            self::ENTRY => 'Entrada',
            self::EXIT => 'Saída',
            self::INTERN => 'Interna',
        ];
    }

}
