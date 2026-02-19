<?php

namespace App\Http\Controllers\Warehouse\Transport;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\DeliveryController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormPackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Order\Order;
use App\Models\Utility\DateTime;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    // بسته بندی حمل و نقل ویژه دوره پیازده سازی

    var $view_path = "warehouse.transport.dashboard.";
    var $route_path = "wh.transport.dashboard.";
    public static $not_alloewed_status_id = [7005002, 7005006, 7005009];

    public function index(ProductRequestForm $product_request_form, $page = 1, $dashboard_type = "")
    {

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return back()->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $product_request_form_ids = [$product_request_form->id];
        if ($dashboard_type == "customer") {
            $product_request_form_ids = ProductRequestForm::
            where("applicant_type_id", $product_request_form->applicant_type_id)->
            where("applicant_id", $product_request_form->applicant_id)->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            pluck("product_request_forms.id")->toArray();
            $product_request_form_ids[] = $product_request_form->id;
        }
        $transport_items = TransportItem::whereIn("product_request_form_id", $product_request_form_ids)->with("product_request_form")->get();
        return view($this->view_path . "index", compact("transport_items", "dashboard_type", "product_request_form", "page"));
    }

    public function create_transport_item(ProductRequestForm $product_request_form, $page = 1, $dashboard_type = "")
    {

        $item = TransportItem::create([
            "product_request_form_id" => $product_request_form->id,
            "user_id" => Auth::id(),
            "status_id" => 6010001
        ]);


        return redirect()->route($this->route_path . "packing_list", [$item, $page, $dashboard_type]);
    }

    public function confirm_multi_transport_item(Request $request, ProductRequestForm $product_request_form)
    {

        if (!isset($request->data["transport_item"])) {
            return back()->withErrors("لطفا حداقل یک بسته بندی حمل و نقل را انتخاب نمایید.");
        }

        $list = TransportItem::
        where("product_request_form_id", $product_request_form->id)->
        whereIn("id", array_keys($request->data["transport_item"]))->
        get();

        $message = "";
        $count = 0;
        foreach ($list as $transport_item) {
            $result = $transport_item->confirm();
            if ($result["result"]) {
                $count++;
            } else {
                $message .= $result["message"] . "<br/>";
            }
        }

        if ($message != "") {
            $message .= $count == 0 ? "" : "تعداد " . $count . " بسته بندی تایید نهایی شد.";

            return back()->withErrors($message);
        }

        return back()->with(["success" => "تعداد " . $count . " بسته بندی تایید نهایی شد."]);
    }

    public function packing_list(TransportItem $transport_item, $page = 1, $dashboard_type = "")
    {

        return view($this->view_path . "packing_list", compact("transport_item", "dashboard_type", "page"));
    }

    public function add_packing_form_to_transport_item_product_request_form(Request $request)
    {

        $product_request_form = ProductRequestForm::find($request->product_request_form_id);
        $transport_item = TransportItem::find($request->transport_item_id);
        $user_id = $request->user_id;
        $dashboard_type = $request->dashboard_type;
        $page = $request->page ?? 1;
        $message = "";
        if (in_array($product_request_form->status_id, self::$not_alloewed_status_id)) {
            $message = "با توجه به وضعیت درخواست (" . ($product_request_form->status->caption ?? "") . ") امکان ثبت بسته بندی بارگیری وجود ندارد.";

            return view($this->view_path . "_packing_add", compact("dashboard_type", "transport_item", "message", "page"));
        }

        // چک کردن پالت
        $packing_form_code = strtoupper($request->packing_form_code);
        $pallet_code = trim($packing_form_code, "PC");

        if ("PC" . $pallet_code == $packing_form_code) {
            $pallet = Pallet::find($pallet_code);
            $pallet_code = "PC" . $pallet_code;
            if (!$pallet) {
                $message = " پالت  " . $pallet_code . " در سامانه یافت نشد.";
            }
            if ($pallet && $pallet->status_id != 6080002) {
                $message = " با توجه به اینکه وضعیت پالت $pallet_code " . $pallet->status->caption . " می باشد، امکان ورود به انبار امکان پذیر نمی باشد.";

            }

            if ($message != "") {
                return view($this->view_path . "_packing_add", compact("dashboard_type", "transport_item", "message", "page"));
            } else {
                $result_add_pallet_to_transport_item = self::AddPalletToTransportItem($pallet, $product_request_form, $user_id, $transport_item, $dashboard_type);
                $message = $result_add_pallet_to_transport_item["error"];
                $success_message = null;
                if (isset($result_add_pallet_to_transport_item["success_message"])) {
                    $success_message = $result_add_pallet_to_transport_item["success_message"];
                }

                return view($this->view_path . "_packing_add", compact("dashboard_type", "transport_item", "message", "page", "success_message"));
            }

        } else {
            if ($product_request_form->warehouse->allow_entry_with_pin) {
                // اگر تنظیمات با کد Pin است، باید با پین وارد شود در غیر این صورت کد بسته بندی چک می شود.
                $packing_form = PackingForm::where("pin1", $packing_form_code)->first();
            } else {
                $packing_form = PackingForm::where("code", "DCPK/" . $packing_form_code)->first();
            }
            if (!$packing_form) {


                if ($product_request_form->warehouse->allow_entry_with_pin) {
                    $message = "کد پین " . $packing_form_code . " نامعتبر است. ";
                } else {
                    $message = "بسته بندی DCPK/" . $packing_form_code . " در سیستم وجود ندارد. ";
                }


                return view($this->view_path . "_packing_add", compact("dashboard_type", "transport_item", "message", "page"));
            }
        }


        $message = "";
        if (!$product_request_form || !$transport_item || $product_request_form->id != $transport_item->product_request_form_id || $product_request_form->user_id != $user_id) {
            $message = "اطلاعات ارسال شده نامعتبر است، لطفا دوباره تلاش کنید.";
        }
        if (in_array($transport_item->status_id, [6010002])) {
            $message = "امکان تغییر در بسته بندی بار برای وضعیت " . $transport_item->status->caption . " وجود ندارد.";
        }

        // افزودن بسته بنید به عدل
        $result_add_packing_form_to_transport_item = self::AddPackingFormToTransportItem($packing_form, $product_request_form, $user_id, $transport_item, $dashboard_type);
        $message = $result_add_packing_form_to_transport_item["error"];

        return view($this->view_path . "_packing_add", compact("dashboard_type", "transport_item", "message", "page"));
    }

    public static function CheckPackingTypeIds($product_request_form, $product_ids, $current_packing_type_ids, $product_request_form_ids)
    {
        $message = "";


        $exist_product_id_in_request_ids =
            ProductRequestFormItem::
            whereIn("product_request_form_id", $product_request_form_ids)->
            whereIn("product_id", $product_ids)->
            exists();


        if (!$exist_product_id_in_request_ids) {
            return [
                "result" => false,
                "error" => "<br/>" . "کالای موجود در بسته بندی، جزء کالاهای مجاز در درخواست نمی باشد."
            ];
        }
        $packing_type_ids = ProductRequestFormPackingType::
        whereIn("product_request_form_id", $product_request_form_ids)->
        whereIn("product_id", $product_ids)->
        pluck("packing_type_id")->toArray();

        if (count($packing_type_ids) == 0) {
            return [
                "result" => false,
                "error" => "<br/>" . "کالای موجود در بسته بندی در درخواست وجود ندارد و یا هیچ نوع بسته بندی مجازی برای کالا تعریف نشده است."
            ];
        }

        foreach ($current_packing_type_ids as $current_packing_type_id) {

            if (!in_array($current_packing_type_id, $packing_type_ids)) {

                $current_packing_type = PackingType::find($current_packing_type_id);

                $message = "<br/>" . "نوع بسته بندی " . $current_packing_type->caption . "  معتبر نمی باشد، " .
                    "<br/>لطفا نوع بسته بندی را به یکی از مجاز تغییر دهید:" . "<br/>";

                $packing_type_list = PackingType::whereIn("id", $packing_type_ids)->get();

                foreach ($packing_type_list as $item) {
                    $message .= $item->caption . "<br/>";
                    $message .= print_r($packing_type_ids);
                }
                return [
                    "result" => false,
                    "error" => $message
                ];
            }


        }
        return [
            "result" => true,
            "error" => ""
        ];
    }

    public static function CheckDegree(ProductRequestForm $product_request_form, $packing_form_items)
    {
        $message = "";
        foreach ($packing_form_items as $packing_item) {
            // بررسی انطباق درجه درخواست شده کالا از انبار با درجه تحویلی کالا در رسته کالایی
            if ($packing_item->product->goods_kind->checking_compatibility_grade_in_delivery) {

                $product_request_form_item_degree_list = $product_request_form->product_request_form_packing_types()->where("product_id", $packing_item->product_id)->pluck("degree_id")->toArray();
                if (!in_array($packing_item->degree_id, $product_request_form_item_degree_list)) {
                    $message .= "کالای " . $packing_item->product->code . " با درجه (" . $packing_item->degree->caption . ") " .
                        "در درخواست " . $product_request_form->code . " وجود ندارد،
                        <br/> لطفا بسته بندی انتخاب نمایید که از نظر درجه بندی با ردیف های درخواست انطباق داشته باشد.";
                }

            }

        }
        return [
            "result" => $message == "",
            "error" => $message
        ];

    }

    public static function CheckTaransportPackingForm($packing_form_ids, $transport_item)
    {
        $message = "";
        $transport_packing_form = TransportPackingForm::whereIn("packing_form_id", $packing_form_ids)->first();
        if ($transport_packing_form) {
            $packing_form = $transport_packing_form->packing_form;
            if ($transport_item && $transport_packing_form->transport_item_id == $transport_item->id) {
                $message = " بسته بندی " . $packing_form->getCode() . " قبلا در همین  " . " - بسته بندی حمل و نقل" . " خوانده شده است.";

            } else {

                $message = " بسته بندی " . $packing_form->getCode() . "قبلا در درخواست " . ($transport_packing_form->product_request_form->code ?? "--") . " - بسته بندی حمل و نقل" . $transport_packing_form->transport_item->code() . " استفاده شده است.";
            }
        }
        return [
            "result" => $message == "",
            "error" => $message
        ];
    }

    public static function CheckMaxOfProduct($product_request_form, $product_ids, $selected_packing_ids, $product_final_amount, $allowed_percentage_to_be_higher,$product_request_form_ids)
    {
        $product_request_form_product_ids = ProductRequestFormItem::whereIn("product_request_form_id", $product_request_form_ids)->pluck("product_id")->toArray();
        $message = "";

        foreach ($product_ids as $product_id) {
            if (!in_array($product_id, $product_request_form_product_ids)) {
                $message = "همه کالاهای موجود در بسته بندی جزء کالاهای درخواست  " . $product_request_form->code . " نمی باشد.";
            } else {
                // مقدار باقی مانده * (1+x/100) => جمع مقدار در حال تحویل + مقدار بسته انتخاب شده

                // جمع مقدار در حال تحویل
                $in_delivery_sum = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->where("product_id", $product_id)->sum("final_amount");

                // حداکثر مقداری که می تواند تحویل دهد: مقدار باقی مانده کل درخواست + مقدار درخواست * x درصد تقسیم بر 100
                $max = 0;
                $amount_remaining = 0;
                $amount_request = 0;
                $sum_form_current_delivery = 0;
                $product_request_form_items = ProductRequestFormItem::whereIn("product_request_form_id",$product_request_form_ids)->where("product_id", $product_id)->get();
                $product_request_form_item_for_special_license = null;
                foreach ($product_request_form_items as $product_request_form_item) {
                    $sum_form_in_sending = \App\Http\Controllers\Warehouse\Out\DashboardController::getCurrentDelivery($product_request_form_item);

                    $sum_form_current_delivery += $sum_form_in_sending["sum_amount"];
                    $amount_request += $product_request_form_item->amount_request;
                    $amount_remaining += $product_request_form_item->amount_remaining;
                    $product_request_form_item_for_special_license = $product_request_form_item;
                }

                //درصد مجاز بیشتر بودن مقدار تحویلی در زمان تحویل کالا از طرف انبار
                //  $max_percent = $item->product->goods_kind->allowed_percentage_to_be_higher;
                $max_percent = $allowed_percentage_to_be_higher[$product_id];

                // ممکن است یک بسته بندی از دو آیتم با کالاهای مثل هم داشته باشد،
                // وقتی می خواهیم مقدار بسته بندی جهت اضافه شدن را بررسی کنیم،
                // باید مقدار کل آن کالا را در نظر بگیرم به مقدار هر آیتم را
//                $sum_final_amount_in_packing = $packing_form->items()->where("product_id", $item->product_id)->sum("final_amount");
                $sum_final_amount_in_packing = isset($product_final_amount[$product_id]) ? $product_final_amount[$product_id] : 0;

                // مقدار در حال تحویل: مقدار بسته بندی که کد آن وارد شده + مقداری که قبلا از این کالا تحویل شده + مقداری که در فرم های بسته بندی حمل و نقل انتخاب شده اند.
                if ($sum_final_amount_in_packing + $sum_form_current_delivery + $in_delivery_sum > $amount_remaining + $amount_request * $max_percent / 100) {

                    // بررسی اینکه مجوز خروج دارد یا خیر
                    $special_license = DeliveryController::HasSpecialLicenseMax($product_request_form, $product_id, $sum_final_amount_in_packing + $in_delivery_sum);
                    if (!$special_license) {
                        $in_delivery_packing = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->where("product_id", $product_id)->groupBy("packing_form_id")->get();
                        $curretn_amount = round(($amount_remaining + $amount_request * $max_percent / 100) - ($sum_form_current_delivery + $in_delivery_sum), 2);
                        $message .= "مقدار کالای موجود در بسته بندی از مقدار درخواست بیشتر است." . "<br/>" .
                            "حداکثر مقدار قابل تحویل:" . round($amount_remaining + $amount_request * $max_percent / 100, 2) . "<br/>" .
                            "حداکثر مقدار قابل تحویل فعلی:" . ($curretn_amount > 0 ? $curretn_amount : 0) . "<br/>" .
                            "مقدار در حال تحویل:" . ($sum_final_amount_in_packing + $in_delivery_sum) . "<br/> لیست بسته بندی ها" . "<br/>" .
                            SpecialLicense::GetLink(1, $product_request_form->id, "ثبت درخواست مجوز ", $product_id, $product_request_form_item_for_special_license->amount_request, -1);
                        foreach ($in_delivery_packing as $p_item) {
                            $transport_item_p_item = TransportPackingForm::where("packing_form_id", $p_item->packing_form->id)->first();
                            $message .= $p_item->packing_form->code;
                            if ($transport_item_p_item) {
                                $message .= " (" . $transport_item_p_item->transport_item->code . ")";
                            }
                            $message .= "<br/>";
                        }
                    }
                }
            }
        }
        return [
            "result" => $message == "",
            "error" => $message
        ];
    }

    public static function AddPackingFormToTransportItem($packing_form, ProductRequestForm $product_request_form, $user_id, $transport_item, $dashboard_type)
    {
        $message = "";

        if ($packing_form && $packing_form->warehouse_status_id != 4201 || $packing_form->status_id != 7007003) {
            $message = "وضعیت بسته بندی  " . $packing_form->getCode() . " (" . $packing_form->status->caption . ") " . "نامعتبر است." . "<br/>";
        }
        $product_request_form_ids = [$product_request_form->id];
        // بررسی اینکه نوع بسته بندی، بسته ها در درخواست مجاز است یا خیر
        if ($dashboard_type == "customer") {
            $product_request_form_ids = ProductRequestFormItem::
            join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
            where("applicant_type_id", $product_request_form->applicant_type_id)->
            where("applicant_id", $product_request_form->applicant_id)->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            pluck("product_request_form_id")->toArray();
            $product_request_form_ids []= $product_request_form->id;
            $product_request_form_ids =array_unique($product_request_form_ids);
        }

        // ثبت بسته های انتخاب شده در سشن
        $session_data = ProductRequestFormSessionData::
        getData($product_request_form,false, $dashboard_type == "customer");

        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }


        // بررسی اینکه در بسته بندی حمل و نقل دیگری نباشد
        $result_check_transport_packing_form = self::CheckTaransportPackingForm([$packing_form->id ?? 0], $transport_item);
        if (!$result_check_transport_packing_form["result"]) {
            $message .= $result_check_transport_packing_form["error"];
        }
        // بررسی نوع بسته بندی
        $product_ids = $packing_form->items()->pluck("product_id")->toArray();
        $product_ids[] = -1;
        $result_check_packing_type_ids = self::CheckPackingTypeIds($product_request_form, $product_ids, [$packing_form->packing_type_id], $product_request_form_ids);
        if (!$result_check_packing_type_ids["result"] && $message == "") {
            $message .= $result_check_packing_type_ids["error"];
        }
        // بررسی انطباق درجه درخواست کالا با درجه تحویلی
        // هر عدل را می توان فقط به یک درخواست تحویل داد
        $result_check_degree = self::CheckDegree($product_request_form, $packing_form->items);
        if (!$result_check_degree["result"]) {
            $message .= $result_check_degree["error"] . "<br/>";
        }

        if ($packing_form && $message == "") {
            $allowed_percentage_to_be_higher = [];
            foreach ($packing_form->items as $item) {
                if (!isset($allowed_percentage_to_be_higher[$item->product_id])) {
                    $allowed_percentage_to_be_higher[$item->product_id] = $item->product->goods_kind->allowed_percentage_to_be_higher;
                }
            }
            $product_final_amount =
                PackingFormItem::
                where("packing_form_id", $packing_form->id)->
                groupBy("product_id")->
                selectRaw("sum(final_amount) as final_amount, product_id")->
                pluck("final_amount", "product_id")->
                toArray();


            $result_check_max_product = self::CheckMaxOfProduct(
                $product_request_form,
                $packing_form->items()->pluck("product_id")->toArray(),
                $selected_packing_ids,
                $product_final_amount,
                $allowed_percentage_to_be_higher,
                $product_request_form_ids
            );
            if (!$result_check_max_product["result"]) {
                $message .= $result_check_max_product["error"] . "<br/>";
            }
        }


        //$message = "درست کار می کند.";
        if ($message == "") {
            TransportPackingForm::create([
                "product_request_form_id" => $product_request_form->id,
                "packing_form_id" => $packing_form->id,
                "transport_item_id" => $transport_item->id
            ]);

            // ثبت بسته های انتخاب شده در سشن
            //برای اطمینان دوباره مقدار ها را می گیریم.
            $session_data = ProductRequestFormSessionData::
            getData($product_request_form);

            $selected_packing_ids = $session_data["selected_packing_ids"];
            if (!$selected_packing_ids) {
                $selected_packing_ids[] = -1;
            }
            $selected_packing_ids[] = $packing_form->id;
            $session_data["selected_packing_ids"] = $selected_packing_ids;

            ProductRequestFormSessionData::
            setData($product_request_form, $session_data);
        }

        return [
            "result" => $message == "",
            "error" => $message,
        ];
    }

    public static function AddPalletToTransportItem(Pallet $pallet, ProductRequestForm $product_request_form, $user_id, $transport_item, $dashboard_type)
    {
        $message = "";

        $packing_form_ids = $pallet->items()->pluck("packing_form_id")->toArray();

        $packing_form_ids_valid = PackingForm::where([
            "warehouse_status_id" => 4201,
            "status_id" => 7007003,
        ])->
        whereIn("id", $packing_form_ids)->
        pluck("id")->toArray();
        $success_message = null;

        if (count($packing_form_ids) != count($packing_form_ids_valid)) {

            $success_message .= "تعداد " . count($packing_form_ids_valid) . " عدد بسته بندی (های) داخل پالت   به درخواست اضافه شدند. ";

        }
        if (count($packing_form_ids_valid) <= 0) {
            $message .= "هیچ بسته بندی داخل پالت نمی باشد.";
            return [
                "result" => $message == "",
                "error" => $message,
            ];
        }

        $product_request_form_ids = [$product_request_form->id];
        // بررسی اینکه نوع بسته بندی، بسته ها در درخواست مجاز است یا خیر
        if ($dashboard_type == "customer") {
            $product_request_form_ids = ProductRequestFormItem::
            join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
            where("applicant_type_id", $product_request_form->applicant_type_id)->
            where("applicant_id", $product_request_form->applicant_id)->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            pluck("product_request_form_id")->toArray();
            $product_request_form_ids []= $product_request_form->id;
            $product_request_form_ids =array_unique($product_request_form_ids);
        }

        // ثبت بسته های انتخاب شده در سشن
        $session_data = ProductRequestFormSessionData::
        getData($product_request_form, false, $dashboard_type == "customer");

        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }


        // بررسی اینکه در بسته بندی حمل و نقل دیگری نباشد
        $result_check_transport_packing_form = self::CheckTaransportPackingForm($packing_form_ids_valid, $transport_item);

        if (!$result_check_transport_packing_form["result"]) {
            $message .= $result_check_transport_packing_form["error"] . "<br/>";
        }
        $packing_form_item_list = PackingFormItem::whereIn("packing_form_id", $packing_form_ids_valid)->get();
        // بررسی نوع بسته بندی
        $product_ids = [];
        foreach ($packing_form_item_list as $packing_form_item) {
            $product_ids[$packing_form_item->product_id] = $packing_form_item->product_id;

        }
        $packing_type_ids = PackingForm::whereIn("id", $packing_form_ids_valid)->pluck("packing_type_id")->toArray();

        $result_check_packing_type_ids = self::CheckPackingTypeIds($product_request_form, $product_ids, $packing_type_ids, $product_request_form_ids);
        if (!$result_check_packing_type_ids["result"]) {
            $message .= $result_check_packing_type_ids["error"] . "<br/>";
        }


        // بررسی انطباق درجه درخواست کالا با درجه تحویلی
        // هر عدل را می توان فقط به یک درخواست تحویل داد

        $result_check_degree = self::CheckDegree($product_request_form, $packing_form_item_list);
        if (!$result_check_degree["result"]) {
            $message .= $result_check_degree["error"] . "<br/>";
        }

        if ($message == "") {

            $allowed_percentage_to_be_higher = [];
            foreach ($packing_form_item_list as $item) {
                if (!isset($allowed_percentage_to_be_higher[$item->product_id])) {
                    $allowed_percentage_to_be_higher[$item->product_id] = $item->product->goods_kind->allowed_percentage_to_be_higher;
                }
            }

            $product_final_amount =
                PackingFormItem::
                whereIn("packing_form_id", $packing_form_ids_valid)->
                groupBy("product_id")->
                selectRaw("sum(final_amount) as final_amount, product_id")->
                pluck("final_amount", "product_id")->
                toArray();


            $result_check_max_product = self::CheckMaxOfProduct(
                $product_request_form,
                $product_ids,
                $selected_packing_ids,
                $product_final_amount,
                $allowed_percentage_to_be_higher,
                $product_request_form_ids
            );
            if (!$result_check_max_product["result"]) {
                $message .= $result_check_max_product["error"] . "<br/>";
            }
        }


        //$message = "درست کار می کند.";
        if ($message == "") {

            // ثبت بسته های انتخاب شده در سشن
            //برای اطمینان دوباره مقدار ها را می گیریم.
            $session_data = ProductRequestFormSessionData::
            getData($product_request_form);

            $selected_packing_ids = $session_data["selected_packing_ids"];
            if (!$selected_packing_ids) {
                $selected_packing_ids[] = -1;
            }

            foreach ($packing_form_ids_valid as $packing_form_id) {
                $list[] = [
                    "product_request_form_id" => $product_request_form->id,
                    "packing_form_id" => $packing_form_id,
                    "transport_item_id" => $transport_item->id
                ];
                $selected_packing_ids[] = $packing_form_id;
            }
            TransportPackingForm::insert($list);


            $session_data["selected_packing_ids"] = $selected_packing_ids;

            ProductRequestFormSessionData::
            setData($product_request_form, $session_data);
        }

        return [
            "result" => $message == "",
            "error" => $message,
            "success_message" => $message == "" ? $success_message : null,
        ];
    }

    public
    function delete_packing_form(
        TransportItem $transport_item, TransportPackingForm $transport_packing_form
    )
    {

        if ($transport_packing_form->transport_item_id == $transport_item->id) {
            $transport_packing_form->delete();


// ثبت بسته های انتخاب شده در سشن
            $session_data = ProductRequestFormSessionData::
            getData($transport_item->product_request_form);

            $selected_packing_ids = $session_data["selected_packing_ids"];
            if (!$selected_packing_ids) {
                $selected_packing_ids[] = -1;
            }
            $new_selected_packing_ids = [];
            foreach ($selected_packing_ids as $item) {
                if ($item != $transport_packing_form->packing_form_id) {
                    $new_selected_packing_ids[] = $item;
                }
            }
            $session_data["selected_packing_ids"] = $new_selected_packing_ids;
            ProductRequestFormSessionData::
            setData($transport_item->product_request_form, $session_data);

            return back()->with(["success" => "حذف با موفقیت انجام شد."]);
        }

        return back()->withErrors("حذف انجام نشد.");
    }

    public
    function transport_confirm(
        TransportItem $transport_item, $type,$page=1,$dashboard_type=""
    )
    {

        if ($transport_item->transport_packing_list()->count() == 0) {
            return back()->withErrors("حداقل یک بسته بندی را انتخاب کنید و سپس ثبت موقت کنید.");
        }

        $transport_item->status_id = 6010001;
        $transport_item->save();

        if ($type == "confirm_print_new" || $type == "confirm_print_back") {
            //print
            $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
            $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);
            $result = \App\Http\Controllers\Utility\Transport\DashboardController::create_pdf_file($transport_item, "print", $transport_item_label_type_id);
            Pdf::labelPrinter($result["html"],
                $transport_item_label_type->orientation,
                $transport_item->id, [$transport_item_label_type->width, $transport_item_label_type->long],
                $result["print_file"]
            );

        }
        if ($type == "confirm_print_new" || $type == "confirm_new") {
            return $this->create_transport_item($transport_item->product_request_form,$page,$dashboard_type);
        }

        return redirect()->route($this->route_path . "index", [$transport_item->product_request_form,$page,$dashboard_type])->with(["success" => "ثبت موقت با موفقیت انجام شد."]);
    }

    public
    function transport_final_confirm(
        TransportItem $transport_item,$page=1,$dashboard_type=""
    )
    {
        if (in_array($transport_item->product_request_form->status_id, self::$not_alloewed_status_id)) {
            $message = "با توجه به وضعیت درخواست (" . ($product_request_form->status->caption ?? "") . ") امکان ثبت نهایی وجود ندارد.";

            return back()->withErrors($message);
        }

        $result = $transport_item->confirm();
        if ($result["result"]) {
            return redirect()->route($this->route_path . "index", [$transport_item->product_request_form,$page,$dashboard_type])->with(["success" => "ثبت نهایی با موفقیت انجام شد."]);

        } else {
            return back()->withErrors($result["message"]);
        }
    }

    public
    function transport_item_delete(
        TransportItem $transport_item
    )
    {

        if ($transport_item->status_id == 6010002) {
            return back()->withErrors("با توجه به اینکه بسته بندی ارسال بار تایید نهایی شده است، امکان حذف وجود ندارد.");
        }

        TransportPackingForm::where("transport_item_id", $transport_item->id)->delete();
        $transport_item->delete();

        return back()->with(["success" => "حذف با موفقیت انجام شد."]);

    }

    public
    function DCLP_QR(
        TransportItem $transport_item, $key
    )
    {

        if ($transport_item->random != $key) {
            return back()->withErrors("آدرس نامعتبر است.");
        }

        return $transport_item;
    }

    public
    function download(
        TransportItem $transport_item
    )
    {
        $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
        $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);
        $result = \App\Http\Controllers\Utility\Transport\DashboardController::create_pdf_file($transport_item, "download", $transport_item_label_type_id);
        Pdf::labelPrinter($result["html"],
            "P",
            $transport_item->id, [115, 165],
        );
    }

    public
    function print(
        TransportItem $transport_item, $i, $j
    )
    {

        $transport_item_label_type_id = Setting::getIntegerValue("transport_item_label_type");
        $transport_item_label_type = PackingTypeLabelPrintingType::find($transport_item_label_type_id);

        $result = \App\Http\Controllers\Utility\Transport\DashboardController::create_pdf_file($transport_item, "print", $transport_item_label_type_id);
        Pdf::labelPrinter($result["html"],
            $transport_item_label_type->orientation,
            $transport_item->id, [$transport_item_label_type->width, $transport_item_label_type->long],
            $result["print_file"]
        );
        $worker = Auth::user();
        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);

    }

    public function download_transport(ProductRequestForm $product_request_form)
    {
        $header_text = Setting::getStringValue("transport_loading_header_text");

        if (count($product_request_form->transport_items) == 0) {
            return back()->withErrors("بسته بندی باری برای درخواست ثبت نشده است.");
        }
        $view_path = "warehouse.out.transport.print.print_transport.";
        $html = [];
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_transport", compact("product_request_form", "header_text"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();
        Pdf::createAsHtml($html,
            "P",
            $product_request_form->getCode(),
            "A4",
            " "
        );
    }

    public function download_report1(ProductRequestForm $product_request_form)
    {
        // گزارش براساس کد کالا
        $header_text = Setting::getStringValue("transport_loading_header_text");


        $list = PackingFormItem::
        join("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where("product_request_form_id", $product_request_form->id)->
        groupBy("product_id")->
        addSelect(DB::raw("packing_form_item.id as id,sum(final_amount) as amount,sum(sub_amount) as sub_amount, product_id,count( DISTINCT  packing_form_item.packing_form_id) as packing_form_count"))->get();

        if (count($list) == 0) {
            return back()->withErrors("هیچ بسته بندی برای درخواست ثبت نشده است.");
        }
        $view_path = "warehouse.out.transport.print.print_report1.";
        $html = [];
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_transport", compact("product_request_form", "list", "header_text"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();
        Pdf::createAsHtml($html,
            "P",
            $product_request_form->getCode(),
            "A4",
            " "
        );
    }

    public static function create_pdf_file(TransportItem $transport_item, $type)
    {
        1 / 0;
//        $static_ip = Setting::getStringValue("static_ip");
//        $local_ip = url("");
//        $url = route("DCLP_QR", [$transport_item, $transport_item->getRandom()]);
//        $worker = Auth::user();
//
//        $header_text = Setting::getStringValue("transport_loading_header_text");
//        $date_time = jdate(Carbon::parse($transport_item->created_at)->timestamp)->format('H:i Y/m/d ');
//        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
//        if ($static_ip != "") {
//            $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
//        }
//        $qr = QrCode::size(100)->generate($url);
//        $barcode = DNS1D::getBarcodeSVG($transport_item->codeNumber(), 'C39', 1.6, 50);
//
//        $view_path = "utility.transport.print.transport_item.";
//        $html[0] = view($view_path . "_head")->render();
//        $html[0] .= view($view_path . "_print_info", compact("transport_item", "qr", "header_text", "date_time", "barcode"))->render() . $html[0];
//        $html[0] .= view($view_path . "_footer")->render();
//
//        $print_file = null;
//        if ($type != "download") {
//            $print_file = PrinterFile::create([
//                "user_id" => $worker->id,
//                "filename" => $transport_item->code() . ".pdf",
//                "status_id" => 305001, // در انتظار دانلود
//                "is_landscape" => 0,
//                "printer_id" => $worker->default_label_printer_id
//            ]);
//        }
//
//        return ["html" => $html, "print_file" => $print_file];
    }


}

