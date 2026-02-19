<?php

namespace App\Http\Controllers\Supplier\TrustProduct;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Order\OrderPackingForm;
use App\Models\Production\Production;
use App\Models\Production\ProductionLog;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Hekmatinasser\Jalali\Jalali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TrustProductAllocation extends Controller
{
    public static $info = [
        "route" => "supplier.trust_product.trust_product_allocation.",
        "view" => "supplier.trust_product.trust_product_allocation.",
        "enable_status" => ["001", "002"],
        "button" => ["caption" => "تخصیص مشتری", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "supplier.trust_product.dashboard.";

    public function __construct()
    {
        $this->route_path = TrustProductAllocation::$info["route"];
        $this->view_path = TrustProductAllocation::$info["view"];
    }

    public function index(Production $production)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

//        if ($production->packing_types()->count() == 0) {
//            return back()->withErrors("بسته بندی مجاز فروش برای پیمان مشخص نشده است، لطفا با پشتیبانی تماس بگیرد.");
//        }


        $allocation_amount = $production->number - $production->get_allocation_amount(false, 3);

        if ($allocation_amount <= 0) {
            return back()->withErrors("کل مقدار کارت تامین (کالای امانی) به مشتری تخصیص داده شده است، و امکان تخصیص جدید وجود ندارد.");
        }

        $min_date = jdate(Carbon::now()->timestamp)->format("Y/m/d");

        return view($this->view_path . "index", compact("production", "allocation_amount", "min_date"));
    }

    public function submit(Request $request, Production $production)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        // تاریخ هماهنگی ارسال (همان تاریخ پیش بینی شروع تولید (عملی)
        $predict_of_production_start_date_practical = $request->predict_of_production_start_date_practical;
        $result_allocation = self::PostSubmit($production, $production->order, $request->allocation_amount, $predict_of_production_start_date_practical);
        if ($result_allocation["result"]) {
            return redirect()->route($this->dashboard_route . "view_card", $production)->with(["success" => $result_allocation["message"]]);

        } else {
            return back()->withErrors($result_allocation["error"]);
        }


    }

    public static function PostSubmit(Production $production, $order, $allocation_amount, $predict_of_production_start_date_practical, $user_id = null)
    {
        $message_log = "";
        $allocation_amount_remaining = $production->number - $production->get_allocation_amount(false, 3);
        if ($allocation_amount_remaining <= 0) {
            return [
                "result" => false,
                "error" => "کل مقدار کارت تامین به مشتری تخصیص داده شده است، و امکان تخصیص جدید وجود ندارد."
            ];

        }
        if (!$order) {
            return [
                "result" => false,
                "error" => "شناسه سفارش نامعتبر است، لطفا با پشتیبانی تماس بگیرید."
            ];
        }
        if ($allocation_amount > $allocation_amount_remaining) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص درخواست شده از  مقدار باقی مانده کارت پیمان بیشتر است."
            ];
        }

        $line_product_stations = LineProductStation::
        where("product_id", $production->product_id)->
            where("customer_id", $order->customer_id)->
        whereNotNull("customer_id")->
        get();
        if (count($line_product_stations) == 0) {
            return [
                "result" => false,
                "error" => " با توجه به اینکه برای کالای " . $production->product->caption . " هیچ مسیر محصولی تعریف نشده است، امکان تخصیص به مشتری وجود ندارد."
            ];
        }
        if (count($line_product_stations) != 1) {
            return [
                "result" => false,
                "error" => " با توجه به اینکه برای کالای " . $production->product->caption . " بیش از یک مسیر محصول تعریف شده است، امکان تخصیص به مشتری وجود ندارد."
            ];
        }
        $line_product_station = $line_product_stations[0];
        $customer = $line_product_station->customer;

        //        // بررسی تاریخ قرارداد
//        if (!$customer->end_date_of_contract || !$customer->start_date_of_contract) {
//            return [
//                "result" => false,
//                "error" => "تاریخ شروع و پایان قرارداد برای مشتری ثبت نشده است، لطفا با واحد پشتیبانی تماس بگیرید."
//            ];
//        }
//        if (
//            Carbon::now()->greaterThan(Carbon::parse($customer->end_date_of_contract)) ||
//            Carbon::now()->lessThan(Carbon::parse($customer->start_date_of_contract))
//        ) {
//            return [
//                "result" => false,
//                "error" => "تاریخ قرارداد با مشتری نامعتبر است، لطفا نسبت به تمدید قرارداد با مشتری اقدامات لازم مبذول فرمایید. "
//            ];
//        }

