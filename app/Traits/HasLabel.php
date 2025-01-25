<?php

namespace App\Traits;

trait HasLabel
{
    /**
     * Returns enum values as an array.
     */
    static function labels(): array
    {
        $values = [];

        foreach (self::cases() as $index => $enumCase) {
            $values[$enumCase->value] = $enumCase->label() ?? $enumCase->name;
        }

        return $values;
    }

    abstract public function label();
}
