<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'show_dashboard' , 'title' => 'نمایش داشبورد' , 'guard_name'=> 'web'],

            ['name' => 'show_requests_'.UnitType::MOSQUE->value, 'title' => 'نمایش درخواست های مساجد', 'guard_name' => 'web'],
            ['name' => 'edit_requests_'.UnitType::MOSQUE->value, 'title' => 'ویرایش درخواست های مساجد', 'guard_name' => 'web'],
            ['name' => 'delete_requests_'.UnitType::MOSQUE->value, 'title' => 'حذف درخواست های مساجد', 'guard_name' => 'web'],
            ['name' => 'export_requests_'.UnitType::MOSQUE->value, 'title' => 'خروجی درخواست های مساجد', 'guard_name' => 'web'],

            ['name' => 'show_requests_'.UnitType::SCHOOL->value, 'title' => 'نمایش درخواست های مدارس', 'guard_name' => 'web'],
            ['name' => 'edit_requests_'.UnitType::SCHOOL->value, 'title' => 'ویرایش درخواست های مدارس', 'guard_name' => 'web'],
            ['name' => 'delete_requests_'.UnitType::SCHOOL->value, 'title' => 'حذف درخواست های مدارس', 'guard_name' => 'web'],
            ['name' => 'export_requests_'.UnitType::SCHOOL->value, 'title' => 'خروجی درخواست های مدارس', 'guard_name' => 'web'],

            ['name' => 'show_requests_'.UnitType::CENTER->value, 'title' => 'نمایش درخواست های مرکز تعالی', 'guard_name' => 'web'],
            ['name' => 'edit_requests_'.UnitType::CENTER->value, 'title' => 'ویرایش درخواست های مرکز تعالی', 'guard_name' => 'web'],
            ['name' => 'delete_requests_'.UnitType::CENTER->value, 'title' => 'حذف درخواست های مرکز تعالی', 'guard_name' => 'web'],
            ['name' => 'export_requests_'.UnitType::CENTER->value, 'title' => 'خروجی درخواست های مرکز تعالی', 'guard_name' => 'web'],

            ['name' => 'show_requests_'.UnitType::UNIVERSITY->value, 'title' => 'نمایش درخواست های دانشگاه', 'guard_name' => 'web'],
            ['name' => 'edit_requests_'.UnitType::UNIVERSITY->value, 'title' => 'ویرایش درخواست های دانشگاه', 'guard_name' => 'web'],
            ['name' => 'delete_requests_'.UnitType::UNIVERSITY->value, 'title' => 'حذف درخواست های دانشگاه', 'guard_name' => 'web'],
            ['name' => 'export_requests_'.UnitType::UNIVERSITY->value, 'title' => 'خروجی درخواست های دانشگاه', 'guard_name' => 'web'],

            ['name' => 'show_requests_written', 'title' => 'نمایش درخواست های مکتوب', 'guard_name' => 'web'],
            ['name' => 'edit_requests_written', 'title' => 'ویرایش درخواست های مکتوب','guard_name' => 'web'],
            ['name' => 'delete_requests_written', 'title' => 'حذف درخواست های مکتوب', 'guard_name' => 'web'],
            ['name' => 'export_requests_written', 'title' => 'خروجی درخواست های مکتوب', 'guard_name' => 'web'],

            ['name' => 'show_accounting', 'title' => 'نمایش حسابداری', 'guard_name' => 'web'],
            ['name' => 'export_accounting', 'title' => 'خروجی حسابداری', 'guard_name' => 'web'],

            ['name' => 'show_log_activities', 'title' => 'نمایش فعالیت کاربران', 'guard_name' => 'web'],

            ['name' => 'show_rings', 'title' => 'نمایش حلقه ها', 'guard_name' => 'web'],
            ['name' => 'delete_rings', 'title' => 'حذف حلقه ها', 'guard_name' => 'web'],
            ['name' => 'export_rings', 'title' => 'خروجی حلقه ها', 'guard_name' => 'web'],

            ['name' => 'show_forms', 'title' => 'نمایش فرم ها', 'guard_name' => 'web'],
            ['name' => 'delete_forms', 'title' => 'حذف فرم ها', 'guard_name' => 'web'],
            ['name' => 'edit_forms', 'title' => 'ویرایش فرم ها', 'guard_name' => 'web'],
            ['name' => 'create_forms', 'title' => 'ایجاد فرم ها', 'guard_name' => 'web'],

            ['name' => 'show_form_reports', 'title' => 'نمایش گزارشگیر', 'guard_name' => 'web'],
            ['name' => 'delete_form_reports', 'title' => 'حذف گزارشگیر', 'guard_name' => 'web'],
            ['name' => 'edit_form_reports', 'title' => 'ویرایش گزارشگیر', 'guard_name' => 'web'],
            ['name' => 'export_form_reports', 'title' => 'خروجی گزارشگیر', 'guard_name' => 'web'],

            ['name' => 'show_locations', 'title' => 'نمایش شهر ها و مناطق', 'guard_name' => 'web'],
            ['name' => 'delete_locations', 'title' => 'حذف شهر ها و مناطق', 'guard_name' => 'web'],
            ['name' => 'edit_locations', 'title' => 'ویرایش شهر ها و مناطق', 'guard_name' => 'web'],
            ['name' => 'create_locations', 'title' => 'ایجاد شهر ها و مناطق', 'guard_name' => 'web'],

            ['name' => 'show_units', 'title' => 'نمایش واحد ها', 'guard_name' => 'web'],
            ['name' => 'delete_units', 'title' => 'حذف واحد ها', 'guard_name' => 'web'],
            ['name' => 'edit_units', 'title' => 'ویرایش واحد ها', 'guard_name' => 'web'],
            ['name' => 'export_units', 'title' => 'ویرایش واحد ها', 'guard_name' => 'web'],
            ['name' => 'create_units', 'title' => 'ایجاد واحد ها', 'guard_name' => 'web'],

            ['name' => 'show_request_plans', 'title' => 'نمایش اکشن پلن ها', 'guard_name' => 'web'],
            ['name' => 'delete_request_plans', 'title' => 'حذف اکشن پلن ها', 'guard_name' => 'web'],
            ['name' => 'edit_request_plans', 'title' => 'ویرایش اکشن پلن ها', 'guard_name' => 'web'],
            ['name' => 'create_request_plans', 'title' => 'ایجاد اکشن پلن ها', 'guard_name' => 'web'],

            ['name' => 'show_banners', 'title' => 'نمایش بنر ها', 'guard_name' => 'web'],
            ['name' => 'delete_banners', 'title' => 'حذف بنر ها', 'guard_name' => 'web'],
            ['name' => 'edit_banners', 'title' => 'ویرایش بنر ها', 'guard_name' => 'web'],
            ['name' => 'create_banners', 'title' => 'ایجاد بنر ها', 'guard_name' => 'web'],

            ['name' => 'show_dashboard_items', 'title' => 'نمایش ایتم های داشبورد', 'guard_name' => 'web'],
            ['name' => 'delete_dashboard_items', 'title' => 'حذف ایتم های داشبورد', 'guard_name' => 'web'],
            ['name' => 'edit_dashboard_items', 'title' => 'ویرایش ایتم های داشبورد', 'guard_name' => 'web'],

            ['name' => 'show_roles', 'title' => 'نمایش نقش ها', 'guard_name' => 'web'],
            ['name' => 'delete_roles', 'title' => 'حذف نقش ها', 'guard_name' => 'web'],
            ['name' => 'edit_roles', 'title' => 'ویرایش نقش ها', 'guard_name' => 'web'],
            ['name' => 'export_roles', 'title' => 'خروجی نقش ها', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission['name'],
                'title' => $permission['title'],
            ],[
                'guard_name' => 'web'
            ]);
        }
    }
}
