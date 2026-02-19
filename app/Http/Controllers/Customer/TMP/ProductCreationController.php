<?php

namespace App\Http\Controllers\Customer\TMP;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductCreation\MethodOfSendingProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessLog;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCreationController extends Controller
{
    //
    var $view_path;
    var $route_path;

    public function __construct()
    {
        $this->route_path = "customer_group.tmp.product_creation.";
        $this->view_path = "customer.tmp.product_creation.";
    }

    public function index()
    {

        $worker = Worker::find(\Auth::id());

        $list = ProductCreationProcess::
        //  فقط درخواست های خودش را ببیند.
        where("user_id", $worker->id)->
        paginate();

        return view("customer.product_creation.dashboard.index", compact("list"));

    }

    public function create()
    {

        $worker = Worker::find(Auth::id());

        $customer = Customer::where("user_id", $worker->id)->first();
        if (!$customer) {
            return back()->withErrors("مشخصات مشتری برای شما یافت نشد.");
        }
        if ($customer->status_id == 2001001) {
            return redirect()->route("customer_group.tmp.definition_customer.edit")->withErrors("لطفا ابتدا اطلاعات پروفایل را تکمیل نمایید.");
        }


        $method_of_sending_product_list = MethodOfSendingProduct::get();
        $product_service_type_option = Option::get("product_service_type", 1);
        $goods_kind_option = Option::get("goods_kind");
        $product_creation_process_id = false;

        return view($this->view_path . "create", compact("method_of_sending_product_list", "goods_kind_option", "product_service_type_option", "product_creation_process_id"));
    }

    public function submit(Request $request)
    {


        if (!isset($request->image_file)) {
            return back()->withErrors(__("message.please upload product image"));
        }
        $validator = $request->validate([
            'image_file' => 'max:' . (1024 * 10),
        ]);

        if ($request->caption == "" || ProductCreationProcess::Exists($request->caption, false, "caption")) {
            return back()->withErrors(__("message.the suggested name is duplicate/invalid"));
        }

        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors(__("message.the product name can have a maximum of 8 space characters"));
        }


        $product_creation_process = ProductCreationProcess::create([
            "caption" => $request->caption,
            "user_id" => Auth::id(),
            "goods_kind_id" => $request->goods_kind_id,
            "method_of_sending_product_id" => $request->method_of_sending_product_id,
            "product_service_type_id" => $request->product_service_type_id,
            "has_physical_sample" => $request->has_physical_sample,
            "status_id" => $request->has_physical_sample ?
                5231001 : // در انتظار ارسال نمونه پارچه
                5231002 // در انتظار تکمیل اطلاعات پایه
        ]);

        $file = File::uploadFile($request->file('image_file'), $product_creation_process->id . "_" . rand(1000, 9000) . ".png", 42, "upload/product_creation/", true);

        $product_creation_process->file_id = $file->id;

        if ($request->has_physical_sample) {
            switch ($product_creation_process->method_of_sending_product_id) {
                case 1: // تحویل حضوری
                    $product_creation_process->status_id = 5231001; // در انتظار ارسال نمونه کالا
                    break;
                case 2: // خدمات پستی
                    $product_creation_process->status_id = 5231004; // در انتظار ثبت اطلاعات پستی
                    break;

            }
        }

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231001));
        $product_creation_process->save();

        return redirect()->route($this->route_path . "view", $product_creation_process)->with(["success" => __("message.a product creation request was successfully submitted")]);

    }


    public function view(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $worker = Worker::find(Auth::id());
        $customer = Customer::where("user_id", $worker->id)->first();


        return view($this->view_path . "view", compact("product_creation_process", "worker", "customer"));

    }

    public function log(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $list = ProductCreationProcessLog::where("product_creation_process_id", $product_creation_process->id)->paginate();

        return view($this->view_path . "log", compact("product_creation_process", "list"));

    }


    public function download(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $label_printer_size = PackingTypeLabelPrintingType::find(3);

        Pdf::createAsHtml(ProductCreation\DashboardController::getPdfFile($product_creation_process, "line_product_station.product.product_creation.dashboard."),
            "P",
            $product_creation_process->getCode() . ".pdf", "A5"
        );
    }

    public function submit_post_tracking_code(Request $request, ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        if ($request->tracking_code == "") {
            return back()->withErrors(__("message.please enter the tracking code correctly"));
        }
        if ($product_creation_process->status_id != 5231004) {
            return back()->withErrors(__("message.the tracking code has already been registered"));
        }
        $product_creation_process->status_id = 5231001; // در انتظار ارسال نمونه کالا

        $product_creation_process->save();
        event(new ProductCreationProcessLogEvent($product_creation_process, 5231006, $request->tracking_code));

        return back()->with(["success" => __("message.tracking code registered successfully")]);
    }

    public function checkPermission($product_creation_process)
    {
        if ($product_creation_process->user_id != Auth::id()) {
            return back()->withErrors(__("message.the design form does not exist in your dashboard"));
        }


    }
}
