<?php

namespace App\Traits;

trait HasLabel
{
    /**
     * Returns enum values as an array.
     */
    static function labels(): array
    {
        $args = [];
        if (func_num_args() > 0) {
            $args = func_get_args();
        }
        $values = [];

        foreach (self::cases() as $index => $enumCase) {
            $values[$enumCase->value] = $enumCase->label(... $args) ?? $enumCase->name;
        }

        return $values;
    }

    abstract public function label();
}
