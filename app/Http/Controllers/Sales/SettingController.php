<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    var $route_path = "sales.setting.";
    var $view_path = "sales.setting.";

    //
    public function index()
    {

        $goods_kinds = GoodsKind::get();
        $property_option = [];
        foreach ($goods_kinds as $item) {

            $grouping = GoodsKind\GoodsKindDisplayProperty::
            where("goods_kind_id", $item->id)->
            first();

            $property_option[$item->id] = Option::get("get_property_by_goods_kind", $grouping->goods_kind_property_id ?? 0, $item->id);
        }

        $formal_status = Status::whereIn("status_type_id", [350, 351])->get();

        $sale_formal_status_list = Setting::getStringValue("sale_formal_status_list");

        $sale_formal_status_list = json_decode($sale_formal_status_list, true);



        $invalid_status=[35010,35050,35060,35065,35070,35075,35078,35095,35096,35097];
        $planing_status = Status::whereIn("status_type_id", [350, 351])->whereNotIn("id",$invalid_status)->get();

        $planing_status_status_list = Setting::getStringValue("sale_planing_status_list");

        $planing_status_status_list = json_decode($planing_status_status_list, true);

        $values = Setting::getValues();

        return view($this->view_path . "index", compact("goods_kinds", "sale_formal_status_list","planing_status_status_list","planing_status", "property_option", "formal_status", "values"));
    }

    public function submit(Request $request)
    {

        GoodsKind\GoodsKindDisplayProperty::where("id", ">", 0)->delete();
        $goods_kinds = GoodsKind::get();
        foreach ($goods_kinds as $item) {
            $id = "goods_kind_" . $item->id;
            if ($request->$id) {
                GoodsKind\GoodsKindDisplayProperty::create([
                    "goods_kind_id" => $item->id,
                    "goods_kind_property_id" => $request->$id
                ]);
            }
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "تغییرات با موفقیت ذخیره شد."]);
    }

    public function submit_formal_status(Request $request)
    {

        if (!$request->data["status"]) {
            return back()->withErrors("لطفا یک وضعیت را انتخاب نمایید.");
        }
        $data = $request->data["status"];

        $sale_formal_status_list = Setting::where("key", "sale_formal_status_list")->first();
        $sale_formal_status_list->string_value = array_keys($data);
        $sale_formal_status_list->save();

        return back()->with("اطلاعات با موفقیت ذخیره شد.");
    }

    public function submit_planing_status(Request $request)
    {

        if (!$request->data["planing_status"]) {
            return back()->withErrors("لطفا یک وضعیت را انتخاب نمایید.");
        }

        $data = $request->data["planing_status"];

        $sale_planing_status_list = Setting::where("key", "sale_planing_status_list")->first();
        $sale_planing_status_list->string_value = array_keys($data);
        $sale_planing_status_list->save();

        return back()->with("اطلاعات با موفقیت ذخیره شد.");
    }

    public function property()
    {

    }
}
