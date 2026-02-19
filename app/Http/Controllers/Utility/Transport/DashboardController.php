<?php

namespace App\Http\Controllers\Utility\Transport;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Order\Order;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    // بسته بندی حمل و نقل ویژه دوره پیازده سازی

    var $view_path = "utility.transport.dashboard.";
    var $route_path = "utility.transport.dashboard.";
    var $printer_type_lable_printer_type;

    public function index(Request $request)
    {


        $search = $request->search;

        $start_date_time = null;

        $end_date_time = null;

        if ($request->start_date) {
            $start_date_time = DateTime::getDateTimeFromRequest($request, "start_date");

        }
        if ($request->end_date) {
            $end_date_time = DateTime::getDateTimeFromRequest($request, "end_date");

        }


        $list = Transport::orderByDesc("transports.id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where("order_code", "like", "%" . $search . "%");
                $query->orWhere("series", "like", "%" . $search . "%");
                $query->orWhere("customer_caption", "like", "%" . $search . "%");
                $query->orWhere("transports.code", "like", "%" . $search . "%");
            });
        })->
        whereNotNull("order_code")->
        when($start_date_time, function ($query) use ($start_date_time) {
            return $query->where("transports.created_at", ">=", $start_date_time);
        })->
        when($end_date_time, function ($query) use ($end_date_time) {
            return $query->where("transports.created_at", "<=", $end_date_time);
        })->
        select("transports.id", "transports.status_id", "customer_caption", "order_id", "transports.created_at")->
        paginate();


        return view($this->view_path . "index", compact("list", "search", "start_date_time", "end_date_time"));

    }

    public function create_transport($customer_caption = false, $series = false, $order_code = false)
    {

        return view($this->view_path . "create_transport", compact("customer_caption", "series", "order_code"));
    }


    public function store_transport(Request $request)
    {

        if (!Transport::where("customer_caption", $request->customer_caption)->exists() && $request->new_customer == 0) {
            $customer_caption = $request->customer_caption;
            $series = $request->series;
            $order_code = $request->order_code;

            return redirect()->route($this->route_path . "create_transport", [
                $customer_caption,
                $series,
                $order_code
            ]);
        }

        $order = Order::where(["code" => $request->order_code, "series" => $request->series])->first();
        if (!$order && $request->order_exist) {
            return back()->withErrors("شماره سفارش در سامانه یافت نشد.");
        }
        $transport = Transport::create([
            "order_code" => $request->order_code ?? "000",
            "series" => $request->series ?? 0,
            "status_id" => 6010001,//ثبت موفقت
            "order_id" => $order->id ?? null,
            "customer_caption" => $request->customer_caption,
            "user_id" => Auth::id()
        ]);

        return redirect()->route($this->route_path . "transport_item", $transport)->with(["یک ارسال بار جدید ایجاد گردید."]);
    }

    public function transport_item(Transport $transport)
    {

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $list = PackingFormItem::
        join("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where("transport_id", $transport->id)->
        groupBy("product_id")->
        addSelect(DB::raw("packing_form_item.id as id,sum(final_amount) as amount,sum(sub_amount) as sub_amount, product_id,count( DISTINCT  packing_form_item.packing_form_id) as packing_form_count"))->get();


        return view($this->view_path . "transport_item", compact("transport","list"));
    }

    public function create_transport_item(Transport $transport)
    {

        $item = TransportItem::create([
            "transport_id" => $transport->id,
            "user_id" => Auth::id(),
            "status_id" => 6010001
        ]);


        return redirect()->route($this->route_path . "packing_list", $item);
    }

    public function packing_list(TransportItem $transport_item)
    {

        return view($this->view_path . "packing_list", compact("transport_item"));
    }

    public function add_packing_form_to_transport_item(Request $request)
    {
        $transport = Transport::find($request->transport_id);
        $transport_item = TransportItem::find($request->transport_item_id);
        $user_id = $request->user_id;

        $packing_form = PackingForm::where("code", "DCPK/" . $request->packing_form_code)->first();

        $message = "";
        if (!$transport || !$transport_item || $transport->id != $transport_item->transport_id || $transport->user_id != $user_id) {
            $message = "اطلاعات ارسال شده نامعتبر است، لطفا دوباره تلاش کنید.";
        }
        if ($transport->status_id == 6010002) {
            $message = "این ارسال بار قبلا تایید نهایی شده و امکان تغییر در آن وجود ندارد.";
        }

        if (!$packing_form) {
            $message = "کد بسته بندی وارد شده، در سیستم وجود ندارد.";
        }
//        if ( $packing_form && ( $packing_form->warehouse_status_id != 4201 || $packing_form->status_id != 7007003 ) ) {
//            $message = "بسته بندی مورد نظر در  انبار وجود ندارد.";
//        }

        if ($packing_form && $packing_form->status_id != 7007003) {
            $message = "وضعیت ".$packing_form->status->caption."بسته بندی " . $packing_form->code . " (" . $packing_form->status->caption . ") جهت افزودن به عدل نامعتبر است.";
        }

        $transport_packing_form = TransportPackingForm::where("packing_form_id", $packing_form->id ?? 0)->first();
        if ($transport_packing_form) {
            $message = "این بسته بندی قبلا در ارسال بار کد " . $transport_packing_form->transport->getCode() . " استفاده شده است.";
        }

        if ($message == "") {
            TransportPackingForm::create([
                "transport_id" => $transport->id,
                "packing_form_id" => $packing_form->id,
                "transport_item_id" => $transport_item->id
            ]);
        }

        return view($this->view_path . "_packing_add", compact("transport_item", "message"));
    }

    public function delete_packing_form(TransportItem $transport_item, TransportPackingForm $transport_packing_form)
    {

        if ($transport_packing_form->transport_item_id == $transport_item->id) {
            $transport_packing_form->delete();

            return back()->with(["success" => "حذف با موفقیت انجام شد."]);
        }

        return back()->withErrors("حذف انجام نشد.");
    }

    public function transport_confirm(TransportItem $transport_item, $type)
    {

        if ($transport_item->transport_packing_list()->count() == 0) {
            return back()->withErrors("حداقل یک بسته بندی را انتخاب کنید و سپس ثبت موقت کنید.");
        }



        $transport_item->status_id = 6010001;
        $transport_item->save();

        if ($type == "confirm_print_new" || $type == "confirm_print_back" || $type == "print_back") {
            $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
            $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);
            //print
            $result = DashboardController::create_pdf_file($transport_item, "print",$transport_item_label_type_id);
            Pdf::labelPrinter($result["html"],
                $transport_item_label_type->orientation,
                $transport_item->id, [$transport_item_label_type->width, $transport_item_label_type->long],
                $result["print_file"]
            );

            if($type == "print_back"){
                return redirect()->route($this->route_path . "transport_item", $transport_item->transport_id)->with(["success" => "پرینت با موفقیت ارسال شد."]);

            }

        }
        if ($type == "confirm_print_new" || $type == "confirm_new") {
             $this->create_transport_item($transport_item->transport);
        }

        return redirect()->route($this->route_path . "transport_item", $transport_item->transport_id)->with(["success" => "ثبت موقت با موفقیت انجام شد."]);
    }

    public function transport_final_confirm(TransportItem $transport_item, $type)
    {

        if ($transport_item->transport_packing_list()->count() == 0) {
            return back()->withErrors("حداقل یک بسته بندی را انتخاب کنید و سپس ثبت نهایی کنید.");
        }
        if ($transport_item->status_id != 6010001) {
            return back()->withErrors("این بسته بندی هنوز تایید موقت نشده است.");
        }

        $transport_item->status_id = 6010002; // ثبت نهایی
        $transport_item->save();

        return redirect()->route($this->route_path . "transport_item", $transport_item->transport_id)->with(["success" => "ثبت نهایی با موفقیت انجام شد."]);
    }

    public function transport_item_delete(TransportItem $transport_item)
    {

        if ($transport_item->status_id == 6010002) {
            return back()->withErrors("با توجه به اینکه بسته بندی ارسال بار تایید نهایی شده است، امکان حذف وجود ندارد.");
        }

        TransportPackingForm::where("transport_item_id", $transport_item->id)->delete();
        $transport_item->delete();

        return back()->with(["success" => "حذف با موفقیت انجام شد."]);

    }

    public function DCLP_QR(TransportItem $transport_item, $key)
    {

        if ($transport_item->random != $key) {
            return back()->withErrors("آدرس نامعتبر است.");
        }

        return $transport_item;
    }

    public function DCBL_QR(Transport $transport, $key)
    {
        if ($transport->random != $key) {
            return back()->withErrors("صفحه مربوط به بار یافت نشد.");
        }

        return view($this->view_path . "show_transport_qr", compact("transport"));

    }

    public function download(TransportItem $transport_item)
    {

        $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
        $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);
        $result = DashboardController::create_pdf_file($transport_item, "download",$transport_item_label_type_id);
        Pdf::labelPrinter($result["html"],
            $transport_item_label_type->orientation,
            $transport_item->id, [$transport_item_label_type->width, $transport_item_label_type->long],
        );
    }

    public function print(TransportItem $transport_item)
    {

        $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
        $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);
        $result = DashboardController::create_pdf_file($transport_item, "print",$transport_item_label_type_id);
        Pdf::labelPrinter($result["html"],
            $transport_item_label_type->orientation,
            $transport_item->id, [$transport_item_label_type->width, $transport_item_label_type->long],
            $result["print_file"]
        );
    }


    public function download_report1(Transport $transport)
    {
        // گزارش براساس کد کالا
        $header_text = Setting::getStringValue("transport_loading_header_text");


        $list = PackingFormItem::
        join("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where("transport_id", $transport->id)->
        groupBy("product_id")->
        addSelect(DB::raw("packing_form_item.id as id,sum(final_amount) as amount,sum(sub_amount) as sub_amount, product_id,count( DISTINCT  packing_form_item.packing_form_id) as packing_form_count"))->get();

        $view_path = "utility.transport.print.print_report1.";
        $html = [];
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_transport", compact("transport", "list", "header_text"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();
        Pdf::createAsHtml($html,
            "P",
            $transport->getCode(),
            "A4",
            " "
        );
    }

    public function download_report2(Transport $transport)
    {
        // گزارش براساس بسته بندی
        $header_text = Setting::getStringValue("transport_loading_header_text");


        $list = PackingFormItem::
        join("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where("transport_id", $transport->id)->
        groupBy("packing_form_item.id")->
        addSelect(DB::raw("packing_form_item.id as id,sum(final_amount) as amount,sum(sub_amount) as sub_amount, product_id,packing_form_item.code"))->get();

        $view_path = "utility.transport.print.print_report2.";
        $html = [];
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_transport", compact("transport", "list", "header_text"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();
        Pdf::createAsHtml($html,
            "P",
            $transport->getCode(),
            "A4",
            " "
        );
    }

    public static function create_pdf_file(TransportItem $transport_item, $type,$transport_item_label_type_id)
    {

        $static_ip = Setting::getStringValue("static_ip");
        $local_ip = url("");
        $url = route("DCLP_QR", [$transport_item, $transport_item->getRandom()]);
        $worker = Auth::user();

        $header_text = Setting::getStringValue("transport_loading_header_text");
        $date_time = jdate(Carbon::parse($transport_item->created_at)->timestamp)->format('H:i Y/m/d ');
        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ($static_ip != "") {
            $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
        }
        $qr = QrCode::size(100)->generate($url);
        $barcode = DNS1D::getBarcodeSVG($transport_item->codeNumber(), 'C39', 1.6, 50);

        $view_path = "utility.transport.print.transport_item.";
        $html[0] = view($view_path . "_head",["font_size"=>12])->render();
        $html[0] .= view($view_path . "_print_info_type".$transport_item_label_type_id, compact("transport_item", "qr", "header_text", "date_time", "barcode"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $transport_item->code() . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_label_printer_id
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

    public function set_to_warehouse(Transport $transport)
    {

         \App\Http\Controllers\Warehouse\DashboardController::SetToWarehouse($transport);
         return back()->with(["success" => "برای تمامی بسته بندی های در انتظار تایید انبار، تایید انبار صادر گردید."]);
    }

}
