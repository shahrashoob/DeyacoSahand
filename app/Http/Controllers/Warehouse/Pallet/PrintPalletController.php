<?php

namespace App\Http\Controllers\Warehouse\Pallet;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintPalletController extends Controller
{

    public static $view_path = "warehouse.pallet.print_pallet.";


    public function __construct()
    {
    }

    public function DCPC_QR(Pallet $pallet, $random)
    {

        if ($pallet->random != $random) {
            return back()->withErrors("اطلاعات پالت یافت نشد.");
        }
        return $this->download($pallet);
    }

    public function download(Pallet $pallet,$type=302)
    {

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($type);
        $result = $this->create_pdf_file($pallet, $worker, "download", $packing_type_label_printing_type);


        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $pallet->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );

    }

    public static function create_pdf_file(Pallet $pallet, $worker, $type, PackingTypeLabelPrintingType $packing_type_label_printing_type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCPC_QR", [$pallet, $pallet->getRandom()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $qr = QrCode::size($packing_type_label_printing_type->qr_size)->generate($url);

        $barcode_size=[1.6,30];
        if(in_array($packing_type_label_printing_type->size,["A4","A5"])){
            $barcode_size=[5,50];
        }
        $barcode = DNS1D::getBarcodeSVG($pallet->getCodeNumber(), 'C39', $barcode_size[0], $barcode_size[1]);

        $software_name = Setting::getStringValue("software_name");
        $html[0] = view(self::$view_path . "template" . $packing_type_label_printing_type->id . "._head")->render();
        $html[0] .= view(self::$view_path . "template" . $packing_type_label_printing_type->id . "._print_info", compact("pallet", "qr", "software_name", "barcode"))->render() . $html[0];
        $html[0] .= view(self::$view_path . "template" . $packing_type_label_printing_type->id . "._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $default_printer_id=in_array($packing_type_label_printing_type->size,["A4","A5"])?$worker->default_printer_id:$worker->default_label_printer_id;
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $pallet->getCodeNumber() . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $default_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }
//return $html[0];
        return ["html" => $html, "print_file" => $print_file];
    }

    public static function direct_print(Pallet $pallet, Worker $worker,$packing_type_label_printing_type_id=302)
    {

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($packing_type_label_printing_type_id);
        $result = self::create_pdf_file($pallet, $worker, "print", $packing_type_label_printing_type);
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $pallet->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
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


}
