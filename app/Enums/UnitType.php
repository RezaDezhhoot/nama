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
    case UNIVERSITY = 'university';

    public function label()
    {
        return match ($this) {
            self::SCHOOL => 'مدرسه',
            self::MOSQUE => 'مسجد',
            self::CENTER => 'مرکز تعالی',
            self::UNIVERSITY => 'دانشگاه',
        };
    }

    public static function subTypes($v)
    {
        $v = $v instanceof UnitType ? $v : UnitType::tryFrom($v);
        return match ($v) {
            self::MOSQUE => [
                UnitSubType::BROTHERS, UnitSubType::SISTERS
            ],
            self::SCHOOL => [
                UnitSubType::MALE, UnitSubType::FEMALE , UnitSubType::SUPPORT
            ],
            default => []
        };
    }
}
