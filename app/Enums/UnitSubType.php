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

    public static function classed(): array
    {
        return [
            UnitType::MOSQUE->value => [
                self::BROTHERS->value => self::BROTHERS->label(),
                self::SISTERS->value => self::SISTERS->label(),
            ],
            UnitType::SCHOOL->value => [
                self::MALE->value => self::MALE->label(),
                self::FEMALE->value => self::FEMALE->label(),
                self::SUPPORT->value => self::SUPPORT->label(),
            ],
            UnitType::CENTER->value => [],
            UnitType::UNIVERSITY->value => [],
        ];
    }

}
