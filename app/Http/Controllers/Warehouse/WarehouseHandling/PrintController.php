<?php

namespace App\Http\Controllers\Warehouse\WarehouseHandling;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.print.",
        "enable_status" => ["301", "302", "304", "311", "312", "313", "322", "323"],
        "button" => ["caption" => "پرینت دستور انبارگردانی", "class" => "btn-info"],
        "view_path" => "warehouse.warehouse_handling.print.",

    ];
    var $view_path;
    var $route_path;
    var $paginage = 50;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(WarehouseHandling $warehouse_handling)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
        if ($warehouse_handling->products()->count() == 0) {
            return back()->withErrors("با توجه به اینکه در دستور انبار گردانی همه کالاها انتخاب شده اند، امکان پرینت دستور انبارگردانی وجود ندارد.");
        }
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $product_list = $warehouse_handling->products()->paginate($this->paginage);
        $packing_form_data = self::get_packing_form_data($warehouse_handling, $product_list);

        return view($this->view_path . "index", compact("warehouse_handling", "product_list", "packing_form_data"));
    }

    public function print(WarehouseHandling $warehouse_handling, $from_row)
    {
        $result = self::create_pdf_file( $warehouse_handling, "print",$from_row);

        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }
        $worker = Worker::find(Auth::user()->id);
        $printer = $worker->default_printer;

        Pdf::createAsHtml( $result["html"],
            "L",
            $warehouse_handling->code, "A5"," ",$result["print_file"]
        );
        return back()->with( [ "success" => "جهت دریافت پرینت دستور انبارگردانی، به محل پرینتر " . $printer->caption . " مراجعه فرمایید." ] );

    }
    public function download(WarehouseHandling $warehouse_handling, $from_row){
        $result = self::create_pdf_file( $warehouse_handling, "download",$from_row);

        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }

        Pdf::createAsHtml( $result["html"],
            "L",
            $warehouse_handling->code, "A5"," "
        );
    }

    public static function get_packing_form_data(WarehouseHandling $warehouse_handling, $product_list)
    {
        $product_ids = [];
        foreach ($product_list as $item) {
            $product_ids[$item->product_id] = $item->product_id;
        }
        if (count($product_ids) == 0) {
            return back()->withErrors("امکان پرینت مقدور نمی باشد، لطفا یک بار دیگر تلاش کنید.");
        }
        $packing_form_data = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $warehouse_handling->warehouse_id)->
        where("packing_forms.status_id", 7007003)->// تحویل شده به انبار
        where("warehouse_status_id", 4201)->
        whereIn("product_id", $product_ids)->
        groupBy("product_id")->
        selectRaw("product_id,count(distinct(packing_form_id)) as count,sum(final_amount) as sum_final_amount")->
        get()->keyBy("product_id");
        return $packing_form_data;
    }

    public static function create_pdf_file(WarehouseHandling $warehouse_handling, $type, $first_row)
    {

        $software_name = Setting::getStringValue("software_name");
        $controller = new self();

        $product_list = $warehouse_handling->products()->
        skip($first_row - 1)->
        take($controller->paginage)->get();

        $packing_form_data = self::get_packing_form_data($warehouse_handling, $product_list);

        // Page 1
        $html[0] = view($controller->view_path . "_head")->render();
        $html[0] .= view($controller->view_path . "_print_info",
                compact("warehouse_handling","product_list", "packing_form_data", "first_row", "software_name"))->render() . $html[0];
        $html[0] .= view($controller->view_path . "_footer")->render();


        $worker = Worker::find(Auth::user()->id);
        $printer = $worker->default_printer;
        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => "WH" . $warehouse_handling->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $printer->id,
                "number_of_prints" => 1
            ]);

        }

        return ["result" => true, "html" => $html, "print_file" => $print_file];
    }

    public function checkPermission(WarehouseHandling $warehouseHandling)
    {

//        $result = DashboardController::checkPermissionConditions($warehouseHandling, self::$info);
//        if (!$result["result"]) {
//            return back()->withErrors($result["message"]);
//        }

        return "";
    }
}
