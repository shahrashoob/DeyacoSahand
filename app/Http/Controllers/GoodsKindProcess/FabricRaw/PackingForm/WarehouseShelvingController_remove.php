<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WarehouseShelvingController extends Controller
{

    public static $info = [
        "route" => "fabric_raw.packing_form.warehouse_shelving.",
        "enable_status" => [
            "001",
            "002",
            "003",
            "004",
            "005",
            "006",
            "007",
            "008",
            "009",
            "010",
            "011",
            "012",
            "013",
            "020"
        ],
        "button" => ["caption" => "ثبت قفسه بسته بندی", "class" => "btn-primary"],

    ];
    public static $view_path = "goods_kind_process.fabric_raw.packing_form.warehouse_shelving.";

    var $dashboard_route = "fabric_raw.packing_form.";

    public function __construct()
    {
    }

    public function index(PackingForm $packing_form)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }


        return view(PrintQRController::$view_path . "index",
            compact("packing_form", ));
    }

    public function submit(Request $request, PackingForm $packing_form, $check_permission = true)
    {

        if ($check_permission) {
            $result = $this->checkPermission($packing_form);
            if ($result != "") {
                return $result;
            }
        }


//        return redirect()->route("fabric_raw.packing_form.view", $packing_form)->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);
    }
    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, PrintQRController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}