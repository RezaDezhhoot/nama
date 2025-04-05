<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum RequestStep: string
{
    use EnumHelpers , HasLabel;

    case APPROVAL_MOSQUE_HEAD_COACH = 'approval_mosque_head_coach';
    case APPROVAL_MOSQUE_CULTURAL_OFFICER = 'approval_mosque_cultural_officer';

    case APPROVAL_AREA_INTERFACE = 'approval_area_interface';

    case APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES = 'approval_executive_vice_president_mosques';

    case APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING = 'approval_deputy_for_planning_and_programming';

    case FINISH = 'finish';

    public function label()
    {
        return match ($this) {
            self::APPROVAL_MOSQUE_HEAD_COACH => 'تایید سر مربی',
            self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'تایید مسئول فرهنگی',
            self::APPROVAL_AREA_INTERFACE => 'تایید  رابط منطقه',
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'تایید معاونت اجرایی مساجد',
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'تایید معاونت طرح و برنامه',
            self::FINISH => 'اتمام یافته',
        };
    }

    public function label2()
    {
        return match ($this) {
            self::APPROVAL_MOSQUE_HEAD_COACH => 'در انتظار تایید سر مربی',
            self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'انتظار تایید مسئول فرهنگی',
            self::APPROVAL_AREA_INTERFACE => 'انتظار تایید  رابط منطقه',
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'انتظار تایید معاونت اجرایی مساجد',
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'انتظار تایید معاونت طرح و برنامه',
            self::FINISH => 'اتمام یافته',
        };
    }

    public function backSteps(): array
    {
        return match ($this) {
            self::APPROVAL_MOSQUE_HEAD_COACH => [],
            self::APPROVAL_MOSQUE_CULTURAL_OFFICER => [self::APPROVAL_MOSQUE_HEAD_COACH],
            self::APPROVAL_AREA_INTERFACE => [self::APPROVAL_MOSQUE_HEAD_COACH , self::APPROVAL_MOSQUE_CULTURAL_OFFICER],
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => [self::APPROVAL_MOSQUE_HEAD_COACH  , self::APPROVAL_MOSQUE_CULTURAL_OFFICER , self::APPROVAL_AREA_INTERFACE],
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [self::APPROVAL_MOSQUE_HEAD_COACH  , self::APPROVAL_MOSQUE_CULTURAL_OFFICER , self::APPROVAL_AREA_INTERFACE , self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES],
            default => []
        };
    }

    public function title()
    {
        return match ($this) {
            self::APPROVAL_MOSQUE_HEAD_COACH => ' سر مربی',
            self::APPROVAL_MOSQUE_CULTURAL_OFFICER => ' مسئول فرهنگی',
            self::APPROVAL_AREA_INTERFACE => '  رابط منطقه',
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => ' معاونت اجرایی مساجد',
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => ' معاونت طرح و برنامه',
            self::FINISH => 'اتمام یافته',
        };
    }
}
