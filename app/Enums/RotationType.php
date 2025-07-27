<?php

namespace App\Enums;

use App\Concerns\Enum;

class RotationType implements Enum
{
    public const DAY = 'day';
    public const WEEK = 'week';
    public const MONTH = 'month';
    public const YEAR = 'year';

    public static function getDescriptiveValues(): array
    {
        return [
            self::DAY => 'Dia',
            self::WEEK => 'Semana',
            self::MONTH => 'Mês',
            self::YEAR => 'Ano',
        ];
    }
}