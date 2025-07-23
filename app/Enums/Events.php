<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;

enum Events: string
{
    use EnumHelpers , HasLabel;

    case CREATED = "created";
    case UPDATED = "updated";
    case DELETED = "deleted";
    case RESTORED = "restored";

    public function label()
    {
        return match ($this) {
            self::CREATED => 'ایجاد شده',
            self::UPDATED => 'ویرایش شده',
            self::DELETED => 'حذف شده',
            self::RESTORED => 'بازنشانی شده',
        };
    }

    public function icon()
    {
        return match ($this) {
            self::CREATED => 'flaticon-plus text-success',
            self::UPDATED => 'flaticon2-edit text-info',
            self::DELETED => 'flaticon2-trash text-danger',
            self::RESTORED => 'fa fa-trash-restore text-primary',
        };
    }
}
