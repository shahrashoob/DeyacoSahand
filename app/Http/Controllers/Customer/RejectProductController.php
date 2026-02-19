<?php

namespace App\Http\Controllers\Customer;

use App\Events\Form\PackingLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductReasonType;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RejectProductController extends Controller
{
    // مرجوع کردن کالا
    public static $info = [
        "route" => "customer_group.order.reject_product.",
        "view" => "customer.group.reject_product.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "customer_group.order.";

    //
    public function __construct()
    {
        $this->route_path = RejectProductController::$info["route"];
        $this->view_path = RejectProductController::$info["view"];
    }

    public function index(Order $order, Form $form)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result = self::allowFormReject($order, $form);
        if (!$result["result"]) { // تایید شده
            return redirect()->route($this->dashboard_route . "view_form", [$order, $form]);
        }

        return self::GetIndex($order, $form, $this->route_path, $this->dashboard_route . "show");
    }

    public static function GetIndex(Order $order, Form $form, $route_path, $dashboard_route)
    {
        $packing_form_item_ids = FormItem::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();

        $packing_form_ids = PackingFormItem::whereIn("id", $packing_form_item_ids)->pluck("packing_form_id")->toArray();

        $packing_form_list = PackingForm::whereIn("id", $packing_form_ids)->orderByDesc("updated_at")->get();

        $checking_carrier_at_delivery_of_product_customer = true;
        foreach ($form->item as $item) {
            $checking_carrier_at_delivery_of_product_customer = $checking_carrier_at_delivery_of_product_customer && $item->product->goods_kind->checking_carrier_at_delivery_of_product_customer;
        }
        $percent_allow_to_reject_packing_form = Setting::getIntegerValue("percent_allow_to_reject_packing_form");


        return view("customer.group.reject_product.index", compact(
                "order", "percent_allow_to_reject_packing_form", "form",
                "packing_form_list", "checking_carrier_at_delivery_of_product_customer",
                "route_path", "dashboard_route")
        );

    }

    public function step1(Request $request, Order $order, Form $form)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result = RejectProductController::allowFormReject($order, $form);
        if (!$result["result"]) { // تایید شده
            return back()->withErrors($result["message"]);
        }

        return self::PostStep1($request, $order, $form, $this->route_path, $this->dashboard_route . "show");
    }

    public static function PostStep1(Request $request, Order $order, Form $form, $route_path, $dashboard_route)
    {
        $data = $request->data;

        if (!isset($data["reject_packing"]) || count($data["reject_packing"]) == 0) {
            return redirect()->route($route_path . "index", [
                $order,
                $form
            ])->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }

        // بررسی اینکه مقدار باقی مانده هر بسته بندی به درستی انتخاب شده باشد.
        $percent_allow_to_reject_packing_form = Setting::getIntegerValue("percent_allow_to_reject_packing_form");
        foreach ($data["reject_packing"] as $packing_form_id => $on) {

            $packing_form = PackingForm::find($packing_form_id);

            if ($data["amount_remaining"][$packing_form_id]) {

                $min_must_exist = $packing_form->getFinalAmount() * $percent_allow_to_reject_packing_form / 100;
                if ($data["amount_remaining"][$packing_form_id] < $min_must_exist) {
                    return redirect()->route($route_path . "index", [
                        $order,
                        $form
                    ])->withErrors("مقدار باقی مانده بسته بندی " . $packing_form->getCode() . " حداقل باید " . round($min_must_exist) . " مقدار باشد.");
                }
            }
            $count_product_id = PackingFormItem::where("packing_form_id", $packing_form_id)->distinct("product_id")->count();
            if ($count_product_id != 1) {
                return redirect()->route($route_path . "index", [
                    $order,
                    $form
                ])->withErrors("ثبت مرجوعی برای بسته بندی هایی قابل ثبت است که فقط یک نوع کالا در آنها استفاده شده باشد.");

            }
            if (!$packing_form->allowRejectProduct()) {
                return redirect()->route($route_path . "index", [
                    $order,
                    $form
                ])->withErrors("ثبت مرجوعی برای بسته بندی " . $packing_form->code . " قبلا ثبت شده است.");

            }
        }


        $reject_product_reason_type_option = Option::get("reject_product_reason_type");

        $reject_packing_list = PackingForm::whereIn("id", array_keys($data["reject_packing"]))->get();

        $amount_remaining = $data["amount_remaining"];

        $reject_product_description = Setting::getStringValue("reject_product_description");

        return view("customer.group.reject_product.step1", compact("order", "form", "amount_remaining",
            "reject_packing_list", "reject_product_reason_type_option",
            "reject_product_description", "route_path", "dashboard_route"));

    }

    public function confirm(Request $request, Order $order, Form $form)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result = RejectProductController::allowFormReject($order, $form);
        if (!$result["result"]) { // تایید شده
            return back()->withErrors($result["message"]);
        }

        return self::PostConfirm($request, $order, $form, $this->route_path, $this->dashboard_route . "show");

    }

    public static function PostConfirm(Request $request, Order $order, Form $form, $route_path, $dashboard_path)
    {

        $data = $request->data;

        if (!isset($data["reject_packing"]) || count($data["reject_packing"]) == 0) {
            return redirect()->route($route_path . "index", [
                $order,
                $form
            ])->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");

        }

        $packing_form_reject_list = [];
        $packing_is_safe = [];

        // چک کردن اینکه بسته بندی قبلا مرجوع نشده باشد
        $before_reject = RejectProductFormItem::
        join("reject_product_forms", "reject_product_forms.id", "reject_product_form_id")->
        whereNotIn("status_id", [7009002])->//کنسل شده
        whereIn("packing_form_id", $data["reject_packing"])->first();
        if ($before_reject) {
            return redirect()->route($route_path . "index", [
                $order,
                $form
            ])->withErrors("بسته بندی " . $before_reject->packing_form->getCode() . " قبلا مرجوع شده است");

        }

        // بررسی اینکه مقدار باقی مانده هر بسته بندی به درستی انتخاب شده باشد.
        $percent_allow_to_reject_packing_form = Setting::getIntegerValue("percent_allow_to_reject_packing_form");
        foreach ($data["reject_packing"] as $packing_form_id) {
            $packing_form_reject_list[$packing_form_id] = PackingForm::find($packing_form_id);
            $packing_is_safe[$packing_form_id] = 1;

            $packing_form = $packing_form_reject_list[$packing_form_id];

            if ($data["amount_remaining"][$packing_form_id]) {

                $min_must_exist = $packing_form->getFinalAmount() * $percent_allow_to_reject_packing_form / 100;
                if ($data["amount_remaining"][$packing_form_id] < $min_must_exist) {
                    return redirect()->route($route_path . "index", [
                        $order,
                        $form
                    ])->withErrors("مقدار باقی مانده بسته بندی " . $packing_form->getCode() . " حداقل باید " . round($min_must_exist) . " مقدار باشد.");
                }
                $packing_is_safe[$packing_form_id] = 0;
            }
            $count_product_id = PackingFormItem::where("packing_form_id", $packing_form_id)->distinct("product_id")->count();
            if ($count_product_id != 1) {
                return redirect()->route($route_path . "index", [
                    $order,
                    $form
                ])->withErrors("ثبت مرجوعی برای بسته بندی هایی قابل ثبت است که فقط یک نوع کالا در آنها استفاده شده باشد.");

            }
            if (!$packing_form->allowRejectProduct()) {
                return redirect()->route($route_path . "index", [
                    $order,
                    $form
                ])->withErrors("ثبت مرجوعی برای بسته بندی " . $packing_form->code . " قبلا ثبت شده است.");

            }
        }


        //ثبت مرجوعی جدید
        $reject_product_form = RejectProductForm::create([
            "applicant_type_id" => 30,
            "applicant_id" => $order->customer_id,
            "status_id" => 7009008, // در انتظار تایید کارشناس فروش
            "exit_form_id" => $form->id,
            "order_id" => $order->id,
            "reject_product_reason_type_id" => $request->reject_product_reason_type_id
        ]);

        event(new RejectProductLogEvent($reject_product_form, 7009001)); // ثبت مرجوعی


        foreach ($data["reject_packing"] as $packing_form_id => $item) {

            $packing_form = $packing_form_reject_list[$packing_form_id];
            RejectProductFormItem::create(
                [
                    "reject_product_form_id" => $reject_product_form->id,
                    "packing_form_id" => $packing_form->id,
                    "packing_is_safe" => $packing_is_safe[$packing_form_id],
                    "amount_remaining" => $data["amount_remaining"][$packing_form_id]
                ]);


            event(new PackingLogEvent($packing_form, 7007013)); // ثبت مرجوعی
        }

        // بسته بندی هایی که مرجوع می شوند باید عدل آنها هم مرجوع شود که دیگر با هم انتخاب نشوند.
        TransportPackingForm::whereIn("packing_form_id", $data["reject_packing"])->update(["status_id" => 6010106]);

        return redirect()->route($dashboard_path, $order)->with(["success" => "یک درخواست مرجوعی کالا با موفقیت ثبت گردید"]);

    }

    public static function allowFormReject(Order $order, Form $form)
    {

        if ($form->status_id != 500000200) {
            return ["result" => false, "message" => "وضعیت برگ خروج تایید شده نیست"];
        }

        $max_reject_datetime = Carbon::parse($form->updated_at)->addDay($order->customer->the_max_day_for_reject_product);

        if (Carbon::now()->greaterThan($max_reject_datetime) ) { // زمان مجاز گذشته است.
            if (!self::HasSpecialLicense($order, $form)) { // مجوز تایید شده ندارد.
                return ["result" => false, "message" => "با توجه به اینکه مدت زمان مجاز برای مرجوع کردن کالا به پایان رسیده است، امکان ثبت مرجوعی وجود ندارد. ".
                    SpecialLicense::GetLink(15, $order->id, "ثبت درخواست مجوز جهت مرجوع کردن کالا  ", $form->id,Carbon::now()->diffInDays(Carbon::parse($form->updated_at)))
                ];

            }
        }

        return ["result" => true];

    }


    public function submit_send_product(Order $order, RejectProductForm $reject_product_form)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $reject_product_form->status_id = 7009004; // در انتظار ورود به کارخانه(نگهبانی)
        $reject_product_form->save();

        event(new RejectProductLogEvent($reject_product_form, 7009005)); // ارسال کالا (درخواست کننده)

        return back()->with(["success" => "ارسال کالا با موفقیت ثبت گردید."]);

    }

    public function download_form(Order $order, RejectProductForm $reject_product_form)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $static_ip = Setting::getStringValue("static_ip");
        $local_ip = url("");

        $url = route("DCRG_SortLink", [$reject_product_form, $reject_product_form->getRandom()]);

        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ($static_ip != "") {
            $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
        }
        $qr = QrCode::size(100)->generate($url);
        $software_name = Setting::getStringValue("software_name");
        $html[0] = view($this->view_path . "print._head")->render();
        $html[0] .= view($this->view_path . "print._print_info",
                compact("reject_product_form", "order", "qr", "software_name"))->render() . $html[0];
        $html[0] .= view(ExitFormController::$view_path . "print._footer")->render();

        Pdf::createAsHtml($html, "P", $reject_product_form->id, "A4", "");


    }

    public function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();

        // باید مشتری باشد.
        if (!$customer) {
            return back()->withErrors("صفحه مورد نظر یافت نشد");
        }
        if ($order->customer_id != ($customer->id ?? 0)) {

            return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
        }


    }


    public static function HasSpecialLicense(Order $order, Form $form)
    {

        $special_license = SpecialLicense::where([
            "special_license_type_id" => 15,
            "reference_id" => $order->id,
            "param1" => $form->id,
            "status_id" => 6040002, // تایید شده
        ])->
        get();
        foreach ($special_license as $license) {
            // اگر زمان ارسال مرجوعی از زمان حال بزرگتر است، مجوز دارد، در غیراین صورت باید مجوز دیگری ثبت نمایید.
            if (Carbon::parse($license->created_at)->addDay($license->param3)->greaterThan(Carbon::now())) {

                return true;
            }
        }

        return false;

    }

}
