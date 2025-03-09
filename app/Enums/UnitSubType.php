<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum UnitSubType: string
{
    use EnumHelpers , HasLabel;
    case BROTHERS = 'brothers';
    case SISTERS = 'sisters';

    case MALE = 'male';
    case FEMALE = 'female';
    case SUPPORT = 'support';

    public function label()
    {
        return match ($this) {
            self::BROTHERS => 'برادران',
            self::SISTERS => 'خواهران',
            self::MALE => 'پسرانه',
            self::FEMALE => 'دخترانه',
            self::SUPPORT => 'حمایتی',
        };
    }


}
