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

    case MOSQUE_SISTERS = 'mosque_sisters';
    case MOSQUE_BROTHERS = 'mosque_brothers';

    case GIRLS_SCHOOL = 'girls_school';
    case BOYS_SCHOOL = 'boys_school';

    case DAR_AL_QURAN_BROTHERS = 'dar_al_quran_brothers';
    case DAR_AL_QURAN_SISTERS = 'dar_al_quran_sisters';

    case NEIGHBORHOOD_CENTER_SISTERS = 'neighborhood_center_sisters';
    case NEIGHBORHOOD_CENTER_BROTHERS = 'neighborhood_center_brothers';

    case HOME_SESSIONS_SISTERS = 'home_sessions_sisters';
    case HOME_SESSIONS_BROTHERS = 'home_sessions_brothers';

    public function label()
    {
        return match ($this) {
            self::BROTHERS => 'برادران',
            self::SISTERS => 'خواهران',
            self::MALE => 'پسرانه',
            self::FEMALE => 'دخترانه',
            self::SUPPORT => 'حمایتی',

            self::MOSQUE_SISTERS => 'مرکز قرآنی مسجد خواهران',
            self::MOSQUE_BROTHERS => 'مرکز قرآنی مسجد برادران',

            self::GIRLS_SCHOOL => 'مرکز قرآنی مدرسه دخترانه',
            self::BOYS_SCHOOL => 'مرکز قرآنی مدرسه پسرانه',

            self::DAR_AL_QURAN_BROTHERS => 'مرکز قرآنی دارالقرآن برادران',
            self::DAR_AL_QURAN_SISTERS => 'مرکز قرآنی دارالقرآن خواهران',

            self::NEIGHBORHOOD_CENTER_SISTERS => 'مرکز قرآنی سرای محله خواهران',
            self::NEIGHBORHOOD_CENTER_BROTHERS => 'مرکز قرآنی سرای محله برادران',

            self::HOME_SESSIONS_SISTERS => 'مرکز قرآنی جلسات خانگی خواهران',
            self::HOME_SESSIONS_BROTHERS => 'مرکز قرآنی جلسات خانگی برادران',
        };
    }

    public function parent(): ?UnitType
    {
        return match ($this){
            self::BROTHERS, self::SISTERS => UnitType::MOSQUE,
            self::MALE, self::FEMALE, self::SUPPORT => UnitType::SCHOOL,
            self::MOSQUE_SISTERS , self::MOSQUE_BROTHERS , self::GIRLS_SCHOOL , self::BOYS_SCHOOL , self::DAR_AL_QURAN_BROTHERS , self::DAR_AL_QURAN_SISTERS ,
            self::NEIGHBORHOOD_CENTER_SISTERS , self::NEIGHBORHOOD_CENTER_BROTHERS , self::HOME_SESSIONS_SISTERS , self::HOME_SESSIONS_BROTHERS => UnitType::QURANIC_CENTER,
            default => null,
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
            UnitType::QURANIC_CENTER->value => [
                self::MOSQUE_SISTERS->value => self::MOSQUE_SISTERS->label(),
                self::MOSQUE_BROTHERS->value => self::MOSQUE_BROTHERS->label(),
                self::GIRLS_SCHOOL->value => self::GIRLS_SCHOOL->label(),
                self::BOYS_SCHOOL->value => self::BOYS_SCHOOL->label(),
                self::DAR_AL_QURAN_BROTHERS->value => self::DAR_AL_QURAN_BROTHERS->label(),
                self::DAR_AL_QURAN_SISTERS->value => self::DAR_AL_QURAN_SISTERS->label(),
                self::NEIGHBORHOOD_CENTER_SISTERS->value => self::NEIGHBORHOOD_CENTER_SISTERS->label(),
                self::NEIGHBORHOOD_CENTER_BROTHERS->value => self::NEIGHBORHOOD_CENTER_BROTHERS->label(),
                self::HOME_SESSIONS_SISTERS->value => self::HOME_SESSIONS_SISTERS->label(),
                self::HOME_SESSIONS_BROTHERS->value => self::HOME_SESSIONS_BROTHERS->label(),
            ],
            UnitType::CENTER->value => [],
            UnitType::UNIVERSITY->value => [],
        ];
    }

}
