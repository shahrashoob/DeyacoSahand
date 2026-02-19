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

class PrintQRController extends Controller
{

    public static $info = [
        "route" => "fabric_raw.packing_form.print_qr.",
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
            "020",
            "026"
        ],
        "button" => ["caption" => "پرینت بسته بندی", "class" => "btn-info"],

    ];
    public static $view_path = "goods_kind_process.fabric_raw.packing_form.print_qr.";

    var $dashboard_route = "fabric_raw.packing_form.";

    public function __construct()
    {
    }

    public function index(PackingForm $packing_form, $back_url_route = false, $id = false)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }


        $software_name = Setting::getStringValue("software_name");

        if (!Route::has($back_url_route)) {
            $back_url_route = false;
        }

        $qr = '<img  style="font-size: 20px;" src="' . url("assets/images/qr_core.png") . "'/>";
        $barcode = "";
        $barcode_pin = "";

        //نمایش نقص های غیرمجاز کالا
        $product_fault_list= $product_fault_list=self::GetProductFault($packing_form);

        return view(PrintQRController::$view_path . "index",
            compact("packing_form", "qr", "software_name", "back_url_route", "id", "barcode","barcode_pin","product_fault_list"));
    }

    public function submit(Request $request, PackingForm $packing_form, $check_permission = true)
    {

        if ($check_permission) {
            $result = $this->checkPermission($packing_form);
            if ($result != "") {
                return $result;
            }
        }

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $result = $this->create_pdf_file($packing_form, $worker, "print");

        Pdf::labelPrinter($result["html"],
            $packing_form->packing_type->packing_type_label_printing_type->orientation,
            $packing_form->id, [
                $packing_form->packing_type->packing_type_label_printing_type->width,
                $packing_form->packing_type->packing_type_label_printing_type->long
            ],
            $result["print_file"]);

        if (Route::has($request->back_url_route)) {
            return redirect()->route($request->back_url_route, $request->id)->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);

        }


        return redirect()->route("fabric_raw.packing_form.view", $packing_form)->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);
    }

    public function download(PackingForm $packing_form, $check_permission = true)
    {

        $result = $this->checkPermission($packing_form);

        if ($result != "" && $check_permission) {
            return $result;
        }
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $result = $this->create_pdf_file($packing_form, $worker, "download");

        Pdf::labelPrinter($result["html"],
            $packing_form->packing_type->packing_type_label_printing_type->orientation,
            $packing_form->id, [
                $packing_form->packing_type->packing_type_label_printing_type->width,
                $packing_form->packing_type->packing_type_label_printing_type->long
            ]
        );

    }

    public static function create_pdf_file(PackingForm $packing_form, $worker, $type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCPK_QR", [$packing_form, $packing_form->getRandom()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


        $qr = QrCode::size($packing_form->packing_type->packing_type_label_printing_type->qr_size)->generate($url);
        $barcode = DNS1D::getBarcodeSVG($packing_form->getCodeNumber(), 'C39', 1.6, 30);
        $barcode_pin="";

          //  $barcode = DNS1D::getBarcodeSVG($packing_form->getCodeNumber(), 'C39', 1.6, 40);
            $barcode_pin = DNS1D::getBarcodeSVG($packing_form->getPin1(), 'C128', 1.6, 50);
//            $barcode_pin[1] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'C39E+', 1.6, 70);
//            $barcode_pin[2] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'C93', 1.6, 70);
//            $barcode_pin[3] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'S25', 1.6, 70);
//            $barcode_pin[4] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'S25+', 1.6, 70);
//            $barcode_pin[5] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'I25', 1.6, 70);
//            $barcode_pin[6] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'C128B', 1.6, 70);
//            $barcode_pin[7] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'C128', 1.6, 70);
//            $barcode_pin[8] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'POSTNET', 1.6, 70);
//            $barcode_pin[9] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'KIX', 1.6, 70);
//            $barcode_pin[10] = DNS1D::getBarcodeSVG($packing_form->getPin(), 'CODE11', 1.6, 70);

        //Test
        // https://github.com/milon/barcode
        // https://avandprinter.com/kinds-of-barcodes/
//        if($packing_form->id == 70556) {
//            $barcode = DNS1D::getBarcodeSVG("PI3R71556xox765W77.85A258B77.85", 'C93', 2, 50);
//        }

        //نمایش نقص های غیرمجاز کالا
        $product_fault_list=self::GetProductFault($packing_form);





        // در بعضی از جاها مثل برگشت مواد اولیه از انبارک لازم است،
        // تا اطلاعاتی به غیر از اطلاعات داخل دیتابیس بر روی برچست چاپ شود
        // تا بعد از انجام تراکنش اصلاحی اطلاعات بسته بندی هم بروز شود، در این جور مواقع از این متغیر استفاده می کنیم.
        $unconfirmed_data = $packing_form->unconfirmed_data ?? null;


        $software_name = Setting::getStringValue("software_name");
        $html[0] = view(PrintQRController::$view_path . "template" . $packing_form->packing_type->packing_type_label_printing_type_id . "._head")->render();
        $html[0] .= view(PrintQRController::$view_path . "template" . $packing_form->packing_type->packing_type_label_printing_type_id . "._print_info", compact("packing_form", "qr", "software_name", "barcode", "unconfirmed_data","barcode_pin","product_fault_list"))->render() . $html[0];
        $html[0] .= view(PrintQRController::$view_path . "template" . $packing_form->packing_type->packing_type_label_printing_type_id . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $packing_form->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_form->packing_type->packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }
//return $html[0];
        return ["html" => $html, "print_file" => $print_file];
    }

    public static function direct_print(PackingForm $packing_form, Worker $worker)
    {

        $result = PrintQRController::create_pdf_file($packing_form, $worker, "print");
        Pdf::labelPrinter($result["html"],
            $packing_form->packing_type->packing_type_label_printing_type->orientation,
            $packing_form->id, [
                $packing_form->packing_type->packing_type_label_printing_type->width,
                $packing_form->packing_type->packing_type_label_printing_type->long
            ],
            $result["print_file"],
        );
    }

    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, PrintQRController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public static function GetProductFault(PackingForm $packing_form)
    {
        $product_fault_list=[];
        if(env("APP_Store3") == 1){ // این تنظیمات را فقط در حریر فعال کنید.
            return $product_fault_list;
        }
        if(in_array($packing_form->packing_type->packing_type_label_printing_type_id ,[1])){

            foreach ($packing_form->items as $item_packing_form){
                $product_fault_list[$item_packing_form->id] = BOMFaultIllegal::
                where("product_id", $item_packing_form->production_form_item->production->parent_production->product_id ?? 0)->
                groupBy("product_fault_id")->
                get();

            }

        }
        return $product_fault_list;
    }

}
