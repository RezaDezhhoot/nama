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

    public function label()
    {
        // TODO: Implement label() method.
    }
}
