<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum FormReportEnum: string
{
    use HasLabel , EnumHelpers;
    case PENDING = "pending";
    case DONE = "done";
    case ACTION_NEEDED = "action_needed";

    public function label()
    {
        return match ($this) {
            self::PENDING => "در حال بررسی",
            self::DONE => "تایید شده",
            self::ACTION_NEEDED => "نیازمند اصلاح",
        };
    }
}
