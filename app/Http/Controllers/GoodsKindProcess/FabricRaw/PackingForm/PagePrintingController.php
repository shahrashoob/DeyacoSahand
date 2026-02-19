<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModification;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Order\TransKind;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PagePrintingController extends Controller
{

    // پرینت گرفتن بسته بندی ها به صورت صفحه بندی
    public static $info = [
        "route" => "fabric_raw.packing_form.page_printing.",
        "button" => ["caption" => "پرینت بسته بندی ها", "class" => "btn-primary"],

    ];
    public static $view_path = "goods_kind_process.fabric_raw.packing_form.page_printing.";
    public static $route_path = "fabric_raw.packing_form.page_printing.";

    var $dashboard_route = "fabric_raw.packing_form.";

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        $resultIndex = FabricRaw\PackingFormController::GetIndex($request);
        if ($resultIndex["result"]) {
            $list = $resultIndex["list"];
            return view(self::$view_path . "index", compact("list"));

        } else {
            return back()->withErrors($resultIndex["error"]);
        }
    }

    public function submit(Request $request)
    {

        $packing_form_ids = $request->packing_form_ids;
        if (!$packing_form_ids) {
            return redirect()->route(self::$route_path . "index")->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }

        $packing_form_ids = array_keys($packing_form_ids);

        $list = PackingForm::whereIn("packing_forms.id", $packing_form_ids)->
        join("packing_form_item", "packing_form_id", "packing_forms.id")->
        join("products", "product_id", "products.id")->
        select("packing_forms.*")->
        orderBy("product_id")->
        paginate(100000);

        $packing_form_ids = json_encode($packing_form_ids);
        return view(self::$view_path . "confirm_submit", compact("list", "packing_form_ids"));

    }

    public function confirm_submit(Request $request)
    {

        $packing_form_ids = $request->packing_form_ids;
        if (!$packing_form_ids) {
            return redirect()->route(self::$route_path . "index")->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }

        $packing_form_ids = json_decode($packing_form_ids,true);
        $list = PackingForm::whereIn("packing_forms.id", $packing_form_ids)->
        join("packing_form_item", "packing_form_id", "packing_forms.id")->
        join("products", "product_id", "products.id")->
        select("packing_forms.*")->
        orderBy("product_id")->
        paginate(100000);


        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $result = $this->create_pdf_file($list, $worker, "download");

        Pdf::createAsHtml($result["html"],
            "P",
            $worker->id,
        );
    }

    public static function create_pdf_file( $list, $worker, $type)
    {

        $software_name = Setting::getStringValue("software_name");
        $html[0] = view(self::$view_path  . "._head")->render();
        $html[0] .= view(self::$view_path . "._print_info", compact("list"))->render() . $html[0];
        $html[0] .= view(self::$view_path .  "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => "packing_form_".jdate(Carbon::now()->timestamp)->format('Y_m_d') . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" =>   0,
                "printer_id" => $worker->default_printer_id,
                "number_of_prints" => $worker->default_print_number
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

    public function checkPermission(PackingForm $packing_form)
    {


        return "";
    }

}
