<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductionFormController extends Controller
{
    public static $perfix_status_code = "7302";
    var $view_path = "goods_kind_process.fabric.production_form.";
    var $route_path = "fabric.production_form.";

    public function __construct()
    {

        View::share("perfix_status_code", ProductionFormController::$perfix_status_code);
        View::share("view_path", $this->view_path);
        View::share("route_path", $this->route_path);
    }

    public function view(ProductionForm $production_form)
    {


        $result = $this->checkPermission($production_form);
        if ($result != "") {
            return $result;
        }

        $controller_info = ProductionFormController::get_controller_info();
        $controller_info_bands = ProductionFormController::get_controller_info_bands();

        $special_condition = [];


        return \view($this->view_path . "view", compact("production_form", "controller_info", "special_condition", "controller_info_bands"));

    }

    public function download_form(ProductionForm $production_form)
    {
        $worker = Worker::find(Auth::id());
        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(13); // 123*9
        $result = self::create_pdf_file($production_form, $worker, "download", $packing_type_label_printing_type);
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $production_form->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );

    }

    public function direct_print(ProductionForm $production_form)
    {
        $worker = Worker::find(Auth::id());
        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(13); // 123*9
        $result = self::create_pdf_file($production_form, $worker, "print", $packing_type_label_printing_type);

        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $production_form->id,
            [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
            ,
            $result["print_file"],
        );

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);

    }


    public static function create_pdf_file(ProductionForm $production_form, $worker, $type, $packing_type_label_printing_type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCPrFr_QR", [$production_form->id, $production_form->machine_id]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


        $qr = QrCode::size($packing_type_label_printing_type->qr_size)->generate($url);


        $software_name = Setting::getStringValue("software_name");


        $html[0] = view( "goods_kind_process.general.production_form.print.template" . $packing_type_label_printing_type->id . "._head")->render();
        $html[0] .= view( "goods_kind_process.general.production_form.print.template" . $packing_type_label_printing_type->id . "._print_info", compact("production_form", "qr", "software_name"))->render() . $html[0];
        $html[0] .= view( "goods_kind_process.general.production_form.print.template" . $packing_type_label_printing_type->id . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $production_form->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }
//return $html[0];
        return ["html" => $html, "print_file" => $print_file];
    }


    public static function checkPermissionConditions(ProductionForm $production_form, $info = false)
    {

        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = ProductionFormController::$perfix_status_code . $value;
            }
            unset($value);
            if (!in_array($production_form->status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت فرم تولید جهت عملیات نامعتبر است",
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }

    public function checkPermission(ProductionForm $production_form)
    {
        $result = ProductionFormController::checkPermissionConditions($production_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

    }

    public static function get_controller_info()
    {
        return $controller_info = [
//            "01" => FabricRaw\ProductionForm\FabricExtractionController::$info,
//            "02" => FabricRaw\ProductionForm\ExtractionItemController::$info,
//            "02" => FinishingFabricExtractionController::$info,
//            "03" => FabricExtractionForStopOrderController::$info,
        ];
    }

    public static function get_controller_info_bands()
    {
        return $controller_info_bands = [
//            "01" => GradingController::$info,
//            "02" => GradingCancelController::$info,
        ];
    }

    public static function get_controller_info_all()
    {
        return $controller_info = [
//            "01" => FabricRaw\ProductionForm\FabricExtractionController::$info,
//            "02" => FabricRaw\ProductionForm\ExtractionItemController::$info,
//            "02" => GradingController::$info,
//            "03" => FinishingFabricExtractionController::$info,
//            "04" => FabricExtractionForStopOrderController::$info,
//            "05" => GradingCancelController::$info,
        ];
    }
}
