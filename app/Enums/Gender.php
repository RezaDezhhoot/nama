<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum Gender: string
{
    use EnumHelpers , HasLabel;

    case MALE = 'male';
    case FEMALE = 'female';
    case BOTH = 'both';

    public function label()
    {
        return match ($this) {
            self::MALE => 'پسرانه',
            self::FEMALE => 'دخترانه',
            self::BOTH => 'هر دو',
        };
    }
}
