<?php

namespace Database\Seeders\LineProductStation;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCreationProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $module_code = 5231;
        foreach (self::getStatus() as $item) {
            if (!DB::table("status")->where(["id" => $item["id"]])->exists()) {

                DB::table("status")->insert($item);
            } else {
                DB::table("status")->where(["id" => $item["id"]])->update($item);
            }
        }

        $controller_info = \App\Http\Controllers\LineProductStation\Product\ProductCreation\DashboardController::get_controller_info();

        foreach ($controller_info as $key => $item) {
            $id = $module_code . $key;
            $name = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table("buttons")->insert([
                "id" => $id,
                "name" => $name,
                "caption" => $caption,
                "status_type_id" => $module_code
            ]);

        }


        $controller_info = \App\Http\Controllers\LineProductStation\Product\ProductCreation\DashboardController::get_controller_info_for_next();
        $goods_kind_list= GoodsKind::all();
        foreach ($goods_kind_list as $goods_kind) {
            foreach ($controller_info as $key => $item) {
                $id = $module_code . $key;
                // بعد از اینکه ماژول های طراحی کالا اضافه شدن، هر مازول جدید که اضافه شد را به جدول زیر اضافه می کنیم تا اولویت های آن مشخص گردد.
                // و مدیر سیستم بتواند فرایند های آن را بچیند
                if (!DB::table("product_creation_process_priority")->where(["button_id"=> $id,"goods_kind_id"=>$goods_kind->id])->first()) {
                    DB::table("product_creation_process_priority")->insert([
                        "button_id" => $id,
                        "goods_kind_id"=>$goods_kind->id,
                        "priority_number" => $item["priority_number"]
                    ]);
                } else {
                    DB::table("product_creation_process_priority")->
                    where("button_id", $id)->
                    where("goods_kind_id", $goods_kind->id)->
                    update([
                        "priority_number" => $item["priority_number"]
                    ]);
                }
            }
        }

    }

    public static function getStatus()
    {
        // وضعیت درخواست طراحی کالا
        return
            [
                ["id" => 5231001, "caption" => "در انتظار ارسال نمونه کالا", "status_type_id" => 5231],
                ["id" => 5231002, "caption" => "در انتظار تکمیل اطلاعات پایه", "status_type_id" => 5231],
                ["id" => 5231003, "caption" => "در انتظار تعریف کالاهای مصرفی", "status_type_id" => 5231],
                ["id" => 5231004, "caption" => "در انتظار ثبت اطلاعات پستی", "status_type_id" => 5231],
                ["id" => 5231005, "caption" => "در انتظار تکمیل اطلاعات فروش", "status_type_id" => 5231],
                ["id" => 5231006, "caption" => "در انتظار تکمیل اطلاعات انبارش", "status_type_id" => 5231],
                ["id" => 5231007, "caption" => "در انتظار تکمیل اطلاعات طبقه بندی", "status_type_id" => 5231],
                ["id" => 5231008, "caption" => "در انتظار تعریف مسیر محصول", "status_type_id" => 5231],
                ["id" => 5231009, "caption" => "در انتظار ثبت مشخصات مسیر محصول", "status_type_id" => 5231],
                ["id" => 5231010, "caption" => "در انتظار تعریف BOM", "status_type_id" => 5231],
                ["id" => 5231011, "caption" => "در انتظار تعریف کالای جایگزین تولید", "status_type_id" => 5231],
                ["id" => 5231012, "caption" => "در انتظار تعریف کالای جایگزین مصرف", "status_type_id" => 5231],
                ["id" => 5231013, "caption" => "در انتظار تکمیل اطلاعات ضایعات", "status_type_id" => 5231],
                ["id" => 5231014, "caption" => "در انتظار طراحی جریان همبافتی", "status_type_id" => 5231],
                ["id" => 5231015, "caption" => "در انتظار ثبت بسته بندی های مجاز کالا", "status_type_id" => 5231],
                ["id" => 5231016, "caption" => "در انتظار ثبت مشخصات کالا", "status_type_id" => 5231],
                ["id" => 5231017, "caption" => "در انتظار ثبت اطلاعات لات ", "status_type_id" => 5231],
                ["id" => 5231018, "caption" => "در انتظار ثبت اطلاعات شید ", "status_type_id" => 5231],
                ["id" => 5231019, "caption" => "در انتظار ثبت اطلاعات بهای تمام شده ", "status_type_id" => 5231],

                ["id" => 5231020, "caption" => "در حال تولید نمونه آزمایشگاهی ", "status_type_id" => 5231],
                ["id" => 5231021, "caption" => "در انتظار تایید اولیه نمونه آزمایشگاهی ", "status_type_id" => 5231],
                ["id" => 5231022, "caption" => "در انتظار تایید نهایی نمونه آزمایشگاهی ", "status_type_id" => 5231],
                ["id" => 5231023, "caption" => "در انتظار ارسال نمونه آزمایشگاهی برای مشتری ", "status_type_id" => 5231],
                ["id" => 5231024, "caption" => "در انتظار تایید نمونه آزمایشگاهی توسط مشتری ", "status_type_id" => 5231],
                ["id" => 5231025, "caption" => "در انتظار صدور دستور تولید نمونه آزمایشگاهی ", "status_type_id" => 5231],

                ["id" => 5231026, "caption" => "در انتظار بارگذاری تصویر کالا", "status_type_id" => 5231],
                ["id" => 5231027, "caption" => "در انتظار ثبت کالا در نرم افزار مالی", "status_type_id" => 5231],
                ["id" => 5231028, "caption" => "در انتظار قیمت گذاری", "status_type_id" => 5231],
                ["id" => 5231029, "caption" => "در انتظار تعرفه گذاری", "status_type_id" => 5231],

                ["id" => 5231030, "caption" => "گام 1 طراحی", "status_type_id" => 5231],
                ["id" => 5231031, "caption" => "گام 2 طراحی", "status_type_id" => 5231],
                ["id" => 5231032, "caption" => "گام 3 طراحی", "status_type_id" => 5231],
                ["id" => 5231033, "caption" => "گام 4 طراحی", "status_type_id" => 5231],
                ["id" => 5231034, "caption" => "گام 5 طراحی", "status_type_id" => 5231],


                ["id" => 5231035, "caption" => "در انتظار تنظیمات کنترل کیفیت", "status_type_id" => 5231],
                ["id" => 5231036, "caption" => "در انتظار تنظیمات برنامه ریزی", "status_type_id" => 5231],

                ["id" => 5231201, "caption" => "طراحی تکمیل شده است.", "status_type_id" => 5231],


                ["id" => 5231301, "caption" => "در انتظار تکمیل اطلاعات خدمت", "status_type_id" => 5231],
                ["id" => 5231302, "caption" => "در انتظار ثبت خدمت در نرم افزار مالی", "status_type_id" => 5231],
                ["id" => 5231303, "caption" => "در انتظار تایید نهایی خدمت", "status_type_id" => 5231],


                ["id" => 5231501, "caption" => "در انتظار ثبت کالای مصرفی (تعریف سریع)", "status_type_id" => 5231],
                ["id" => 5231502, "caption" => "در انتظار مشخصات کالا (تعریف سریع)", "status_type_id" => 5231],
                ["id" => 5231503, "caption" => "در انتظار بارگرازی تصویر (تعریف سریع)", "status_type_id" => 5231],

            ];

    }
}
