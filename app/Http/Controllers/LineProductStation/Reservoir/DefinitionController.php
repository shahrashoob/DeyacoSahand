<?php

namespace App\Http\Controllers\LineProductStation\Reservoir;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierLog;
use App\Models\LineProduct\Reservoir\Reservoir;
use App\Models\LineProduct\Reservoir\ReservoirType;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DefinitionController extends Controller
{
    //
    public $view_path = "line_product_station.reservoir.definition.";
    public $route_path = "line_product_station.reservoir.definition.";

    public function index()
    {
        $list = Reservoir::get();
        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {
        $active_status_option = Option::get("status", 0, 1100);
        $reservoir_type_option = Option::get("reservoir_type");
        $warehouse_option = Option::get("warehouse");
        $unit_option = Option::get("unit");

        return view($this->view_path . "create", compact("reservoir_type_option", "active_status_option", "unit_option", 'warehouse_option'));

    }

    public function store(Request $request)
    {
        if ($request->caption == "" || Reservoir::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان مخزن تکراری است");
        }
        if ($request->capacity < 0) {
            return back()->withErrors("مقدار ظرفیت مخزن به درستی وارد نشده است.");
        }
        $item = Reservoir::create($request->all());
        $item->getRandom();
        return redirect()->route($this->route_path . "index")->with(["success" => "یک مخزن با موفقیت اضافه گردید."]);

    }

    public function edit(Reservoir $reservoir)
    {

        $active_status_option = Option::get("status", $reservoir->active_status_id, 1100);
        $reservoir_type_option = Option::get("reservoir_type", $reservoir->reservoir_type_id);
        $warehouse_option = Option::get("warehouse", $reservoir->warehouse_id);
        $unit_option = Option::get("unit", $reservoir->unit_id);

        return view($this->view_path . "edit", compact("reservoir_type_option", "active_status_option", "unit_option", "reservoir", 'warehouse_option'));

    }

    public function update(Request $request, Reservoir $reservoir)
    {
        if ($request->caption == "" || Reservoir::ExistsCode($request->caption, $reservoir->id)) {
            return back()->withErrors("عنوان مخزن تکراری است");
        }
        if ($request->capacity < 0) {
            return back()->withErrors("مقدار ظرفیت مخزن به درستی وارد نشده است.");
        }
        if ($reservoir->warehouse_id && $reservoir->warehouse_id != $request->warehouse_id) {
            return back()->withErrors("امکان تغییر انبار مخزن وجود ندارد.");
        }
        $reservoir->update($request->all());
        return redirect()->route($this->route_path . "index")->with(["success" => "تغییرات با موفقیت ذخیره گردید."]);

    }

    public function print_label(Reservoir $reservoir)
    {
        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        if (count($reservoir->products) == 0) {
            return back()->withErrors("با توجه به اینکه  برای مخزن هیچ نوع کالای مجازی تعریف نشده، امکان پرینت لیبل مخزن وجود ندارد.");
        }
        $result = self::create_pdf_file($reservoir, $worker, "download");
        Pdf::labelPrinter($result["html"],
            "L",
            $reservoir->id, [
                60,
                87
            ],
            $result["print_file"]
        );
    }


    public static function create_pdf_file(Reservoir $reservoir, $worker, $type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCRE_ShortLink", [$reservoir, $reservoir->getRandom()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


        $qr = QrCode::size(100)->generate($url);
        $controller = new DefinitionController();

        $software_name = Setting::getStringValue("software_name");
        $html[0] = view($controller->view_path . "print" . "._head")->render();
        $html[0] .= view($controller->view_path . "print" . "._print_info", compact("reservoir", "qr", "software_name",))->render() . $html[0];
        $html[0] .= view($controller->view_path . "print" . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $reservoir->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 1,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => 1
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

}
