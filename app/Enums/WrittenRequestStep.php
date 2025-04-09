<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum WrittenRequestStep: string
{
    use EnumHelpers , HasLabel;

    case APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES = 'approval_executive_vice_president_mosques';

    case APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING = 'approval_deputy_for_planning_and_programming';
    case APPROVAL_ARMAN_BUS = 'approval_arman_bus';

    case FINISH = 'finish';

    public function label()
    {
        return match ($this) {
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'تایید معاونت اجرایی',
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'تایید معاونت طرح و برنامه',
            self::APPROVAL_ARMAN_BUS => 'تایید ریاست ستاد آرمان',
            self::FINISH => 'اتمام یافته',
        };
    }
}
