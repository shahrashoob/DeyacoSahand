<?php

namespace App\Http\Controllers\Utility\Transport;

use App\Http\Controllers\Controller;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Transport\Transport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintQRController extends Controller
{


    public static function download_transport(Transport $transport)
    {
        $result = self::create_pdf_file($transport, "download");
        Pdf::labelPrinter($result["html"],
            "P",
            $transport->id, [115, 165]
        );
    }

    public static function print_transport(Transport $transport)
    {
        $result = self::create_pdf_file($transport, "print");
        Pdf::labelPrinter($result["html"],
            "P",
            $transport->id, [115, 165],
            $result["print_file"]
        );
    }

    public static function create_pdf_file(Transport $transport, $type)
    {

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");
        $url = route("DCBL_QR", [$transport, $transport->getRandom()]);
        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $worker = Auth::user();

        $header_text = Setting::getStringValue("transport_loading_header_text");
        $date_time = jdate(Carbon::parse($transport->created_at)->timestamp)->format('H:i Y/m/d ');

        $qr = QrCode::size(100)->generate($url);
        $barcode = DNS1D::getBarcodeSVG($transport->codeNumber(), 'C39', 1.6, 50);

        $forms_ids = $transport->transport_forms()->pluck("form_id")->toArray();
        $forms_ids[] = -1;
        $product_list = FormItem::join("products", "products.id", "product_id")->
        join("units", "unit_id", "units.id")->
        whereIn("form_id", $forms_ids)->
        groupBy("product_id")->
        selectRaw("products.id,products.code,products.caption, sum(form_item.amount) as sum_amount,units.caption as unit_caption")->
        get();

        //به دست آوردن شماره سفارش و نام مشتری
        $list_order_info = ProductRequestForm::
        join("product_request_form_form", "product_request_forms.id", "=", "product_request_form_id")->
        whereIn("product_request_form_form.form_id", $forms_ids)->
        whereNotNull("order_id")->
        with("order", "order.customer")->select("order_id","product_request_form_form.form_id")->get();

        $packing_form_count = Transport::getPackingFromCount($transport, $forms_ids);

        $result = Transport::getWeight($transport, "product");
        $product_weight = $result["product_weight"];

        $view_path = "utility.transport.print.transport.";
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_info", compact("transport", "qr", "header_text",
                "date_time", "barcode", "product_list", "product_weight", "packing_form_count","list_order_info"))
                ->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $transport->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_printer_id,
                "number_of_prints" => $worker->default_print_number
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }
}
