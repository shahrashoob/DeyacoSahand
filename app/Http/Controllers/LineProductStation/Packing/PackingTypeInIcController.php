<?php

namespace App\Http\Controllers\LineProductStation\Packing;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Status;
use Illuminate\Http\Request;

class PackingTypeInIcController extends Controller
{
    var $view_path = "line_product_station.packing.packing_type_ic.";
    var $rout_path = "line_product_station.packing.packing_type_ic.";

    public function index($search = "")
    {//تابع نمایش search
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($search!="") {
            session(["search_packing_type_in_ic" => $search]);

        }
        $search = session("search_packing_type_in_ic");

        $list = [];
        $active_status = [];
        if ($search != "") {
            $result_packing_types = PackingType::SearchPackingType($search, env("IC_APIKEY"));
            if (!$result_packing_types["result"]) {
                return back()->withErrors($result_packing_types["error"]);
            }
            $list = $result_packing_types["packing_types"];
        }
        foreach ($list as $item) {
            $active_status = Status::where('id', $item["active_status_id"])->first();
        }
        return view($this->view_path . "index", compact('list', 'search', 'active_status'));
    }

    public function search(Request $request)
    {//تابع Search
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $search = $request->search;
        $result_packing_types = PackingType::SearchPackingType($search, env("IC_APIKEY"));// این ای پی ای در بسته بندی هایی که در ای سی وجود دارد سرچ می کند
        if (!$result_packing_types["result"]) {
            return back()->withErrors($result_packing_types["error"]);
        }
        return redirect()->route($this->rout_path . "index", $search);
    }

    public function create($packing_type_id)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_packing_types = PackingType::ShowPackingType($packing_type_id, env("IC_APIKEY"));// این ای پی ای با توجه به بسته بندی که می خواهید اضافه کنید اطلاعات را برمی گرداند و در ای سی ذخیره می کند.
        if (!$result_packing_types["result"]) {
            return back()->withErrors($result_packing_types["error"]);
        }
        $packing_Type_exist = PackingType::where('id', $packing_type_id)->exists();

        if ($packing_Type_exist) {
            return back()->withErrors('این نوع بسته بندی قبلا در سامانه  ثبت شده است.');
        } else {
            if ($result_packing_types["packing_type"]['first_packing_type_id'] != null) {
                $first_packing_Type_exist = PackingType::where('id', $result_packing_types["packing_type"]['first_packing_type_id'])->exists();
                if (!$first_packing_Type_exist) {
                    return back()->withErrors(" با توجه به اینکه بسته بندی با کد " . $result_packing_types["packing_type"]['first_packing_type_id'] . " جزء بسته بندی لایه اول بسته بندی $packing_type_id می باشد." . "<br/>" .
                        "لطفا ابتدا بسته بندی با کد " . $result_packing_types["packing_type"]['first_packing_type_id'] . " را از منظومه داده ای دریافت نمایید.");
                }
            }


            // اضافه کردن نوع حامل بسته بندی
            $packing_type_layer = $result_packing_types['packing_type_layer'];
            foreach ($packing_type_layer as $item) {
                $result_carrier_types = CarrierType::ShowCerrierType($item['carrier_type_id'], env("IC_APIKEY"));
                if (!$result_carrier_types["result"]) {
                    return back()->withErrors($result_carrier_types["error"]);
                }

                $carrier_type_exsist = CarrierType::where('id', $item['carrier_type_id'])->exists();
                if (!$carrier_type_exsist) {
                    CarrierType::insert($result_carrier_types["carrier_type"]);
                }
            }



            PackingType::insert($result_packing_types["packing_type"]);// افزودن به سامانه
            foreach ($result_packing_types["packing_type_layer"] as $item) {
                PackingTypeLayer::insert($item);
            }

        }