//        // بررسی ارتباط با سامانه مشتری
        if ($customer->software_system_id) {

            $result_api_login = SoftwareSystem::Login($customer->software_system, $customer->api_url, $customer->api_username, $customer->api_password, $customer->api_key);
            if (!$result_api_login["result"]) {
                return [
                    "result" => false,
                    "error" => $result_api_login["message"]
                ];
            }


            // ارسال درخواست هماهنگی
            if (!$production->parent_production) {
                return [
                    "result" => false,
                    "error" => "کارت سطح بالای کارت تامین " . $production->serial() . " یافت نشد، بنابراین امکان هماهنگی برای پیمانکار وجود ندارد."
                ];
            }
            if (!$production->parent_production) {
                return [
                    "result" => false,
                    "error" => "کارت سطح بالای کارت تامین " . $production->serial() . " یافت نشد، بنابراین امکان هماهنگی برای پیمانکار وجود ندارد."
                ];
            }
            if (!$production->parent_production->order_list) {
                return [
                    "result" => false,
                    "error" => "سفارش مربوط به کارت " . $production->serial() . " یافت نشد، بنابراین امکان هماهنگی برای پیمانکار وجود ندارد."
                ];
            }

            // به دست آوردن بسته بندی های پیشنهادی
            $order_id = $production->parent_production->order_list->order_id;
            $suggested_packing_codes = OrderPackingForm::
            where("order_id", $order_id)->
            pluck("packing_form_code")->
            toArray();

//return $production->parent_production->order_list;
            $message = "هماهنگی ارسال کالا با شماره سفارش "
                . $production->parent_production->order->code()
                . " و کارت تولید " . $production->parent_production->serial() . " در " . Setting::getStringValue("software_name");
            // هماهنگی ارسال کالا ( تخصیص کارت تامین (کالای امانی)
            $result_api_product_creation = SoftwareSystem::CallCoordinationForSending(
                $customer->software_system,
                $customer->api_url,
                $result_api_login["token"],
                $production->parent_production->order_list->tracking_code1,
                $production->parent_production->order_list->tracking_code2,
                $predict_of_production_start_date_practical,
                $message,
                $suggested_packing_codes,
                $customer->id,
                $order_id
            );

            if (!$result_api_product_creation["result"]) {
                SoftwareSystem::Logout(
                    $customer->software_system,
                    $customer->api_url,
                    $result_api_login["token"]
                );
                return [
                    "result" => false,
                    "error" => "خطای " . $customer->software_system->caption . " در " . $customer->caption . ": <br/>" . $result_api_product_creation["error"] . "<br/>"
                ];
            }


            // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
            SoftwareSystem::Logout(
                $customer->software_system,
                $customer->api_url,
                $result_api_login["token"]
            );

        }

        $allocation_status_id = 5312101; // ارسال مواد اولیه توسط مشتری
        $production_status_id = 7011002; // ارسال مواد اولیه


        $allocation = Allocation::create([
            "order_id" => $order->id,
            "status_id" => $allocation_status_id
        ]);
        $allocation->predict_of_production_start_date_practical = $predict_of_production_start_date_practical;
        $allocation->save();

        $machine_allocation = MachineAllocation::create([
            "allocation_id" => $allocation->id,
            "production_id" => $production->id,
            "order_id" => $order->id,
            "product_id" => $production->product_id,
            "status_id" => $allocation_status_id,
            "user_id" => Auth::user()->id ?? $user_id,
            "allocation_amount" => $allocation_amount
        ]);

        if ($production->waiting_status_id == 7011001) { // تخصیص مشتری
            $production->status_id = 500; // در انتظار
            $production->waiting_status_id = $production_status_id;
            $production->save();
        }
        event(new ProductionCardLogEvent($machine_allocation->production, "", $user_id));


        event(new ContractorLogEvent(null, 5312101, $production, $machine_allocation, $message_log, $user_id, $order));


        return [
            "result" => true,
            "message" => "تخصیص با موفقیت ثبت گردید.",
            "production_id" => $production->id,
            "allocation_id" => $allocation->id
        ];


    }

    public function terminate(Production $production)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }
        if ($production->status_id == 520) {
            return back()->withErrors("این کارت قبلا خاتمه یافته شده است.");
        }

        $production->status_id = 520; //خاتمه یافته
        $production->waiting_status_id = 7011003; //خاتمه یافته
        $production->save();

        event(new ProductionCardLogEvent($production, "", Auth::id()));

        return back()->with(["success" => "کارت تامین ما موفقیت خاتمه یافته گردید."]);
    }

    public function checkPermission(Production $production)
    {

        $result = DashboardController::checkPermissionConditions($production, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }


}
