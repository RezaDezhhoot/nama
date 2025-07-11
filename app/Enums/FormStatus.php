<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum FormStatus: string
{
    use HasLabel , EnumHelpers;
    case PUBLISHED = "published";
    case DRAFT = "draft";

    public function label()
    {
        return match ($this) {
            self::DRAFT => "پیشنویس",
            self::PUBLISHED => "منتشر شده",
        };
    }
}
