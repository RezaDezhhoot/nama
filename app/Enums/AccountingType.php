<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum AccountingType: string
{
    use EnumHelpers , HasLabel;
    case REQUEST = "request";
    case REPORT = "report";


    public function label()
    {
        return match ($this) {
            self::REPORT => "گزارش",
            self::REQUEST => "درخواست",
        };
    }
}
