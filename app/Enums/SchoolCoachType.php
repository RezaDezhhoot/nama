<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum SchoolCoachType: string
{
    use EnumHelpers , HasLabel;
    case COACH = "coach";
    case AMIN = "amin";
    case SOLDER = "solder";
    case ARMAN_COACH = "arman_coach";

    public function label()
    {
        return match ($this) {
            self::COACH => 'طلبه معلم',
            self::AMIN => 'طلبه امین',
            self::SOLDER => 'سرباز طلبه',
            self::ARMAN_COACH => 'مربی آرمانی',
        };
    }
}
