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
    case GARDEN = 'garden';
    case HALL = 'hall';
    case STADIUM = 'stadium';
    case DARUL_QURAN = 'darul_quran';
    case CULTURAL_INSTITUTE = 'cultural_institute';
    case SEMINARY = 'seminary';
    case QURANIC_CENTER = 'quranic_center';

    public function label()
    {
        return match ($this) {
            self::SCHOOL => 'مدرسه',
            self::MOSQUE => 'مسجد',
            self::CENTER => 'مرکز تعالی',
            self::UNIVERSITY => 'دانشگاه',
            self::GARDEN => 'بوستان',
            self::HALL => 'سرا',
            self::STADIUM => 'ورزشگاه',
            self::DARUL_QURAN => 'دارالقرآن',
            self::CULTURAL_INSTITUTE => 'موسسه فرهنگی',
            self::SEMINARY => 'حوزه علمیه',
            self::QURANIC_CENTER => 'مرکز قرآنی',
        };
    }

    public static function subTypes($v): array
    {
        $v = $v instanceof UnitType ? $v : UnitType::tryFrom($v);
        return match ($v) {
            self::MOSQUE => [
                UnitSubType::BROTHERS, UnitSubType::SISTERS
            ],
            self::SCHOOL => [
                UnitSubType::MALE, UnitSubType::FEMALE , UnitSubType::SUPPORT
            ],
            self::QURANIC_CENTER => [
                UnitSubType::MOSQUE_SISTERS , UnitSubType::MOSQUE_BROTHERS , UnitSubType::GIRLS_SCHOOL , UnitSubType::BOYS_SCHOOL , UnitSubType::DAR_AL_QURAN_BROTHERS , UnitSubType::DAR_AL_QURAN_SISTERS ,
                UnitSubType::NEIGHBORHOOD_CENTER_SISTERS , UnitSubType::NEIGHBORHOOD_CENTER_BROTHERS , UnitSubType::HOME_SESSIONS_SISTERS , UnitSubType::HOME_SESSIONS_BROTHERS
            ],
            default => []
        };
    }
}
