<?php

namespace App\Http\Controllers\LineProductStation\Carrier;

use App\Http\Controllers\Controller;
use App\Models\HR\Employment\Employment;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CarrierTypeController extends Controller
{
    // line_product_station/carrier/carrier_type
    var $view_path = "line_product_station.carrier.carrier_type.";
    var $rout_path = "line_product_station.carrier.carrier_type.";

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by ?? "";
        } else {
            $search = session("search_carrier_type");
            $order_by = session("order_by_carrier_type");
        }

        session(["search_carrier_type" => $search, "order_by_carrier_type" => $order_by]);

        $list = CarrierType:: where("id", "like", "%" . $search . "%")->
        orWhere("caption", "like", "%" . $search . "%")->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        orderBy("carrier_group_id")->
        paginate(50);

        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.carrier.carrier_type.create_packing_type");

        $order_by_Option = Option::OrderBy("public", $order_by);
        return view($this->view_path . "index", compact("list", "search", "order_by_Option", "allow_create"));
    }

    public function create()
    {

        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.carrier.carrier_type.create_packing_type");

        if (!$allow_create) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید.");
        }
        return back()->withErrors("برای افزودن نوع کامل لطفا از طریق افزودن از منظومه داده ای اقدام نمایید.");

        $unit_option = Option::get("unit");
        $carrier_group_option = Option::get("carrier_group");

        return view($this->view_path . "create", compact("unit_option", "carrier_group_option"));
    }

    public function store(Request $request)
    {

        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.carrier.carrier_type.create_packing_type");

        if (!$allow_create) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید.");
        }
        return back()->withErrors("برای افزودن نوع کامل لطفا از طریق افزودن از منظومه داده ای اقدام نمایید.");

        if ($request->caption == "" || CarrierType::Exists($request->caption)) {
            return back()->withErrors("عنوان نوع حامل تکراری است");
        }

        $request["placed_in_warehouse"] = isset($request->placed_in_warehouse) ? 1 : 0;
        $request["has_number_ability"] = isset($request->has_number_ability) ? 1 : 0;
        $request["system_can_define_new_carrier"] = isset($request->system_can_define_new_carrier) ? 1 : 0;
        $request["it_changes_volume_after_filling"] = isset($request->it_changes_volume_after_filling) ? 1 : 0;
        CarrierType::create($request->all());

        return redirect()->route($this->rout_path . "index")->with(["success" => "یک نوع حامل با موفقیت اضافه گردید"]);

    }


    public function edit(CarrierType $carrier_type)
    {

        $unit_option = Option::get("unit", $carrier_type->unit_id);
        $carrier_group_option = Option::get("carrier_group", $carrier_type->carrier_group_id);

        $post_user = Auth::user()->posts->first();
        $edit_main_property_carrier_type = $post_user->checkButtonPermission("line_product_station.carrier.carrier_type.edit_main_property_carrier_type");

        return view($this->view_path . "edit", compact("carrier_type", "unit_option", "carrier_group_option", "edit_main_property_carrier_type"));
    }

    public function update(Request $request, CarrierType $carrier_type)
    {

        if ($request->caption == "" || CarrierType::Exists($request->caption, $carrier_type->id)) {
            return back()->withErrors("عنوان نوع حامل تکراری است");
        }
        $post_user = Auth::user()->posts->first();
        $edit_main_property_carrier_type = $post_user->checkButtonPermission("line_product_station.carrier.carrier_type.edit_main_property_carrier_type");

        $request["placed_in_warehouse"] = isset($request->placed_in_warehouse) ? 1 : 0;
        $request["has_number_ability"] = isset($request->has_number_ability) ? 1 : 0;
        $request["system_can_define_new_carrier"] = isset($request->system_can_define_new_carrier) ? 1 : 0;

        if ($edit_main_property_carrier_type) {
            $request["it_changes_volume_after_filling"] = isset($request->it_changes_volume_after_filling) ? 1 : 0;
        } else {
            // اگر دسترسی به ویرایش کل را ندارد، آنهایی که مجاز نیست را حذف می کنیم.
            unset($request["caption"]);
            unset($request["carrier_group_id"]);
            unset($request["min_band_number"]);
            unset($request["max_band_number"]);
            unset($request["min_band_capacity"]);
            unset($request["max_band_capacity"]);
            unset($request["average_weight"]);
            unset($request["length"]);
            unset($request["width"]);
            unset($request["height"]);
        }

        $carrier_type->update($request->all());

        return redirect()->route($this->rout_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);

    }

    public function carrier_list(CarrierType $carrier_type)
    {
        return view($this->view_path . "carrier_list", compact("carrier_type"));
    }

    public function create_carrier(CarrierType $carrier_type)
    {
        return view($this->view_path . "create_carrier", compact("carrier_type"));
    }

    public function store_carrier(Request $request, CarrierType $carrier_type)
    {

        $carrier = Carrier::where("code", $request->code)->
        where("carrier_type_id", $carrier_type->id)->first();
        if ($carrier) {
            return back()->withErrors("کد حامل تکراری می باشد.");
        }

        if ($request->weight + 0 <= 0) {
            return back()->withErrors("مقدار وزن باید عددی بزرگتر از صفر باشد.");
        }

        $default_status = 5320001;// خالی
        $result = Carrier:: firstOrCreate($request->code, $carrier_type->id, $default_status, null, false);
        if ($result["result"]) {
            $carrier = $result["carrier"];
            $carrier->weight = $request->weight;
            $carrier->save();

            return redirect()->route($this->rout_path . "carrier_list", $carrier_type)->with(["success" => "اطلاعات با موفقیت ثبت گردید"]);
        } else {
            return back()->withErrors($result["message"]);
        }
    }

    public function edit_carrier(CarrierType $carrier_type, Carrier $carrier)
    {


        return view($this->view_path . "edit_carrier", compact("carrier_type", "carrier"));
    }

    public function update_carrier(Request $request, CarrierType $carrier_type, Carrier $carrier)
    {

        if ($request->weight + 0 < 0) {
            return back()->withErrors("مقدار وزن باید عددی بزرگتر از صفر باشد.");
        }
        if (Carrier::where("id", "!=", $carrier->id)->where("carrier_type_id", $carrier_type->id)->where("code", $request->code)->exists()) {
            return back()->withErrors("کد حامل تکراری است.");
        }

        $carrier->weight = $request->weight;
        $carrier->code = $request->code;
        $carrier->save();

        return redirect()->route($this->rout_path . "carrier_list", $carrier_type)->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

    }

    public function download(Carrier $carrier)
    {


        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $result = $this->create_pdf_file($carrier, $worker, "download");

        Pdf::labelPrinter($result["html"],
            "L",
            $carrier->id, [
                95,
                125
            ],
        );

    }

    public static function create_pdf_file(Carrier $carrier, $worker, $type)
    {


        $html[0] = view("line_product_station.carrier.print.template1." . "._head")->render();
        $html[0] .= view("line_product_station.carrier.print.template1." . "._print_info", compact("carrier"))->render() . $html[0];
        $html[0] .= view("line_product_station.carrier.print.template1." . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => "carrier_" . $carrier->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

    public static function direct_print(Carrier $carrier)
    {

        $worker = Worker::find(Auth::user()->id);
        $result = CarrierTypeController::create_pdf_file($carrier, $worker, "print");
        Pdf::labelPrinter($result["html"],
            "L",
            $carrier->id, [
                95,
                125
            ],
            $result["print_file"]
        );

        return back()->with(["success" => "پرینت کارت حامل با موفقیت انجام شد، جهت دریافت به پرینتر پیشفرض مراجعه فرمایید."]);
    }
}
