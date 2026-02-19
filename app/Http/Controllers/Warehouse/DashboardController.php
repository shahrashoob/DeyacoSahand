<?php

namespace App\Http\Controllers\Warehouse;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Order\OrderPackingForm;
use App\Models\Post\Post;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Transport\Transport;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psy\Util\Json;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    var $view_path = "warehouse.dashboard.";
    var $route_path = "wh.dashboard.";

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $warehouse_id = $request->warehouse_id;
            $order_by = $request->order_by;
        } else {
            $search = session("search_warehouse_input");
            $status_id = session("warehouse_input_status_id");
            $warehouse_id = session("warehouse_input_warehouse_id");
            $order_by = session("warehouse_input_order_by") ?? "forms.created_at__desc";

        }
        session([
            "search_warehouse_input" => $search,
            "warehouse_input_status_id" => $status_id,
            "warehouse_input_warehouse_id" => $warehouse_id,
            "warehouse_input_order_by" => $order_by,
        ]);

        $allowed_status_ids = Post::GetAllStatusPermission();
        $status_option = Option::get("status_permission", $status_id, 5000, $allowed_status_ids);


        if ($status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $status_id;
        }

        $allowed_warehouse_ids = Warehouse::getAllowedWarehouse();
        $warehouse_option = Option::get("post_warehouse", $warehouse_id, 0, $allowed_warehouse_ids);
        // search
        if ($warehouse_id != 0) {
            $allowed_warehouse_ids = [];
            $allowed_warehouse_ids[] = $warehouse_id;
        }


        $list = Form::
        whereIn("warehouse_id", $allowed_warehouse_ids)->
        whereIn("forms.status_id", $allowed_status_ids)->
        whereIn("form_type_id", [304, 306, 401])->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        when($search != "", function ($query) use ($search) {
            return $query->where("code", "like", "%" . $search . "%");
        })->
        paginate(30);


        $order_by_Option = Option::OrderBy("warehouse_input", $order_by);
        $user_id = Auth::id();

        //چک کردن تغییر کالا در ورود به انبار
        $checking_product_change_in_entry = Setting::getIntegerValue("checking_product_change_in_entry");

        return view($this->view_path . "index", compact("list", "user_id", "status_option", "warehouse_option", "search", "order_by_Option", 'checking_product_change_in_entry'));
    }


    public function show_form(Form $form, $page = 1)
    {

        $result = DashboardController::check_permission($form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

//        if ($form->status_id == 500000100) {
//            return back()->withErrors("به دلیل عدم تایید فرم یا معلق بودن فرم امکان مشاهده جزئیات وجود ندارد.");
//        }

//return $form->status_id;
        if ($form->status_id == 500000430) {

            //return $form->warehouse->check_amount_product_in_entry;

            if ($form->warehouse->check_amount_product_in_entry == 1) {

                return redirect()->route("wh.input.complete_information_with_value.index", $form);
            }
            return redirect()->route("wh.input.complete_information.index", $form);
        }

        $packing_form_item_ids = FormItem::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();

        $packing_form_ids = PackingFormItem::whereIn("id", $packing_form_item_ids)->pluck("packing_form_id")->toArray();


        $packing_form_list = PackingForm::whereIn("id", $packing_form_ids)->orderByDesc("updated_at")->get();
        $packing_form_master_ids = PackingForm::MasterPackingFormIds($packing_form_list);

        $packing_form_list = PackingForm::whereIn("id", $packing_form_master_ids)->orderByDesc("updated_at")->get();

        // چک نمودن کد بسته بندی با توجه به تنظیمات رسته کالایی اولین آیتم فرم
        $form_item = FormItem::where("form_id", $form->id)->first();
        $checking_carrier_at_delivery_of_product = $form_item->product->goods_kind->checking_carrier_at_delivery_of_product ?? 1;

        if (!$form_item) {
            return back()->withErrors("هیچ آیتمی در فرم وجود ندارد.");
        }

        $packing_form_item_id_where_put_in_warehouse = WarehouseProduct::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();
        $packing_form_id_where_put_in_warehouse = PackingFormItem::whereIn("id", $packing_form_item_id_where_put_in_warehouse)->pluck("packing_form_id", "packing_form_id")->toArray();

        return view($this->view_path . "show_form_packing", compact("form", "packing_form_list", "checking_carrier_at_delivery_of_product", "page", "packing_form_id_where_put_in_warehouse"));

    }

    public function confirm_packing_list(Request $request, Form $form, $page = 1)
    {

        // چک کردن دسترسی تایید بسته بندی ها توسط انبار
        $post_user = Auth::user()->posts->first();
        $permission_confirm_packing = $post_user->checkButtonPermission("wh.dashboard.input.confirm_packing");
        if (!$permission_confirm_packing) {
            return back()->withErrors("شما مجوز انجام عملیات را ندارید.");
        }

        if ($form->status_id == 500000200) {
            return back()->withErrors("این فرم قبلا تایید شده است");
        }

        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $form->warehouse_id;

        $product_ids_for_check = $form->item()->pluck("product_id")->toArray();
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $error_message = $result_warehouse["error"];
            return back()->withErrors($error_message);

        }
        // اگر قبلا در انبار ثبت شده است، نباید دوباره ثبت شود.
        $packing_form_item_id_where_put_in_warehouse = WarehouseProduct::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();
        $packing_form_id_where_put_in_warehouse = PackingFormItem::whereIn("id", $packing_form_item_id_where_put_in_warehouse)->pluck("packing_form_id", "packing_form_id")->toArray();
        $packing_form_id_where_put_in_warehouse[] = -1;

        $allow_entry_with_pin = $form->warehouse->allow_entry_with_pin;
//return $request->all();
        $packing_form_item_ids = FormItem::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();

        $packing_form_ids = PackingFormItem::whereIn("id", $packing_form_item_ids)->pluck("packing_form_id")->toArray();

        $packing_form_list = PackingForm::
        whereIn("id", $packing_form_ids)->
        whereNotIn("id", $packing_form_id_where_put_in_warehouse)->
        get();


        $confirm_packing_count = [];
        foreach ($packing_form_list as $packing_form) {

            $conform = false;
            // چک کردن کد حامل
            if (isset($request->data["carrier_code"])) {
                foreach ($request->data["carrier_code"] as $carrier_code) {

                    if ($packing_form->carrier && $packing_form->carrier->code == $carrier_code) {
                        $conform = true;
                        $confirm_packing_count[$packing_form->id] = 1;
                        $this->confirm_packing_form($packing_form, $form);
                    }
                }
            }

            if (!$conform) {

                // چک کردن کد بسته بندی، یا کد خود بسته بندی یا کد بسته بندی مستر
                foreach ($request->data["packing_form_code"] as $packing_form_code) {

                    // بررسی اینکه Pin برای انبار فعال است یا خیر
                    if ($allow_entry_with_pin) { //Pin فعال است
                        if (
                            ($packing_form->pin1 == $packing_form_code) ||
                            ($packing_form->packing_form_master && $packing_form->packing_form_master->pin1 == $packing_form_code)
                        ) {

                            // اگر بسته بندی مستر دارد، آن را باید تایید کنیم.
                            if ($packing_form->packing_form_master) {
                                $confirm_packing_count[$packing_form->packing_form_master_id] = 1;
                                $this->confirm_packing_form($packing_form->packing_form_master, $form);
                            } else {
                                $confirm_packing_count[$packing_form->id] = 1;
                                $this->confirm_packing_form($packing_form, $form);
                            }

                        }
                    } else { // Pin غیر فعال است
                        if (
                            ($packing_form->getCode() == "DCPK/" . $packing_form_code) ||
                            ($packing_form->packing_form_master && $packing_form->packing_form_master->getCode() == "DCPK/" . $packing_form_code)
                        ) {

                            // اگر بسته بندی مستر دارد، آن را باید تایید کنیم.
                            if ($packing_form->packing_form_master) {
                                $confirm_packing_count[$packing_form->packing_form_master_id] = 1;
                                $this->confirm_packing_form($packing_form->packing_form_master, $form);
                            } else {
                                $confirm_packing_count[$packing_form->id] = 1;
                                $this->confirm_packing_form($packing_form, $form);
                            }

                        }
                    }
                }
            }

        }

        if (count($confirm_packing_count) == 0) {

            return redirect()->route($this->route_path . "show_form", [
                $form,
                $page
            ])->withErrors("شماره حامل یا کد  بسته بندی یک یا چند بسته بندی به درستی وارد نشده است");
        }


        $result = $this->change_form_status($form, $confirm_packing_count, $packing_form_list);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $form = Form::find($form->id);
        if ($form->status_id == 500000200) {
            return redirect()->route($this->route_path . "index")->with(["success" => "عملیات با موفقیت انجام شد، " . count($confirm_packing_count) . " بسته بندی ثبت نهایی شد."]);
        } else {
            return back()->withErrors("تا کنون " . count($confirm_packing_count) . " بسته بندی ثبت نهایی شده است و هنوز بعضی از بسته بندی ها ثبت نهایی نشده اند.");

        }
    }

    public function confirm_packing_form(PackingForm $packing_form, Form $form, $user_id = null)
    {

        if ($form->form_type_id == 401) {  // فرم نقل و انتقال و ثبت تراکنش اصلاحی
            return;
        }
        if ($packing_form->packing_form_master) {
            return; // بسته بندی فرعی نمی تواند ورود به انبار داشته باشد.
        }


        $packing_form->items()->update(["status_id" => 7006004]); // // ثبت و تایید
        $packing_form->status_id = 7007003; // تحویل شده به انبار
        $packing_form->form_id = $form->id;
        $packing_form->save();

        event(new PackingLogEvent($packing_form, 7007006, null, "", $form->id, $user_id));

        // تغییر وضعیت حامل
        if ($packing_form->carrier) {
            $packing_form->carrier->SetStatus(5320002, null, 5320108, null, null, null, $user_id, $packing_form->id);
        } // پر موجود در انبار

//        // تغییر وضعیت بسته های فرعی
//        foreach ( $packing_form->sub_packing as $sub_packing ) {
//            $sub_packing->packing_form->items()->update( [ "status_id" => 7006004 ] ); // // ثبت و تایید
//            $sub_packing->packing_form->status_id = 7007003; // خاتمه یافته
//            $sub_packing->packing_form->save();
//
//            event( new PackingLogEvent( $sub_packing->packing_form, 7007006, null, "", $form->id ) );
//
//            // تغییر وضعیت حامل
//            if ( $sub_packing->packing_form->carrier ) {
//                $sub_packing->packing_form->carrier->SetStatus( 5320002, null, 5320108 );
//            } // پر موجود در انبار
//        }
        // اگر بسته بندی دارای بسته بندی های فرعی می باشد، به ازای هر بسته بندی فرعی تراکنش ثبت می گردد.

        event(new PutInWarehouseEvent($form, $packing_form, $packing_form->id));

        // در صورتی که بسته بندی از نوع کالای تامین امانی است،
        // احتمالا یک تخصیص دارد و باید وضعیت بسته بندی در تخصیص ارسال شده شود.
        if (isset($form->allocation) && isset($form->allocation->order_id)) {
            OrderPackingForm::
            where("order_id", $form->allocation->order_id)->
            where("packing_form_id", $packing_form->id)->
            update(["status_id" => 6070002]); // ارسال شده
        }

    }

    public function change_form_status(Form $form, $confirm_packing_counts = [], $packing_form_list = [], $user_id = null)
    {

        switch ($form->form_type_id) {
            case 304:
                $form_item_count = $form->item()->count("packing_form_item_id");

                $warehouse_item_count = WarehouseProduct::where("form_id", $form->id)->count("packing_form_item_id");
                if ($form_item_count == $warehouse_item_count) {


                    // اگر فرم ورود از طرف سامانه ارسال شده باشد، باید برگ خروج آن تایید شود.
                    if (isset($form->applicant_type_id) && isset($form->applicant_id)) {

                        $result = self::ConfirmApplicantForInputForm($form);

                        if (!$result["result"]) {
                            $form->status_id = 500000110;
                            $form->save();
                            event(new FormLogEvent($form, $result["error"]));
                            SMSMessage::ExceptionError("تراکنش های ورود به انبار فرم " . $form->code . " به درستی ثبت نشده است، فرم در انتظار بررسی قرار گرفت");

                            return $result;
                        }
                    }

                    $form->status_id = 500000200;
                    $form->save();
                    event(new FormLogEvent($form, "", $user_id));

                    if (isset($form->allocation->order_id)) {
                        // اگر همه بسته بندی های سفارش را ارسال کرده باشد، تخصیص را خاتمه یافته می کند.
                        Allocation::UpdateAllocationStatus($form->allocation, $form, $user_id);
                    }


                } elseif ($form_item_count < $warehouse_item_count) {
                    $form->status_id = 500000110; // در انتظار بررسی تراکنش های انبار
                    $form->save();
                    event(new FormLogEvent($form, "", $user_id));
                    SMSMessage::ExceptionError("تراکنش های ورود به انبار فرم " . $form->code . " به درستی ثبت نشده است، فرم در انتظار بررسی قرار گرفت");
                }
                break;
            case 306:
                $form_item_count = $form->item()->count("packing_form_item_id");

                $warehouse_item_count = WarehouseProduct::where("form_id", $form->id)->count("packing_form_item_id");
                if ($form_item_count == $warehouse_item_count) {
                    $form->status_id = 500000200;
                    $form->save();
                    event(new FormLogEvent($form, "", $user_id));

                } elseif ($form_item_count < $warehouse_item_count) {
                    $form->status_id = 500000110; // در انتظار بررسی تراکنش های انبار
                    $form->save();
                    event(new FormLogEvent($form, "", $user_id));
                    SMSMessage::ExceptionError("تراکنش های ورود به انبار فرم " . $form->code . " به درستی ثبت نشده است، فرم در انتظار بررسی قرار گرفت");
                }
                // در صورتی که فرم از نوع فرم مرجوعی باشد باید وضعیت درخواست مرجوعی بروز شود.
                $reject_product_form = RejectProductForm::where("input_form_id", $form->id)->first();
                if ($reject_product_form) {
                    $reject_product_form->status_id = 7009006;//در انتظار بررسی کنترل کیفیت
                    $reject_product_form->save();
                    event(new RejectProductLogEvent($reject_product_form, 7009004, "", $user_id)); // ثبت تراکنش انبار
                }
                break;

            case 401:
                // فرم نقل و انتقل از انبار به انبارک با تراکنش اصلاحی
                // بقیه اقدامات در اسکریپت 1012 انحام می شود.
                //new Script1012Controller();
                // چون این نوع فرم تراکنش انبار ندارد و فقط تایید می شود بنابراین لازم نیست که تراکنش های انبار آن چک شوند
                $master_ids = PackingForm::MasterPackingFormIds($packing_form_list);
//                return ( count( $master_ids ))+count($confirm_packing_counts);
                if (count($master_ids) != count($confirm_packing_counts)) {
                    return [
                        "result" => false,
                        "error" => "برای تایید فرم باید تمامی بسته بندی های داخل فرم به صورت صحیح وارد شود."
                    ];
                }
                $form->status_id = 500000200;
                $form->save();
                event(new FormLogEvent($form, "", $user_id));
                break;

        }

        return ["result" => true];

    }

    public function reject_packing_list(Request $request, Form $form)
    {

        // چک کردن دسترسی تایید بسته بندی ها توسط انبار
        $post_user = Auth::user()->posts->first();
        $permission_confirm_packing = $post_user->checkButtonPermission("wh.dashboard.input.confirm_packing");
        if (!$permission_confirm_packing) {
            return back()->withErrors("شما مجوز انجام عملیات را ندارید.");
        }

        if ($form->status_id == 500000200) {
            return back()->withErrors("این فرم قبلا تایید شده است");
        }
        if ($form->status_id == 500000100) {
            return back()->withErrors("این فرم قبلا عدم تایید شده است");
        }
        if ($form->status_id == 500000450) {
            return back()->withErrors("این فرم قبلا تایید شده است");
        }

        $packing_form_item_ids = FormItem::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();

        $packing_form_ids = PackingFormItem::whereIn("id", $packing_form_item_ids)->pluck("packing_form_id")->toArray();

        $packing_form_confirm_count = WarehouseProduct::
        where("form_id", $form->id)->
        count();

        if ($packing_form_confirm_count > 0) {
            return back()->withErrors("با توجه به اینکه تعدادی از بسته بندی ها تایید شده است، امکان عدم تایید فرم وجود ندارد.");
        }


// تغییر وضعیت فرم انبار
        $form->status_id = 500000100;
        $form->save();
        event(new FormLogEvent($form));

        $form->general_items()->update(["status_id" => 5002004]); // عدم تایید

        switch ($form->form_type_id) {
            case 306: // فرم ورود کالای مرجوعی به انبار
            case 304: // فرم تحویل کالا به انبار - فرم ورود پکینگ لیست
                // تغییر وضعیت بسته بندی ها
                $packing_form_item_ids = FormItem::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();

                $packing_form_ids = PackingFormItem::whereIn("id", $packing_form_item_ids)->pluck("packing_form_id")->toArray();

                $packing_form_list = PackingForm::whereIn("id", $packing_form_ids)->get();


                foreach ($packing_form_list as $packing_form) {

                    $packing_form->items()->update(["status_id" => 7007010]);// در انتظار مفایرت گیری
                    $packing_form->status_id = 7007010; // در انتظار مفایرت گیری
                    $packing_form->save();
                    event(new PackingLogEvent($packing_form, 7007007));

                }

        }

        return redirect()->route($this->route_path . "index")->with(["success" => "عملیات با موفقیت انجام شد"]);
    }

    public function submit_new_packing_api(Request $request)
    {
        $error_message = null;
        $success_message = null;
        $entry_with_pin = false;
        $packing_code = $request->packing_code;
        $pallet_code_input = $request->pallet_code;
        $pallet_packing_count = $request->pallet_packing_count;
        $user_id = $request->user_id;
        $packing_message = $request->packing_message ?? "";
        $number_packing_submit = $request->number_packing_submit ?? 0;
        $checking_product_change_in_entry = $request->checking_product_change_in_entry ?? 1;
        $last_product_id_read = $request->last_product_id_read ?? null; //آخرین کد کالایی که خوانده شده

        Auth::loginUsingId($user_id);
        // چک کردن دسترسی تایید بسته بندی ها توسط انبار
        $post_user = Auth::user()->posts->first();
        $permission_confirm_packing = $post_user->checkButtonPermission("wh.dashboard.input.confirm_packing");
        if (!$permission_confirm_packing) {
            $error_message = "شما مجوز انجام این عملیات را ندارید.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }

        $packing_form = PackingForm::where("code", "DCPK/" . $packing_code)->first();
        if (!$packing_form) {
            $packing_form = PackingForm::where("pin1", $packing_code)->first();
            if ($packing_form) {
                // بسته بندی با pin1 خوانده شده و معتبر است.
                $entry_with_pin = true;
            } else {

                // چک کردن پالت
                $packing_code = strtoupper($packing_code);
                $pallet_code = trim($packing_code, "PC");
                if ("PC" . $pallet_code == $packing_code) {
                    $pallet = Pallet::find($pallet_code);
                    $pallet_code = "PC" . $pallet_code;
                    if (!$pallet) {

                        $error_message = " پالت  " . $pallet_code . " در سامانه یافت نشد.";
                        return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

                    }
                    if ($pallet->status_id != 6080002) {
                        $error_message = " با توجه به اینکه وضعیت پالت $pallet_code " . $pallet->status->caption . " می باشد، امکان ورود به انبار امکان پذیر نمی باشد.";
                        return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

                    }

                    if (isset($pallet_packing_count)) { // تعداد بسته بندی را وارد کرده است:

                        // تعداد وارد شده و تعداد بسته بندی در پالت (در انتظار تایید) باید یکی باشد.
                        $get_Number_of_packing_for_sett_o_warehouse = $pallet->get_Number_of_packing_for_sett_o_warehouse();
                        if ($get_Number_of_packing_for_sett_o_warehouse == 0) {
                            $error_message = " هیچ کدام از بسته بندی های  داخل پالت  $pallet_code در انتظار تایید انبار نمی باشد، لطفا از ارسال پالت به انبار اطمینان داشته باشید.";
                            return view($this->view_path . "_confirm_packing", compact("error_message", 'packing_code', "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry', "pallet_code"));

                        } elseif ($get_Number_of_packing_for_sett_o_warehouse != $pallet_packing_count) {
                            $error_message = " تعداد بسته بندی های  (در انتظار تایید انبار) داخل پالت  $pallet_code به درستی وارد نشده است.";
                            return view($this->view_path . "_confirm_packing", compact("error_message", 'packing_code', "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry', "pallet_code"));

                        } else {
                            return $this->set_to_warehouse_pallet($pallet, $user_id, $number_packing_submit, $packing_message, $last_product_id_read, $checking_product_change_in_entry);
                        }

                    } else {
                        $pallet_code = $pallet_code;
                        $warning_message = "لطفا تعداد بسته بندی های  (در انتظار تایید انبار) داخل پالت  $pallet_code را وارد نمایید.";
                        // همه چی اوکی است، باید تعداد بسته بندی در انتظار تایید داخل پالت را بگوید
                        return view($this->view_path . "_confirm_packing", compact("error_message", 'packing_code', "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry', "pallet_code", 'warning_message'));
                    }

                }


                $error_message = "بسته بندی مورد نظر یافت نشد.";

                return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));
            }
        }


        if (!in_array($packing_form->status_id, [
                7007009, // در انتظار تایید دریافت محصول
                7007002, // در انتظار تایید ورود به انبار
                7007003, //تحویل شده با انبار
            ]) || !in_array($packing_form->warehouse_status_id, [4204, 4202, 4207])) {
            $error_message = "وضعیت بسته بندی " . $packing_form->getCode() . "(" . $packing_form->status->caption . ")" . " جهت دریافت بسته بندی معتبر نمی باشد.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }


        $sub_packing_form_ids = $packing_form->packing_form_contents()->pluck("id")->toArray();


        $form_item = FormItem::join("forms", "forms.id", "form_id")->
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        when(count($sub_packing_form_ids) == 0, function ($query) use ($packing_form) {
            return $query->where("packing_form_id", $packing_form->id);
        })->
        when(count($sub_packing_form_ids) > 0, function ($query) use ($sub_packing_form_ids) {
            return $query->whereIn("packing_form_id", $sub_packing_form_ids);
        })->
        whereIn("forms.status_id", [500000410, 500000420])->// در انتظار تایید ورود به انبار
        orderByDesc("forms.id")->
        select("form_id", "form_item.product_id")->
        first();

        if (!$form_item) {
            $error_message = "فرم ورود به انبار مربوط به بسته " . $packing_form->getCode() . " یافت نشد، لطفا با انتخاب فرم ورود به انبار مربوطه در لیست زیر بسته را تحویل نمایید.";

            $form_item_others = FormItem::join("forms", "forms.id", "form_id")->
            join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
            when(count($sub_packing_form_ids) == 0, function ($query) use ($packing_form) {
                return $query->where("packing_form_id", $packing_form->id);
            })->
            when(count($sub_packing_form_ids) > 0, function ($query) use ($sub_packing_form_ids) {
                return $query->whereIn("packing_form_id", $sub_packing_form_ids);
            })->
            whereNotIn("forms.status_id", [500000410, 500000420])->// در انتظار تایید ورود به انبار
            orderByDesc("forms.id")->
            select("form_id", "form_item.product_id")->
            get();

            if (count($form_item_others) > 0) {

                $other_message = "<br/>" . "بسته بندی در فرم های زیر قرار دارد؟";
                foreach ($form_item_others as $form_item_other) {
                    $other_message .= "<br/>" . $form_item_other->form->code . "(" . $form_item_other->form->status->caption . ")";
                }
                $error_message = $error_message . $other_message;
            }

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }

        //چک کردن اینکه کالا در زمان ورود به انبار تغییر کرده است یا خیر
        if ($last_product_id_read && $form_item->product_id != $last_product_id_read && $checking_product_change_in_entry) {
            $new_product = Product::find($form_item->product_id);

            $warning_message = "کالایی که در حال تایید جهت ورود به انبار هستید تغییر کرد. <br/> در صورت تایید بر روی دکمه تایید و ادامه کلیک فرمایید.";
            $warning_message .= "<b><br/> کالای جدید:" . $new_product->fullCaption() . "</b>";
            $warning_message .= "<b><br/> " . $packing_form->code . "</b>";
//            $warning_message .= "<b><br/> " .$last_product_id_read."-".$new_product->id."</b>--".$checking_product_change_in_entry;
            $new_product_read = $new_product->id;


            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "warning_message", "user_id", "number_packing_submit", "packing_message", "last_product_id_read", "new_product_read", "packing_code", 'checking_product_change_in_entry'));

        }

        $form = Form::find($form_item->form_id);

        if ($form->warehouse->allow_entry_with_pin && !$entry_with_pin) {
            $error_message = "ورود به انبار " .
                $form->warehouse->caption .
                "  با خواندن بارکد کد بسته بندی  امکان پذیر نمی باشد، <br/> 
                    لطفا pin  بسته بندی را وارد نمایید.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }
        if (!$form->warehouse->allow_entry_with_pin && $entry_with_pin) {
            $error_message = "ورود به انبار " .
                $form->warehouse->caption .
                "  با خواندن  pin فرم های بسته بندی  امکان پذیر نمی باشد، <br/> 
                    لطفا بارکد کد بسته بندی را وارد نمایید.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message"));

        }

        if ($form->one_time_confirmation) {
            $error_message = "برای تایید بسته بندی " . $packing_form->code . " باید کل فرم ورود به انبار " . $form->code . " را تایید کنید.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }

        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $form->warehouse_id;

        $product_ids_for_check = $packing_form->items()->pluck("product_id")->toArray();
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $error_message = $result_warehouse["error"];
            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }


        $wp = WarehouseProduct::where("form_item_id", $form_item->id)->first();
        if ($wp) {
            $error_message = "برای بسته بندی " . $packing_form->code . " تراکنش انبار ثبت شده است، لطفا با پشتیبانی تماس بگیرید.";

            return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

        }

        $this->confirm_packing_form($packing_form, $form);
        $this->change_form_status($form);

        $success_message = " بسته بندی " . $packing_form->getCode() . " با موفقیت ثبت گردید.";
        $number_packing_submit++;

        $packing_message .= "," . $packing_form->getCode();


        $last_product_id_read = $form_item->product_id;
        return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));


    }

    public static function SubmitNewPackingToWarehouse(PackingForm $packing_form, $user_id, $check_warehouse_handling = true)
    {


        if (!in_array($packing_form->status_id, [
                7007009, // در انتظار تایید دریافت محصول
                7007002, // در انتظار تایید ورود به انبار
                7007003, //تحویل شده با انبار
            ]) || !in_array($packing_form->warehouse_status_id, [4204, 4202, 4207])) {
            $error_message = "وضعیت بسته بندی " . $packing_form->getCode() . "(" . $packing_form->status->caption . ")" . " جهت دریافت بسته بندی معتبر نمی باشد.";

            return [
                "result" => false,
                "error" => $error_message,
            ];

        }


        $sub_packing_form_ids = $packing_form->packing_form_contents()->pluck("id")->toArray();


        $form_item = FormItem::join("forms", "forms.id", "form_id")->
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        when(count($sub_packing_form_ids) == 0, function ($query) use ($packing_form) {
            return $query->where("packing_form_id", $packing_form->id);
        })->
        when(count($sub_packing_form_ids) > 0, function ($query) use ($sub_packing_form_ids) {
            return $query->whereIn("packing_form_id", $sub_packing_form_ids);
        })->
        whereIn("forms.status_id", [500000410, 500000420])->// در انتظار تایید ورود به انبار
        orderByDesc("forms.id")->
        select("form_id")->
        first();

        if (!$form_item) {
            $error_message = "فرم ورود به انبار مربوط به بسته " . $packing_form->getCode() . " یافت نشد، لطفا با انتخاب فرم ورود به انبار مربوطه در لیست زیر بسته را تحویل نمایید.";

            return [
                "result" => false,
                "error" => $error_message,
            ];
        }

        $form = Form::find($form_item->form_id);


        if ($form->one_time_confirmation) {
            $error_message = "برای تایید بسته بندی " . $packing_form->code . " باید کل فرم ورود به انبار " . $form->code . " را تایید کنید.";

            return [
                "result" => false,
                "error" => $error_message,
            ];
        }

        // بررسی اینکه کالا در انبارگردانی نباشد
        if ($check_warehouse_handling) {
            $warehouse_ids = [];
            $warehouse_ids[] = $form->warehouse_id;

            $product_ids_for_check = $packing_form->items()->pluck("product_id")->toArray();
            $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

            if (!$result_warehouse["result"]) {
                $error_message = $result_warehouse["error"];
                return [
                    "result" => false,
                    "error" => $error_message,
                ];
            }
        }

        $wp = WarehouseProduct::where("form_item_id", $form_item->id)->first();
        if ($wp) {
            $error_message = "برای بسته بندی " . $packing_form->code . " تراکنش انبار ثبت شده است، لطفا با پشتیبانی تماس بگیرید.";

            return [
                "result" => false,
                "error" => $error_message,
            ];
        }

        $controller = new self();
        $controller->confirm_packing_form($packing_form, $form, $user_id);
        $controller->change_form_status($form, [], [], $user_id);

        $success_message = " بسته بندی " . $packing_form->getCode() . " با موفقیت ثبت گردید.";


        return [
            "result" => true,
            "success" => $success_message,
        ];
    }

    public static function check_permission(Form $form)
    {
        $allowed_status_ids = Post::GetAllStatusPermission();
        if (!in_array($form->status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما به فرم " . $form->getCode() . " دسترسی ندارید."
            ];
        }
        $allowed_warehouse_ids = Warehouse::getAllowedWarehouse();
        if (!in_array($form->warehouse_id, $allowed_warehouse_ids)) {
            return [
                "result" => false,
                "message" => "شما به انبار " . $form->warehouse->code . " دسترسی ندارید."
            ];
        }

        return [
            "result" => true
        ];
    }

    public function print_packing_form(Form $form, $page = 1)
    {

        $packing_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("form_item", "packing_form_item_id", "packing_form_item.id")->
        where("form_item.form_id", $form->id)->
        select("packing_forms.*")->
        get();

        $packing_form_ids = PackingForm::MasterPackingFormIds($packing_list);
        $packing_list = PackingForm::whereIn("id", $packing_form_ids)->paginate(50);

        $max = count($packing_form_ids);

        return view($this->view_path . "print_packing_form", compact("form", "packing_list", "page", "max"));

    }

    public function submit_print_packing_form(Request $request, Form $form, $page = 1)
    {
        $data = $request->data;
        $worker = Worker::find(Auth::id());
        $packing_forms = [];
        if ($request->from_row) {

            $packing_list = PackingForm::
            join("packing_form_item", "packing_forms.id", "packing_form_id")->
            join("form_item", "packing_form_item_id", "packing_form_item.id")->
            where("form_item.form_id", $form->id)->
            select("packing_forms.*")->
            get();
            $form_row = $request->from_row ?? 1;
            $to_row = $request->to_row ?? 0;

            $packing_form_ids = PackingForm::MasterPackingFormIds($packing_list);
            $packing_forms = PackingForm::whereIn("id", $packing_form_ids)->
            skip($form_row - 1)->take($to_row - $form_row + 1)->
            get();
        }

        if ($data && count($data) > 0) {
            $packing_forms = PackingForm::whereIn("id", array_keys($data["packing_form"]))->get();
        }
        if (count($packing_forms) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }
        if (count($packing_forms) > 50) {
            return back()->withErrors("تعداد چاپ در هر مرحله حداکثر 50 بسته بندی می باشد.");
        }
        if (count($packing_forms) > 20) {
            $packing_form_ids = [];
            foreach ($packing_forms as $packing_form_print) {
                $packing_form_ids[] = $packing_form_print->id;
            }

            $last_queue = QueueOfLargeOperation::where([
                "large_operation_type_id" => 300,
                "status_id" => 3500001,
            ])->first();
            if ($last_queue) {
                return back()->withErrors("با توجه به اینکه یک درخواست چاپ بسته بندی قبلا صادر شده است و هنوز در حال چاپ می باشد، لطفا چند دقیقه دیگر تلاش کنید. ");
            }

            $data["packing_form_ids"] = $packing_form_ids;
            $data["form_id"] = $form->id;
            $data["worker_id"] = $worker->id;
            QueueOfLargeOperation::AddToQueue($data, 300);


        } else {
            foreach ($packing_forms as $packing_form_print) {
                PrintQRController::direct_print($packing_form_print, $worker);
            }

        }

        return redirect()->route($this->route_path . "show_form", [
            $form,
            $page
        ])->with(["success" => "بسته بندی های مورد نظر با موفقیت پرینت شدند"]);
    }

    public function print_entry_form(Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type)
    {

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }


        $printer = $packing_type_label_printing_type->getPrinter($worker);

        $result = $this->create_pdf_file($form, $worker, "print", $packing_type_label_printing_type, $worker->default_print_number);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($packing_type_label_printing_type->size != "") {

            Pdf::createAsHtml($result["html"], $packing_type_label_printing_type->orientation, $form->code, $packing_type_label_printing_type->size, "", $result["print_file"]);
        } else {

            Pdf::labelPrinter($result["html"],
                $packing_type_label_printing_type->orientation,
                $form->code, [
                    $packing_type_label_printing_type->width,
                    $packing_type_label_printing_type->long
                ],
                $result["print_file"]
            );
        }

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر " . $printer->caption . " مراجعه فرمایید."]);
    }

    public function download_entry_form(Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type)
    {

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $result = $this->create_pdf_file($form, $worker, "download", $packing_type_label_printing_type, 1);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $form->code, $packing_type_label_printing_type->size
        );
    }

    public static function create_pdf_file(Form $form, $worker, $type, $packing_type_label_printing_type, $number_of_prints = 1, $print_type = "product")
    {

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCEF_QR", [$form, $form->getRandom()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $form_item = $form->item()->first();
        if (!$form_item) {
            return ["html" => "", "print_file" => null];
        }

        //محاسبه تعداد بسته بندی
        $product_packing_form_count = FormItem::
        join("packing_form_item", "packing_form_item_id", "packing_form_item.id")->
        groupBy("form_item.product_id")->
        groupBy("form_item.degree_id")->
        selectRaw("count(distinct(packing_form_id)) as packing_form_count,form_item.product_id,form_item.degree_id")->
        where("form_id", $form->id)->
        get()->keyBy(function ($item) {
            return $item->product_id . "_" . $item->degree_id;
        });

        $packing_form = $form_item->packing_form_item->packing_form ?? null;


        $sum_amount = $form->item()->sum("amount");
        $sum_sub_amount = $form->item()->sum("sub_amount");


        $qr = QrCode::size(100)->generate($url);
        $view_path_public = "warehouse.dashboard.";
        $view_path = $view_path_public . "print.template" . $packing_type_label_printing_type->id;
        $software_name = Setting::getStringValue("software_name");

        $page_number = 0;

        $confirm_user_logs = FormLog::where(["form_id" => $form->id, "status_id" => 500000200])->groupBy("user_id")->get();

        if ($print_type == "product_and_packing_form" || $print_type == "product") {
            // Page 1
            $html[$page_number] = view($view_path_public . "print._head")->render();
            $html[$page_number] .= view($view_path . "._print_info_page2",
                    compact("product_packing_form_count", "form", "confirm_user_logs", "sum_sub_amount", "sum_amount", "packing_form", "qr", "software_name"))->render() . $html[$page_number];
            $html[$page_number] .= view($view_path_public . "print._footer")->render();
            $page_number++;
        }
//        if ($print_type == "product_and_packing_form" || $print_type == "packing_form") {
//
//            if ($form->item()->count() > 350) {
//                return [
//                    "result" => false,
//                    "error" => "با توجه به اینکه تعداد ردیف های بسته بندی بیش از حد مجاز است، امکان پرینت برگ خروج به تفکیک بسته بندی وجود ندارد."
//                ];
//            }
//            // Page 2
//            if (view()->exists($view_path . "._print_info_page2")) {
//                $html[$page_number] = view(ExitFormController::$view_path . "print._head")->render();
//                $html[$page_number] .= view($view_path . "._print_info_page2",
//                        compact("product_request_form_logs", "product_request_form", "form", "sum_sub_amount", "sum_amount", "packing_form", "qr", "software_name"))->render() . $html[$page_number];
//                $html[$page_number] .= view(ExitFormController::$view_path . "print._footer")->render();
//                $page_number++;
//            }
//        }
        $printer = $packing_type_label_printing_type->getPrinter($worker);
        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $form->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $printer->id,
                "number_of_prints" => $number_of_prints
            ]);

        }

        return ["result" => true, "html" => $html, "print_file" => $print_file];
    }


    /**
     * @param Form $form
     * @return array
     * اگر فرم ورود از سامانه ارسال شده باشد، باید برگ های خروج آن تایید شود.
     */
    public static function ConfirmApplicantForInputForm(Form $form)
    {

        switch ($form->applicant_type_id) {
            case 30: // مشتری
                $customer = Customer::find($form->applicant_id);
                if (!$customer->software_system) {
                    return ["result" => true];
                }
                $result_api_login = SoftwareSystem::Login($customer->software_system, $customer->api_url, $customer->api_username, $customer->api_password, $customer->api_key);
                if (!$result_api_login["result"]) {
                    return [
                        "result" => false,
                        "error" => $result_api_login["message"]
                    ];
                }

                $message = " تایید فرم ورود به انبار " . $form->code . " در " . Setting::getStringValue("software_name");
                $result_api_confirm_applicant_for_input = SoftwareSystem::CallConfirmApplicantForInputForm(
                    $customer->software_system,
                    $customer->api_url,
                    $result_api_login["token"],
                    $form,
                    $message

                );
                if (!$result_api_confirm_applicant_for_input["result"]) {
                    SoftwareSystem::Logout(
                        $customer->software_system,
                        $customer->api_url,
                        $result_api_login["token"]
                    );
                    return [
                        "result" => false,
                        "error" => "خطای " . $customer->software_system->caption . " در " . $customer->caption . ": <br/>" . $result_api_confirm_applicant_for_input["error"] . "<br/>"
                    ];
                }

                // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
                SoftwareSystem::Logout(
                    $customer->software_system,
                    $customer->api_url,
                    $result_api_login["token"]
                );

                return [
                    "result" => true,
                    "message" => "عملیات تایید فرم موفقیت آمیز بود.."
                ];
                break;

            case 20: // پیمانکار
                $contractor = Contractor::find($form->applicant_id);
                if (!$contractor->software_system) {
                    return ["result" => true];
                }

                // بررسی اینکه برگ خروج همراه با کدام فرم ورود در سامانه مشتری ثبت شده است.
                // اگر چند فرم ورود باشد، باید همه آنها تایید شوند تا بتوانیم برگ خروج معادل آن در سامانه پیمانکار را تایید کنیم.
                $json_data = JsonDataList::where("message_type_id", 350)-> // // لیست فرم های ورودی که برای یک برگ خروج صادر شده است.
                whereLike("data", "%*" . $form->id, "*")->first();
                if (!$json_data) {
                    return [
                        "result" => false,
                        "error" => "لیست فرم های معادل فرم ورود به انبار " . $form->code . " جهت تایید برگ خروج در سامانه پیمانکار یافت نشد"
                    ];
                }

                $form_data_list = json_decode($json_data->data, 1);
                $other_input_form_ids = $form_data_list["input_form_ids"];
                $output_form_code = $form_data_list["output_form_code"];
                // فرمی که در حال بررسی هستیم را از لیست حذف می کنیم
                unset($other_input_form_ids[$form->id]);
                // باقی فرم ها باید تایید شده باشند، اگر حداقل یک مورد تایید نشده بود، نیاز به تایید برگ خروج نیست
                // تا زمانی که همه تایید شوند.
                $not_confirm_form = Form::whereIn("id", array_keys($other_input_form_ids))->
                where("status_id", "!=", 500000200)->first();
                if ($not_confirm_form) {
                    return [
                        "result" => true,
                        "message" => "چون فرم ** تایید نشده است،امکان تایید برگ خروج معادل وجود ندارد."
                    ];
                }


                $result_api_login = SoftwareSystem::Login($contractor->software_system, $contractor->api_url, $contractor->api_username, $contractor->api_password, $contractor->api_key);
                if (!$result_api_login["result"]) {
                    return [
                        "result" => false,
                        "error" => $result_api_login["message"]
                    ];
                }

                $message = " تایید فرم ورود به انبار " . $form->code . " در " . Setting::getStringValue("software_name");

                $output_form_codes = [];
                $output_form_codes[] = $output_form_code;
                $other_data_json = json_encode($output_form_codes);

                $result_api_confirm_applicant_for_input = SoftwareSystem::CallConfirmApplicantForInputForm(
                    $contractor->software_system,
                    $contractor->api_url,
                    $result_api_login["token"],
                    $form,
                    $message,
                    $other_data_json
                );
                if (!$result_api_confirm_applicant_for_input["result"]) {
                    SoftwareSystem::Logout(
                        $contractor->software_system,
                        $contractor->api_url,
                        $result_api_login["token"]
                    );
                    return [
                        "result" => false,
                        "error" => "خطای " . $contractor->software_system->caption . " در " . $contractor->caption . ": <br/>" . $result_api_confirm_applicant_for_input["error"] . "<br/>"
                    ];
                }

                // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
                SoftwareSystem::Logout(
                    $contractor->software_system,
                    $contractor->api_url,
                    $result_api_login["token"]
                );

                return [
                    "result" => true,
                    "message" => "عملیات تایید فرم موفقیت آمیز بود.."
                ];
                break;
        }
        return ["result" => true];
    }


    /*
     * ثبت ورود همه بسته بندی هایی که در انتظار تایید هستند در بار را به صورت یکجا می زنیم.
     */
    public static function SetToWarehouse(Transport $transport)
    {


        foreach ($transport->items as $transportItem) {

            foreach ($transportItem->transport_packing_list as $item) {

                if ($item->packing_form->status_id == 7007002) {

                    self::SubmitNewPackingToWarehouse($item->packing_form, $transportItem->user_id);
                }
            }
        }
    }

    /*
     * ورود به انبار با پالت
     */
    public function set_to_warehouse_pallet(Pallet $pallet, $user_id, $number_packing_submit, $packing_message, $last_product_id_read, $checking_product_change_in_entry)
    {

        $error_message = "";
        $success_message = null;
        $number = 0;
        foreach ($pallet->items as $pallet_packing) {

            $result = self::SubmitNewPackingToWarehouse($pallet_packing->packing_form, $user_id);
            if (!$result["result"]) {
                $error_message .= $result["error"] . "<br/>";
            } else {
                $number++;
            }
        }


        $number_packing_submit += $number;

        if ($error_message == "") {
            $error_message = null;
        }
        if ($number > 0) {
            $success_message = "تعداد $number بسته بندی با  موفثیت ثبت گردید.";
        }

        return view($this->view_path . "_confirm_packing", compact("error_message", "success_message", "user_id", "number_packing_submit", "packing_message", 'last_product_id_read', 'checking_product_change_in_entry'));

    }


    /*
     * ثبت ورود به انبار به صورت دست جمعی
     */
    public function set_to_warehouse_all()
    {

        $all_packing_form_data = PackingForm::whereIn("status_id", [7007002, 7007009])->pluck("id", "id")->toArray();
        $all_packing_form_in_warehouse = PackingForm::whereIn("status_id", [7007003])->pluck("id", "id")->toArray();


        $packing_form_read_ids = [];
        $all_transport_packing_form_ids = [];
        $transport_count = 0;
        $user_id = Auth::id();
        return view($this->view_path . "set_to_warehouse_all", compact("all_packing_form_data", "packing_form_read_ids", "all_transport_packing_form_ids", "transport_count", "user_id", "all_packing_form_in_warehouse"));

    }

    public function submit_set_to_warehouse_api(Request $request)
    {
        $user_id = $request->user_id;

        $data = $request->packing_form_read_ids;

        $data[] = -1;
        $packing_forms = PackingForm::whereIn("id", $data)->get();

        foreach ($packing_forms as $item) {

            if ($item->status_id == 7007002) {

                self::SubmitNewPackingToWarehouse($item, $user_id);
            }
        }

    }



