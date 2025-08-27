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
        $type = func_num_args() > 0 ? func_get_arg(0) : null;
        return match ($type) {
            PlanTypes::UNIVERSITY, PlanTypes::UNIVERSITY->value  , UnitType::UNIVERSITY , UnitType::UNIVERSITY->value => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'تایید مسئول تشکل',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'تایید رابط دانشگاه',
                self::APPROVAL_AREA_INTERFACE => 'تایید ناظر',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'تایید معاونت دانشجویی',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'تایید معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
            default => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'تایید سر مربی',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'تایید مسئول فرهنگی',
                self::APPROVAL_AREA_INTERFACE => 'تایید رابط منطقه',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'تایید معاونت اجرایی ',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'تایید معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
        };
    }

    public function label2()
    {
        $type = func_num_args() > 0 ? func_get_arg(0) : null;
        return match ($type) {
            PlanTypes::UNIVERSITY, PlanTypes::UNIVERSITY->value  , UnitType::UNIVERSITY , UnitType::UNIVERSITY->value => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'در انتظار تایید مسئول تشکل',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'انتظار تایید رابط دانشگاه',
                self::APPROVAL_AREA_INTERFACE => 'انتظار تایید ناظر',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'انتظار تایید معاونت دانشجویی',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'انتظار تایید معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
            default => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'در انتظار تایید سر مربی',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'در انتظار تایید مسئول فرهنگی',
                self::APPROVAL_AREA_INTERFACE => 'انتظار تایید  رابط منطقه',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'در انتظار تایید معاونت اجرایی ',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'در انتظار تایید معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
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
        $type = func_num_args() > 0 ? func_get_arg(0) : null;
        return match ($type) {
            PlanTypes::UNIVERSITY, PlanTypes::UNIVERSITY->value  , UnitType::UNIVERSITY , UnitType::UNIVERSITY->value => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'مسئول تشکل',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'رابط دانشگاه',
                self::APPROVAL_AREA_INTERFACE => 'ناظر',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'معاونت دانشجویی',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
            default => match ($this) {
                self::APPROVAL_MOSQUE_HEAD_COACH => 'سر مربی',
                self::APPROVAL_MOSQUE_CULTURAL_OFFICER => 'مسئول فرهنگی',
                self::APPROVAL_AREA_INTERFACE => 'رابط منطقه',
                self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'معاونت اجرایی ',
                self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => ' معاونت طرح و برنامه',
                self::FINISH => 'اتمام یافته',
            },
        };
    }

    public function role()
    {
        return match ($this) {
            self::APPROVAL_MOSQUE_HEAD_COACH => [OperatorRole::MOSQUE_HEAD_COACH],
            self::APPROVAL_MOSQUE_CULTURAL_OFFICER => [OperatorRole::MOSQUE_CULTURAL_OFFICER],
            self::APPROVAL_AREA_INTERFACE => [OperatorRole::AREA_INTERFACE],
            self::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES => [OperatorRole::EXECUTIVE_VICE_PRESIDENT_MOSQUES ],
            self::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [OperatorRole::DEPUTY_FOR_PLANNING_AND_PROGRAMMING],
            default => []
        };
    }
}
