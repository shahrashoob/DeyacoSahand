<?php

namespace App\Http\Controllers\LineProductStation\Carrier;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\CarrierGroup;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrierTypeIcController extends Controller
{
    var $view_path = "line_product_station.carrier.carrier_type_ic.";
    var $rout_path = "line_product_station.carrier.carrier_type_ic.";

    public function index($search = "")
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $list = [];
        $unit = [];
        if ($search != "") {
            $result_carrier_types = CarrierType::SearchCerrierType($search, env("IC_APIKEY"));
            if (!$result_carrier_types["result"]) {
                return back()->withErrors($result_carrier_types["error"]);
            }
            $list = $result_carrier_types["carrier_types"];
        }
        foreach ($list as $item) {
            $unit = Unit::where('id', $item["unit_id"])->first();
        }
        return view($this->view_path . "index", compact('list', 'search', 'unit'));
    }

    public function search(Request $request)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $search = $request->search;
        $result_carrier_types = CarrierType::SearchCerrierType($search, env("IC_APIKEY"));
        if (!$result_carrier_types["result"]) {
            return back()->withErrors($result_carrier_types["error"]);
        }
        return redirect()->route($this->rout_path . "index", $search);
        
    }

    public function create($carrier_type_id)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_carrier_types = CarrierType::ShowCerrierType($carrier_type_id, env("IC_APIKEY"));
        if (!$result_carrier_types["result"]) {
            return back()->withErrors($result_carrier_types["error"]);
        }
        $carrier_type_exsist = CarrierType::where('id', $carrier_type_id)->exists();

        if ($carrier_type_exsist) {
            return back()->withErrors('این نوع حامل  قبلا در سامانه  ثبت شده است.');
        } else {
            CarrierType::insert($result_carrier_types["carrier_type"]);
        }

        return redirect()->route("line_product_station.carrier.carrier_type.index")->with(["success" => "یک نوع حامل با موفقیت اضافه گردید"]);
    }

    public function edit(CarrierType $carrier_type)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $software_name = Setting::getStringValue('software_name');
        $result_carrier_types = CarrierType::ShowCerrierType($carrier_type->id, env("IC_APIKEY"));
        if (!$result_carrier_types["result"]) {
            return back()->withErrors($result_carrier_types["error"]);
        }

        $unit = Unit::where('id', $result_carrier_types["carrier_type"]["unit_id"])->first();
        $carrier_group = CarrierGroup::where('id', $result_carrier_types["carrier_type"]["carrier_group_id"])->first();
        return view($this->view_path . "edit", compact("carrier_type", "software_name", 'result_carrier_types', 'unit', 'carrier_group'));
    }

    public function update(CarrierType $carrier_type)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_carrier_types = CarrierType::ShowCerrierType($carrier_type->id, env("IC_APIKEY"));
        if (!$result_carrier_types["result"]) {
            return back()->withErrors($result_carrier_types["error"]);
        }

        $carrier_type->update([
            "caption"=> $result_carrier_types["carrier_type"]["caption"],
            "carrier_group_id"=> $result_carrier_types["carrier_type"]["carrier_group_id"],
            "min_band_number"=> $result_carrier_types["carrier_type"]["min_band_number"],
            "max_band_number"=> $result_carrier_types["carrier_type"]["max_band_number"],
            "min_band_capacity"=> $result_carrier_types["carrier_type"]["min_band_capacity"],
            "max_band_capacity"=> $result_carrier_types["carrier_type"]["max_band_capacity"],
            "average_weight"=> $result_carrier_types["carrier_type"]["average_weight"],
            "length"=> $result_carrier_types["carrier_type"]["length"],
            "width"=> $result_carrier_types["carrier_type"]["width"],
            "height"=> $result_carrier_types["carrier_type"]["height"],
            "it_changes_volume_after_filling"=> $result_carrier_types["carrier_type"]["it_changes_volume_after_filling"],

        ]);

        return redirect()->back()->with(["success" => "اطلاعات با موفقیت بروزرسانی گردید."]);

    }

}