//
//    public function confirm_form( Request $request, Form $form ) {
//        $result = DashboardController::check_permission( $form );
//        if ( ! $result["result"] ) {
//            return back()->withErrors( $result["message"] );
//        }
//        if ( ! in_array( $form->form_type_id, [ 301, 305 ] ) ) {
//            return back()->withErrors( "عملیات فرم به درستی پیاده سازی نشده است." );
//        }
//
//        if ( $form->status_id == 500000200 ) {
//            return back()->withErrors( "این فرم قبلا تایید شده است" );
//        }
//
//        $form_item = $form->item()->first();
//        if ( $form_item->carrier->code != $request->carrier_code ) {
//
//            return back()->withErrors( "شماره حامل (غلطک) به درستی وارد نشده است" );
//        } else {
//
//            $form->status_id = 500000200;
//            $form->save();
//            $form_item->carrier->SetStatus( 5320002 );
//
//            event( new FormLogEvent( $form ) );
//            event( new PutInWarehouseEvent( $form ) );
//
//            return redirect()->route( $this->route_path . "index" )->
//            with( [ "success" => "عملیات با موفیت انجام شد." ] );
//        }
//    }
//
//    public function reject_form( Request $request, Form $form ) {
//        $result = DashboardController::check_permission( $form );
//        if ( ! $result["result"] ) {
//            return back()->withErrors( $result["message"] );
//        }
//        if ( $form->form_type_id != 301 ) {
//            return back()->withErrors( "عملیات فرم به درستی پیاده سازی نشده است." );
//        }
//
//        if ( $form->status_id == 500000200 ) {
//            return back()->withErrors( "این فرم قبلا تایید شده است" );
//        }
//        $form->status_id = 500000420;
//        $form->save();
//        event( new FormLogEvent( $form ) );
//
//        return redirect()->route( $this->route_path . "index" )->
//        with( [ "success" => "عملیات با موفیت انجام شد." ] );
//    }
}
