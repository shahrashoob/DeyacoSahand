<?php

namespace App\Http\Controllers\Accounting\Store\StoreSetting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\SpecialLicense\Definition;
use App\Models\Accounting\Store\Store;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicenseType;
use App\Models\Utility\SpecialLicense\SpecialLicenseTypeExpert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Store1Controller extends Controller
{
    public $route_path = "accounting.store.store_setting.store1.";
    public $view_path = "accounting.store.store_setting.store1.";
    public $store_list=[1,2]; // لیست کالاهایی که در فروشگاه وجود دارد و نیاز به تنظیمات مجوز می باشد.
    private $max_priority = 5;
    public function create(Store $store,SpecialLicenseType $special_license_type)
    {
        if(!in_array($store->id,$this->store_list)){
            return back()->withErrors("تنظیمات مجوز به درستی انجام نشده با پشتیبانی تماس بگیرید.");
        }
        $post_option_all = Option::get("posts");
        $committee_option_all = Option::get("committee");
        $floating_post_all = Option::get("floating_post");
        $max_priority = $this->max_priority;
        for ($k = 1; $k <= $max_priority; $k++) {
            $post_option[$k] = $post_option_all;
            $committee_option[$k] = $committee_option_all;
            $floating_post_option[$k] = $floating_post_all;

            $post_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("post_id")->
            where("priority_number", $k)->
            pluck("post_id")->
            toArray();
            $committee_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("committee_id")->
            where("priority_number", $k)->
            pluck("committee_id")->
            toArray();


            $floating_post_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("floating_post_type_id")->
            where("priority_number", $k)->
            pluck("floating_post_type_id")->
            toArray();

            foreach ($post_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $post_ids)) {
                    $item["selected"] = 1;
                }
            }
            foreach ($committee_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $committee_ids)) {
                    $item["selected"] = 1;
                }
            }
            foreach ($floating_post_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $floating_post_ids)) {
                    $item["selected"] = 1;
                }
            }
        }
        return view($this->view_path . "create",compact('store',"special_license_type", "committee_option", "post_option", "max_priority", "floating_post_option"));
    }
    public function store(Request $request, Store $store, SpecialLicenseType $special_license_type)
    {
        if(!in_array($store->id,$this->store_list)){
            return back()->withErrors("تنظیمات مجوز به درستی انجام نشده با پشتیبانی تماس بگیرید.");
        }

        Definition\DashboardController::CreateSpecialLicenseTypeExpert($request,$special_license_type,$this->max_priority);

        $special_license_type->active_status_id=1200;//فعال
        $special_license_type->save();

        $store->status_id=4500003;//تکمیل خرید
        $store->save();

        return redirect()->route("accounting.store.dashboard.index")->with(["success" => "تنظیمات با موفقیت ثبت شد."]);
    }
}
