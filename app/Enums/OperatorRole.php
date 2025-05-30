<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum OperatorRole: string
{
    use EnumHelpers , HasLabel;

    case MOSQUE_HEAD_COACH = 'mosque_head_coach';
    case MOSQUE_CULTURAL_OFFICER = 'mosque_cultural_officer';

    case AREA_INTERFACE = 'area_interface';

    case EXECUTIVE_VICE_PRESIDENT_MOSQUES = 'executive_vice_president_mosques';

    case DEPUTY_FOR_PLANNING_AND_PROGRAMMING = 'deputy_for_planning_and_programming';

    public function label(): string
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => 'سرمربی',
            self::MOSQUE_CULTURAL_OFFICER => 'مسئول فرهنگی',
            self::AREA_INTERFACE => 'رابط منطقه',
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'معاونت اجرایی',
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'معاونت طرح و برنامه'
        };
    }

    public function step(): array
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => [RequestStep::APPROVAL_MOSQUE_HEAD_COACH],
            self::MOSQUE_CULTURAL_OFFICER => [RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER ],
            self::AREA_INTERFACE => [RequestStep::APPROVAL_AREA_INTERFACE  ],
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => [RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES ],
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ],
            default => []
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => 'danger',
            self::MOSQUE_CULTURAL_OFFICER => 'warning',
            self::AREA_INTERFACE => 'info',
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => 'success',
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => 'primary',
            default => []
        };
    }

    public function relatedSteps(): array
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => [RequestStep::APPROVAL_MOSQUE_HEAD_COACH],
            self::MOSQUE_CULTURAL_OFFICER => [RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER ],
            self::AREA_INTERFACE => [RequestStep::APPROVAL_AREA_INTERFACE  ],
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => [RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES ],
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING ],
            default => []
        };
    }

    public function history(): array
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => [RequestStep::APPROVAL_MOSQUE_HEAD_COACH],
            self::MOSQUE_CULTURAL_OFFICER => [RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER,RequestStep::APPROVAL_AREA_INTERFACE,RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::AREA_INTERFACE => [RequestStep::APPROVAL_AREA_INTERFACE,RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => [RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            default => []
        };
    }

    public function next(): array
    {
        return match ($this) {
            self::MOSQUE_HEAD_COACH => [RequestStep::APPROVAL_MOSQUE_CULTURAL_OFFICER , RequestStep::APPROVAL_AREA_INTERFACE,RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::MOSQUE_CULTURAL_OFFICER => [RequestStep::APPROVAL_AREA_INTERFACE,RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::AREA_INTERFACE => [RequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES,RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => [RequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING,RequestStep::FINISH],
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [RequestStep::FINISH],
            default => []
        };
    }

    public function writtenStep(): array
    {
        return match ($this) {
            self::EXECUTIVE_VICE_PRESIDENT_MOSQUES => [WrittenRequestStep::APPROVAL_EXECUTIVE_VICE_PRESIDENT_MOSQUES],
            self::DEPUTY_FOR_PLANNING_AND_PROGRAMMING => [WrittenRequestStep::APPROVAL_DEPUTY_FOR_PLANNING_AND_PROGRAMMING],
            default => []
        };
    }
}
