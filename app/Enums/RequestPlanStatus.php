<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum RequestPlanStatus: string
{
    use EnumHelpers , HasLabel;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case COMING_SOON = 'coming_soon';

    public function label()
    {
        return match ($this) {
            self::DRAFT => 'پیشنویس',
            self::PUBLISHED => 'منتشر شده',
            self::COMING_SOON => 'به زودی'
        };
    }
}
