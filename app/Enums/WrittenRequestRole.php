<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum WrittenRequestRole: string
{
    use HasLabel , EnumHelpers;
    case EXECUTIVE_VICE_PRESIDENT_MOSQUES = 'executive_vice_president_mosques';

    case DEPUTY_FOR_PLANNING_AND_PROGRAMMING = 'deputy_for_planning_and_programming';
    case ARMAN_BUS = 'arman_bus';

    public function label()
    {
        return match ($this) {
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'معاونت اجرایی',
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'معاونت طرح و برنامه',
            self::ARMAN_BUS => 'ریاست ستاد ارمان',
        };
    }
}