        return redirect()->route("line_product_station.packing.packing_type.index")->with(["success" => "یک نوع بسته بندی با موفقیت اضافه گردید"]);
    }

    public function edit(PackingType $packing_type)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $software_name = Setting::getStringValue('software_name');
        $result_packing_types = PackingType::ShowPackingType($packing_type->id, env("IC_APIKEY"));
        if (!$result_packing_types["result"]) {
            return back()->withErrors($result_packing_types["error"]);
        }

        $discharge_type = DischargeType::where('id', $result_packing_types["packing_type"]["discharge_type_id"])->first();
        return view($this->view_path . "edit", compact("packing_type", "software_name", 'result_packing_types', 'discharge_type'));
    }

    public function update(PackingType $packing_type)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_packing_types = PackingType::ShowPackingType($packing_type->id, env("IC_APIKEY"));
        if (!$result_packing_types["result"]) {
            return back()->withErrors($result_packing_types["error"]);
        }

        $packing_type->update([// در اینجااطلاعات در سامانه با ای سی بروز رسانی می شود.
            "caption" => $result_packing_types["packing_type"]["caption"],
            "length" => $result_packing_types["packing_type"]["length"],
            "width" => $result_packing_types["packing_type"]["width"],
            "height" => $result_packing_types["packing_type"]["height"],
            "weight" => $result_packing_types["packing_type"]["weight"],
            "weight_error_percentage" => $result_packing_types["packing_type"]["weight_error_percentage"],
            "create_sub_packing_form_in_creation" => $result_packing_types["packing_type"]["create_sub_packing_form_in_creation"],
            "many_degrees_can_fit_into_one" => $result_packing_types["packing_type"]["many_degrees_can_fit_into_one"],
            "discharge_type_id" => $result_packing_types["packing_type"]["discharge_type_id"],

        ]);

        return redirect()->back()->with(["success" => "اطلاعات با موفقیت بروزرسانی گردید."]);

    }

    public function show($packing_type_id)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_packing_types = PackingType::ShowPackingType($packing_type_id, env("IC_APIKEY"));// این ای پی ای با توجه به بسته بندی که می خواهید اضافه کنید اطلاعات را برمی گرداند و در ای سی ذخیره می کند.
        if (!$result_packing_types["result"]) {
            return back()->withErrors($result_packing_types["error"]);
        }

        $carrier_type_list = [];


        $packing_type = $result_packing_types["packing_type"];
        $packing_type_layer = $result_packing_types['packing_type_layer'];

        foreach ($packing_type_layer as $item) {
            $result_carrier_types = CarrierType::ShowCerrierType($item['carrier_type_id'], env("IC_APIKEY"));
            if (!$result_carrier_types["result"]) {
                return back()->withErrors($result_carrier_types["error"]);
            }
            $carrier_type_list[$item['carrier_type_id']] = $result_carrier_types["carrier_type"];
        }

        $first_packing_type = null;
        $first_packing_type_layer = null;
        $second_packing_type = null;
        $result_packing_types_first = PackingType::ShowPackingType($packing_type["first_packing_type_id"], env("IC_APIKEY"));// این ای پی ای با توجه به بسته بندی که می خواهید اضافه کنید اطلاعات را برمی گرداند و در ای سی ذخیره می کند.
        if ($result_packing_types_first["result"]) {
            $first_packing_type = $result_packing_types_first["packing_type"];
            $first_packing_type_layer = $result_packing_types_first['packing_type_layer'];


            $result_packing_types_second = PackingType::ShowPackingType($first_packing_type["first_packing_type_id"], env("IC_APIKEY"));// این ای پی ای با توجه به بسته بندی که می خواهید اضافه کنید اطلاعات را برمی گرداند و در ای سی ذخیره می کند.
            if ($result_packing_types_second["result"]) {
                $second_packing_type = $result_packing_types_second["packing_type"];
            }


            foreach ($first_packing_type_layer as $item) {
                $result_carrier_types = CarrierType::ShowCerrierType($item['carrier_type_id'], env("IC_APIKEY"));
                if (!$result_carrier_types["result"]) {
                    return back()->withErrors($result_carrier_types["error"]);
                }
                $carrier_type_list[$item['carrier_type_id']] = $result_carrier_types["carrier_type"];
            }
        }
//
//return $carrier_type_list;
        $packing_type["caption"];

     //   $carrier_type_list = CarrierType::all()->keyBy('id');
        return view($this->view_path . "show", compact('packing_type', "second_packing_type", "first_packing_type_layer", 'packing_type_layer', "carrier_type_list", 'first_packing_type', 'packing_type'));

    }
}
