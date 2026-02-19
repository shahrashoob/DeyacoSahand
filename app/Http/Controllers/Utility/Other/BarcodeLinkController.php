<?php

namespace App\Http\Controllers\Utility\Other;

use App\Http\Controllers\Controller;
use App\Models\Utility\Option;
use App\Models\Utility\Other\BarcodeLink;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeLinkController extends Controller
{

    private $view_path = "utility.other.barcode_link.";
    private $route_path = "utility.other.barcode_link.";

    public function index(Request $request)
    {

        $list = BarcodeLink::paginate(20);


        $order_by_Option = null;
        $search = "";

        return view($this->view_path . "index", compact("list", "search", "order_by_Option"));
    }

    public function create()
    {

        $barcode_link = new BarcodeLink();

        $status_option = Option::get("status", 0, 1100);
        $customer_option = Option::get("customer", );

        return view($this->view_path . "create", compact("barcode_link", "status_option","customer_option"));
    }

    public function store(Request $request)
    {

        if ($request->caption == "" || BarcodeLink::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان بارکد  تکراری است");
        }
        $barcode = BarcodeLink::create($request->all());
        if($request->customer_id){
            $barcode->barcode_link_type_id = 2; // نوع بارکد عضویت  سطح دو مشتریان
        }
        $barcode->code = $barcode->id . Str::random(7);
        $barcode->save();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک بارکد با موفقیت اضافه شد"]);

    }

    public function edit(BarcodeLink $barcode_link)
    {

        $status_option = Option::get("status", $barcode_link->status_id, 1100);
        return view($this->view_path . "edit", compact("barcode_link", "status_option"));

    }

    public function update(Request $request, BarcodeLink $barcode_link)
    {
        if ($request->caption == "" || BarcodeLink::ExistsCode($request->caption, $barcode_link->id)) {
            return back()->withErrors("عنوان تکراری است");
        }
        $barcode_link->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function download_qr(BarcodeLink $barcodeLink)
    {

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("BarcodeLink_QR", [$barcodeLink->code]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


         $qr = QrCode::size(500)->generate($url);



        $html[0] = view( "utility.other.barcode_link.print._header")->render();
        $html[0] .= view( "utility.other.barcode_link.print._print_info", compact("qr", ))->render() . $html[0];
        $html[0] .= view( "utility.other.barcode_link.print._footer")->render();

        Pdf::labelPrinter( $html,"L",$barcodeLink->id.".pdf","A5");




    }
}
