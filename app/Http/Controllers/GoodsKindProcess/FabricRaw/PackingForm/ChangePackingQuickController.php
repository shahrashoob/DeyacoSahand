<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Http\Controllers\Warehouse\Out\DeliveryController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Post\Post;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ChangePackingQuickController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.packing_form.change_packing_quick.",
        "enable_status" => ["003"],
        "button" => ["caption" => "تغییر بسته بندی (سریع)", "class" => "btn-glow-success"],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.change_packing_quick.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";
    var $session_name = "packing_form_amount_";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
    }

    public function index(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ همبافتی یافت نشد.");
        }


        if (!$packing_form->warehouse_id) {
            return back()->withErrors("با توجه به اینکه انبار بسته بندی مشخص نشده است، امکان تغییر بسته بندی وجود ندارد.");
        }

        if ($packing_form->warehouse_status_id != 4201) {
            return back()->withErrors("با توجه به اینکه وضعیت رزرو بسته بندی <b>" . $packing_form->warehouse_status->caption . " </b> می باشد، امکان تغییر بسته بندی وجود ندارد. ");
        }

        $user_id = Auth::id();
        // حذف اطلاعات بسته بندی ها
        ProductRequestFormSessionData::removeDataByOtherId($user_id, $packing_form->id, 390);

        $packing_type_option = Option::get("packing_type", 0, $packing_form->items()->first()->product->goods_kind_id);

        return view($this->view_path . "index", compact("packing_form", "packing_type_option"));

    }


    public function submit(PackingForm $packing_form, Request $request)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $product_request_form = Product\ProductRequest\ProductRequestForm::where("code", "DCRP/" . $request->product_request_form_code)->first();

        if (!$product_request_form) {
            return back()->withErrors("شماره فرم درخواست کالا از انبار معتبر نمی باشد، لطفا یک کد معتبر وارد نمایید.");
        }

        if (!$product_request_form->order_id) {
            return back()->withErrors("لطفا شماره درخواستی را وارد نمایید که شماره مرجع آن از نوع شماره سفارش باشد.");
        }

        $result_cols = DB::table('packing_form_item')->
        where("packing_form_id", $packing_form->id)->
        selectRaw('
                    (COUNT(DISTINCT degree_id) = 1) as degree_id,
                    (COUNT(DISTINCT product_id) = 1) as product_id,
                    (COUNT(DISTINCT lot_number_id) = 1) as lot_number_id
                ')
            ->first();

        if ($result_cols->product_id == 0) {
            return back()->withErrors("با توجه به اینکه نوع کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }
        if ($result_cols->degree_id == 0) {
            return back()->withErrors("با توجه به اینکه درجه کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }
        if ($result_cols->lot_number_id == 0) {
            return back()->withErrors("با توجه به اینکه درجه کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }


        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $packing_form->id, 390, ["packing_type_id" => 0, "packing_form_ids" => "[" . $packing_form->id . "]"]);

        $data["packing_type_id"] = $request->packing_type_id;
        $data["product_request_form_code"] = $request->product_request_form_code;
        ProductRequestFormSessionData::setDataByOtherId($user_id, $packing_form->id, 390, $data);
        return redirect()->route($this->route_path . "select_packing_forms", $packing_form);
    }


    public function select_packing_forms(PackingForm $packing_form)
    {

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $packing_form->id, 390);

        $packing_form_read_ids = json_decode($data["packing_form_ids"]);

        $allow_entry_with_pin = $packing_form->warehouse->allow_entry_with_pin;

        $packing_list_json_data = PackingForm::
        where([
            "status_id" => 7007003,
            "warehouse_status_id" => 4201
        ])->
        selectRaw("packing_forms.id,packing_forms.code,packing_type_id,pin1")->
        get()->keyBy(
            $allow_entry_with_pin ? "pin1" : "code"
        );

        return view($this->view_path . "select_packing_forms", compact("packing_form", "packing_list_json_data", "allow_entry_with_pin", "packing_form_read_ids", "user_id"));
    }

    public function add_packing_form_to_change_packing_quick_list(Request $request)
    {

        $packing_form_ids = json_encode($request->packing_form_read_ids, true);
        $data = ProductRequestFormSessionData::getDataByOtherId($request->user_id, $request->packing_form_id, 390);
        $data["packing_form_ids"] = $packing_form_ids;
        ProductRequestFormSessionData::setDataByOtherId($request->user_id, $request->packing_form_id, 390, $data);
        return $packing_form_ids;
    }

    public function show_list(PackingForm $packing_form)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $packing_form->id, 390);

        $product_request_form = Product\ProductRequest\ProductRequestForm::where("code", "DCRP/" . $data["product_request_form_code"])->first();
        $customer_code = $packing_form->items()->first()->getCustomerCodeFromTariff("default", $product_request_form->order_id);


        $packing_form_read_ids = json_decode($data["packing_form_ids"]);

        $packing_forms = PackingForm::whereIn("id", $packing_form_read_ids)->get();

        $new_packing_type = PackingType::find($data["packing_type_id"]);
        return view($this->view_path . "show_list", compact("packing_form", "packing_forms", "new_packing_type", "product_request_form", "packing_form_read_ids", "customer_code"));

    }

    public function confirm(PackingForm $packing_form, Request $request)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $packing_form->id, 390);

        $packing_form_read_ids = json_decode($data["packing_form_ids"]);

        $packing_forms = PackingForm::whereIn("id", $packing_form_read_ids)->get();
        $new_packing_type = PackingType::find($data["packing_type_id"]);

        $result_cols = DB::table('packing_form_item')->
        whereIn("packing_form_id", $packing_form_read_ids)->
        selectRaw('
                    (COUNT(DISTINCT degree_id) = 1) as degree_id,
                    (COUNT(DISTINCT product_id) = 1) as product_id,
                    (COUNT(DISTINCT lot_number_id) = 1) as lot_number_id
                ')
            ->first();

        if ($result_cols->product_id == 0) {
            return back()->withErrors("با توجه به اینکه نوع کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }
        if ($result_cols->degree_id == 0) {
            return back()->withErrors("با توجه به اینکه درجه کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }
        if ($result_cols->lot_number_id == 0) {
            return back()->withErrors("با توجه به اینکه درجه کالاهای موجود در بسته بندی متفاوت است، برای جلوگیری از تبعات پیش بینی نشده، امکان تغییر بسته بندی وجود ندارد.");
        }

        if ($request->allow_add_to_request) {
            //
            $product_request_form = Product\ProductRequest\ProductRequestForm::where("code", "DCRP/" . $data["product_request_form_code"])->first();

            $delivery_controller = new DeliveryController();
            $delivery_packing_form_list = DeliveryController::getPackingInWarehouse($product_request_form, null, null, "only_packing_form_ids",
                [ // برای اینکه این بسته بندی را هم مجاز کند.
                    "product_id" => $result_cols->product_id,
                    "packing_type_id" => $new_packing_type->id,
                    "degree_id" => -1
                ])->toArray();
            foreach ($packing_forms as $packing_form_delivery) {
                if (!in_array($packing_form_delivery->id, $delivery_packing_form_list)) {
//                    return back()->withErrors("با توجه به اینکه بسته بندی " . $packing_form_delivery->code . " جزء بسته بندی های مجاز کالا در درخواست " .
//                        $product_request_form->code . " نمی باشد، نمی توان لیست بسته بندی ها را به سفارش اضافه کرد.");
                }
            }

        }


        foreach ($packing_forms as $packing_form_item) {
            $before_packing_type = $packing_form_item->packing_type;
            $packing_form_item->packing_type_id = $new_packing_type->id;
            $packing_form_item->save();
            event(new PackingLogEvent($packing_form_item, 7007038, null, $before_packing_type->code . "=>" . $new_packing_type->code));
            event(new PackingLogEvent($packing_form_item, 7007039, null, "DCRP/" . $data["product_request_form_code"]));
        }

        $packing_forms = PackingForm::whereIn("id", $packing_form_read_ids)->get();

        if ($request->allow_print) {

            if (count($packing_forms) > 10) {
                $packing_form_ids = [];
                foreach ($packing_forms as $packing_form_print) {
                    $packing_form_ids[] = $packing_form_print->id;
                }

                $data_print["packing_form_ids"] = $packing_form_ids;
                $data_print["form_id"] = 0;
                $data_print["worker_id"] = $user_id;
                QueueOfLargeOperation::AddToQueue($data_print, 300);


            } else {
                $worker = Worker::find($user_id);
                foreach ($packing_forms as $packing_form_print) {
                    PrintQRController::direct_print($packing_form_print, $worker);
                }

            }
        }

        if ($request->allow_add_to_request) {

            $delivery_controller->add_or_remove_package($product_request_form, null, $packing_form_read_ids, 0, false, true);

        }

        // حذف اطلاعات بسته بندی ها
        ProductRequestFormSessionData::removeDataByOtherId($user_id, $packing_form->id, 390);

        self::SendSms(count($packing_forms), $before_packing_type, $new_packing_type);

        return redirect()->route($this->dashboard_route . "index")->with(["success" => "تغییر نوع بسته بندی ها با موفقیت انجام شد و لیبل های جدید جهت چاپ به لیبل پرینتر پیض فرض ارسال گردید."]);

    }

    public static function SendSms($count, $before_packing_type, $new_packing_type)
    {
        $send_sms_in_quick_change_packing_post_id = Setting::getIntegerValue("send_sms_in_quick_change_packing_post_id");

        //ارسال پیامک برای ناظر انبار
        $supervisor = Post::where("id", $send_sms_in_quick_change_packing_post_id + 0)->
        whereNotIn("id", Post::InvalidPost())->
        get();

        foreach ($supervisor as $item) {

            foreach ($item->worker as $super_worker) {

                Notification::send(
                    "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                    new SMSNotification("changepackingquick", $count, $before_packing_type->caption ?? "", null, $new_packing_type->caption));

            }

        }
    }

    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }


}
