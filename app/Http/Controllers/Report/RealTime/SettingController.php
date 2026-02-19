<?php

namespace App\Http\Controllers\Report\RealTime;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\Report\RealTime\RealTimeOrder;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Unit\UnitType;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    var $route_path = "report.real_time.setting.";
    var $view_path = "report.real_time.setting.";

    //
    public function index()
    {

        $values = Setting::getValues();

        return view($this->view_path . "index", compact("values"));
    }

    public function submit(Request $request,$type)
    {

        $setting = Setting::whereIn("key", array_keys(RealTimeOrder::$list_setting))->get();
        foreach ($setting as $item) {
            $key = $item->key;

            if (RealTimeOrder::$list_setting[$key] == 1 && $type==1) { // type 1 => لیست های چک باکش را بررسی می کند.


                if (isset($request->$key)) {
                    $item->string_value = 1;
                    $item->integer_value = 1;
                    $item->double_value = 1;
                } else {

                    $item->string_value = 0;
                    $item->integer_value = 0;
                    $item->double_value = 0;
                }
                $item->save();

            } else {
                if (isset($request->$key)) {
                    $item->string_value = $request->$key;
                    $item->integer_value = $request->$key;
                    $item->double_value = $request->$key;
                }
                $item->save();
            }
        }
        return back()->with(["success" => "تنظیمات با موفقیت ثبت گردید."]);
    }


}
