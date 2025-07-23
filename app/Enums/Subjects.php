<?php

namespace App\Enums;

use App\Traits\EnumHelpers;
use App\Traits\HasLabel;
use function Laravel\Prompts\select;

enum Subjects: string
{
    use HasLabel , EnumHelpers;

    case AREA = "area";
    case BANNER = "banner";
    case CITY = "city";
    case COMMENT = "comment";
    case DASHBOARD_ITEM = "dashboard_item";
    case FILE = "file";
    case FORM = "form";
    case FORM_ITEM = "form_item";
    case FORM_REPORT = "form_report";
    case NEIGHBORHOOD = "neighborhood";
    case REGION = "region";
    case REPORT = "report";
    case REQUEST = "request";
    case REQUEST_PLAN = "request_plan";
    case RING = "ring";
    case RING_MEMBER = "ring_member";
    case SETTINGS = "settings";
    case STATISTIC = "statistic";
    case UNIT = "unit";
    case USER = "user";
    case USER_ROLE = "user_role";
    case WRITTEN_REQUEST = "written_request";

    public function label()
    {
        return match ($this) {
            self::AREA => 'ناحیه ها',
            self::BANNER => 'بنر ها',
            self::CITY => 'شهر ها',
            self::COMMENT => 'کامنت ها',
            self::DASHBOARD_ITEM => 'ایتم های داشبورد',
            self::FILE => 'فایل ها',
            self::FORM => 'فرم ها',
            self::FORM_ITEM => 'سوالات فرم ها',
            self::FORM_REPORT => 'گزارش های گزارشگیر',
            self::NEIGHBORHOOD => 'محله ها',
            self::REGION => 'مناطق',
            self::REPORT => 'گزارش ها',
            self::REQUEST => 'درخواست ها',
            self::REQUEST_PLAN => 'اکشن پلن ها',
            self::RING => 'حلقه ها',
            self::RING_MEMBER => 'اعضای حلقه ها',
            self::SETTINGS => 'تنظیمات',
            self::STATISTIC => 'آمار',
            self::UNIT => 'واحد های حقوقی',
            self::USER => 'کاربران',
            self::USER_ROLE => 'نقش ها',
            self::WRITTEN_REQUEST => 'درخواست های مکتوب',
        };
    }
}
