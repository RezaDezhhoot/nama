<?php

namespace App\Traits;

trait EnumHelpers
{
    static function values(): array
    {
        $values = [];

        foreach (self::cases() as $enumCase) {
            $values[$enumCase->value] = $enumCase->value;
        }

        return $values;
    }
}
