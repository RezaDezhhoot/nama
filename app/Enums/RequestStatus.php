<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum RequestStatus: string
{
    use EnumHelpers , HasLabel;

    case REJECTED = 'rejected';
    case IN_PROGRESS = 'in_progress';
    case ACTION_NEEDED = 'action_needed';
    case DONE = 'done';
    case PENDING = 'pending';

    public function label()
    {
        return match ($this) {
            self::DONE => 'تایید شده',
            self::REJECTED => 'رد شده',
            self::IN_PROGRESS => 'جاری',
            self::PENDING => 'باز',
            self::ACTION_NEEDED => 'نیازمند اصلاح'
        };
    }
}
