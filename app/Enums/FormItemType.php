<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum FormItemType: string
{
    use HasLabel , EnumHelpers;

    case TEXT = "text";
    case NUMBER = "number";

    case SELECT = "select";
    case SELECT2 = "select2";
    case RADIO = "radio";
    case TEXTAREA = "textarea";
    case FILE = "file";

    case DATE = "date";
    case CHECKBOX = "checkbox";
    case LOCATION = "location";

    public function label()
    {
        return match ($this) {
            self::TEXT => "متن",
            self::NUMBER => "عدد",
            self::DATE => "تاریخ",
            self::SELECT => "لیست",
            self::SELECT2 => "لیست پیشرفته",
            self::CHECKBOX => "چند گزینه ای",
            self::RADIO => "گزینه ای",
            self::TEXTAREA => "متن بلند",
            self::LOCATION => "لوکیشن",
            self::FILE => "فایل",
        };
    }
}
