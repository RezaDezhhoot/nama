<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum UnitType: string
{
    use EnumHelpers , HasLabel;
    case MOSQUE = 'mosque';
    case SCHOOL = 'school';
    case CENTER = 'center';

    public function label()
    {
        return match ($this) {
            self::SCHOOL => 'مدرسه',
            self::MOSQUE => 'مسجد',
            self::CENTER => 'مرکز تعالی'
        };
    }
}
