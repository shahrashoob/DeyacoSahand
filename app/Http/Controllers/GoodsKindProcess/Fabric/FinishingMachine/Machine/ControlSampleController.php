<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralLogController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineProductPropertyValue;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Database\Seeders\LineProductStation\PackingTypeLablePrintingTypeSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ControlSampleController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.control_sample.",
        "button" => ["caption" => "دریافت نمونه شاهد", "class" => "btn-info"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.control_sample.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index( Machine $machine){
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }
        if ($allocation->items()->count() > 1) {
            return back()->withErrors("امکان دریافت فرم شاهد برای تخصیص هایی که بیش از ردیف دارند، مقدر نمی باشد.");
        }
        $machine_allocation = $allocation->items()->first();
        $machine_product_property_list= MachineProductPropertyValue::GetCaption($machine,$machine_allocation->product,$machine_allocation->line_product_station);

        $current_input_list = CurrentMachineInput::
        where([
            "machine_id" => $allocation->machine->id,
            "allocation_id" => ($allocation->id ?? -1),
        ])->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();

        return view($this->view_path."index",compact("allocation","machine","machine_product_property_list","current_input_list"));
    }
    public function print_download_form(Request $request, Machine $machine, $type)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }
        if ($allocation->items()->count() > 1) {
            return back()->withErrors("امکان دریافت فرم شاهد برای تخصیص هایی که بیش از ردیف دارند، مقدر نمی باشد.");
        }

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        switch ($type) {
            case "download":
                return $this->download($allocation, $worker);
                return "ok";
            case "print":
                $this->direct_print($allocation, $worker);
                return back()->with(["success" => "فرم شاهد به پرینت پیش فرض ارسال گردید."]);
            default:
                return back()->withErrors("نوع پرینت/بسته بندی به درستی انتخاب نشده است، لطفا یکبار دیگر تلاش کنید.");
        }

    }


    public function download(Allocation $allocation, $worker)
    {


        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(1013); // A5
        $result = self::create_pdf_file($allocation, $worker, "download", $packing_type_label_printing_type);
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $allocation->id, $packing_type_label_printing_type->size
        );

    }
    public function direct_print(Allocation $allocation, Worker $worker)
    {

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(1013); // A5
        $result = self::create_pdf_file($allocation, $worker, "print", $packing_type_label_printing_type);

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $allocation->id,
            $packing_type_label_printing_type->size
            ,
            $result["print_file"],
        );
    }


    public static function create_pdf_file(Allocation $allocation, $worker, $type, $packing_type_label_printing_type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCSC_QR", [$allocation, $allocation->machine_id]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


        $qr = QrCode::size($packing_type_label_printing_type->qr_size)->generate($url);
        $machine_allocation = $allocation->items()->first();

       $machine_product_property_list= MachineProductPropertyValue::GetCaption($machine_allocation->machine,$machine_allocation->product,$machine_allocation->line_product_station);
        $software_name = Setting::getStringValue("software_name");
        $current_input_list = CurrentMachineInput::
        where([
            "machine_id" => $allocation->machine->id,
            "allocation_id" => ($allocation->id ?? -1),
            "production_id" => $machine_allocation->production_id ?? -1
        ])->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();

        $html[0] = view(self::$info["view_path"] . "template" . $packing_type_label_printing_type->id . "._head")->render();
        $html[0] .= view(self::$info["view_path"] . "template" . $packing_type_label_printing_type->id . "._print_info", compact("allocation","machine_allocation","current_input_list", "qr", "software_name","machine_product_property_list"))->render() . $html[0];
        $html[0] .= view(self::$info["view_path"] . "template" . $packing_type_label_printing_type->id . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $allocation->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }
//return $html[0];
        return ["html" => $html, "print_file" => $print_file];
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, false, true);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
