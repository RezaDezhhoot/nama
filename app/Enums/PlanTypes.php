<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum PlanTypes: string
{
    use HasLabel , EnumHelpers;

    case DEFAULT = "default";
    case UNIVERSITY = "university";

    public function label()
    {
        return match ($this) {
            self::DEFAULT   => "عادی",
            self::UNIVERSITY   => "دانشجویی",
        };
    }
}
