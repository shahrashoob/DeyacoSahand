<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Events\Form\PackingLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;


class DeliveryController extends Controller
{
    var $view_path = "warehouse.out.delivery.";
    var $route_path = "wh.out.delivery.";
    var $dashbaord_path = "wh.out.dashboard.";
    var $allowed_status = [7005001, 7005005, 7005008, 7005004];
    static $PaginateNumber = 30;

    ##########################################################
    public function index(ProductRequestForm $product_request_form, $product_id = null, $page = 1, $dashboard_type = "")
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $allow_entry_with_pin = $product_request_form->warehouse->allow_entry_with_pin;

        // چک کردن دسترسی تحویل کالا
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید.");
        }

        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return back()->withErrors("این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.");
        }

        if ($product_request_form->order) {
            if ($product_request_form->order->loading_status_id == 460000100 && $product_request_form->order->code() == $product_request_form->getReferenceNumber()) {
                return back()->withErrors("مجوز بارگیری برای سفارش " . $product_request_form->order->code() . " صادر نشده است،");
            }
        }

        if ($product_request_form->warehouse->warehouse_type_id != 1) {
            return redirect()->route($this->route_path . "packing_list_data_for_select", [
                $product_request_form,
                0,
                $page
            ]);
        }
        // لیست بسته های انتخاب شده
        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, true, $dashboard_type == "customer");
        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }

        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $amount_select = isset($session_data["amount_select"]) ? $session_data["amount_select"] : [];


        // لیست بسته هایی که در انبار موجود است و کالای درخواست شده در آن قرار دارد.
        $packing_list_data = null;

        $product_request_form_ids = [];
        $product_request_form_ids[] = $product_request_form->id;

        $packing_form_ids = DeliveryController::getPackingInWarehouse($product_request_form, $product_id, 0, "only_packing_form_ids", null, $dashboard_type);

        if (count($packing_form_ids) == 0) {
            return back()->withErrors("هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد ویا همه ردیف های درخواست به صورت کامل تحویل شده است.");
        }

        $packing_list_json_data = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        whereIn("packing_forms.id", $packing_form_ids)->
        groupBy("packing_forms.id")->
        selectRaw("packing_forms.id,packing_forms.code,packing_type_id, carrier_id,
         sub_packing_form_number,sum(final_amount) as final_amount,pin1")->
        get()->keyBy(
            $allow_entry_with_pin ? "pin1" : "code"
        );


        $selected_packing_list = PackingForm::whereIn("packing_forms.id", $selected_packing_ids)->
        when($product_id, function ($query) use ($product_id) {

            return $query->join("packing_form_item", "packing_forms.id", "packing_form_id")->
            where("product_id", $product_id);
        })->
        select("packing_forms.id", "packing_forms.code", "packing_forms.pin1", "packing_type_id", "carrier_id", "sub_packing_form_number")->
        paginate();
//        //بررسی اینکه آیا باید انطباق درجه را چک کنیم یا خیر
//        $checking_compatibility_grade_in_delivery = PackingFormItem::
//        join( "products", "products.id", "product_id" )->
//        join( "goods_kinds", "goods_kinds.id", "goods_kind_id" )->
//        whereIn( "packing_form_id", $packing_ids )->
//        sum( "checking_compatibility_grade_in_delivery" );
//
//        $degree_ids = [];
//        if ( $checking_compatibility_grade_in_delivery ) {
//
//            $degree_ids = $product_request_form->items()->
//            groupBy( "degree_id" )->pluck( "degree_id" )->toArray();
//
//            // لیست بسته هایی که در انبار موجود است و کالای درخواست شده در آن قرار دارد.
//            $packing_list = DeliveryController::getPackingInWarehouse( $product_request_form, $product_id, 1, $degree_ids );
//
//            if ( count( $packing_list ) == 0 ) {
//                return back()->withErrors( "هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد." );
//            }
//
//            $packing_ids = [];
//            foreach ( $packing_list as $item ) {
//                $packing_ids[] = $item->id;
//            }
//        }


        // بسته بندی حمل و نقل
        $packing_list_transport_item = TransportPackingForm::
        join("transport_item", "transport_item_id", "transport_item.id")->
        whereIn("packing_form_id", $packing_form_ids)->
        where("transport_packing_form.status_id", 0)->
        pluck("code", "packing_form_id")->
        toArray();

        //

        $product = Product::find($product_id);

        // آیا امکان انتخاب از کالا در داشبورد خروج از کالا امکان پذیر است؟
        $allow_select_partial_of_packing_in_output =
            $product_request_form->items()->first()->product->goods_kind->allow_select_partial_of_packing_in_output &&
            $product_request_form->warehouse->allow_select_partial_of_packing_in_output;

        return view($this->view_path . "index", compact("packing_list_data", "packing_list_transport_item",
            "selected_packing_list", "packing_list_json_data", "product", "product_request_form",
            "selected_packing_ids", "count_select", "amount_select", "page", "product_id", "allow_entry_with_pin",
            "dashboard_type", "allow_select_partial_of_packing_in_output"));

    }

    public function submit(Request $request, ProductRequestForm $product_request_form, $page = 1, $product_id_delivery = null, $dashboard_type = "")
    {


        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return back()->withErrors("این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.");
        }

        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form);

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if (!isset($request->data["packing"])) {

            // ممکن است، یک ببسته بندی را به صورت دستی انتخاب کرده باشد، که در صفحات دیگر است.
            if (!isset($session_data["selected_packing_ids"]) || count($session_data["selected_packing_ids"]) == 0) {

                $session_data["selected_packing_ids"] = [];
                Product\ProductRequest\ProductRequestFormSessionData::
                setData($product_request_form, $session_data);

                return back()->withErrors("لطفا یک بسته انتخاب نمایید.");
            }


        }

        $request_data = $request->data ?? null;


        $selected_packing_ids = $session_data["selected_packing_ids"];
        $selected_packing_ids[] = -1;
        $count_select = $session_data["count_select"];
        $amount_select = $session_data["amount_select"];
        $selected_packing_list = PackingForm::whereIn("id", $selected_packing_ids)->get()->keyBy("id");

        // بررسی اینکه به ازای هر بسته بندی کل (بخشی از) بسته بندی انتخاب شده است
        // اگر کل بسته بندی انتخاب شده بود، لازم نیست، که در لیست count_select وجود داشته باشد.
        // در count_select فقط بسته بندی هایی وجود دارد که بسته بندی فرعی دارند و بخشی از بسته بندی های فرعی انتخاب شده اند.
        $count_select_request = isset($request_data["count_select"]) ? $request_data["count_select"] : [];


        // بررسی اینکه آیا قرار است بخشی از بسته بندی از انبار خارج شود یا خیر
        // در amount_select فقط لیست بسته بندی هایی می آید که بخشی از انها قرار است از انبار خارج شوند.
        $amount_select_request = isset($request_data["amount_select"]) ? $request_data["amount_select"] : [];

        foreach ($selected_packing_list as $packing_form) {
            if (isset($count_select_request[$packing_form->id])) {
                if ($count_select_request[$packing_form->id] == $packing_form->sub_packing_form_number) {
                    unset($count_select[$packing_form->id]);
                } else {
                    $count_select[$packing_form->id] = $count_select_request[$packing_form->id];
                }
            }

            if (isset($amount_select_request[$packing_form->id])) {
                // اگر تا سه رقم اعشار مثل هم بودند، لازم نیست که بخشی از بسته بندی انتخاب شود و کل آن را انتخاب می کند.
                if (floor($amount_select_request[$packing_form->id] * 1000) == floor($packing_form->getFinalAmount() * 1000)) {
                    unset($amount_select_request[$packing_form->id]);
                } else {
                    $amount_select[$packing_form->id] = $amount_select_request[$packing_form->id];
                }
            }
        }


        // اگر درخواست کالا از انبارک به انبارک بود، باید به ازای هر بسته بندی که انتخاب می کند، مقدار خارج شده از آن را هم انتخاب کنید
        $exit_amount_of_packing_form = isset($request_data["exit_amount_of_packing_form"]) ? $request_data["exit_amount_of_packing_form"] : [];
        foreach ($exit_amount_of_packing_form as $exit_amount) {
            if ($exit_amount <= 0) {
                return back()->withErrors("مقدار وارد شده برای بسته بندی جهت خروج معتبر نمی باشد.");
            }
        }

        if ($selected_packing_ids == null || count($selected_packing_ids) == 0) {
            return back()->withErrors("لطفا یک بسته انتخاب نمایید.");
        }

        $count_not_set_remaining = $product_request_form->items()->whereNull("amount_request")->count();
        $selected_packing_count = count(array_filter($selected_packing_ids, function ($k) {
            return $k > 0;
        }));
        if ($count_not_set_remaining != 0 && $selected_packing_count > 1) {
            $product_count = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->distinct("product_id")->count();
            if ($product_count != $count_not_set_remaining) {
                return back()->withErrors("از آنجایی که مقدار درخواست مشخص نشده است، به ازای هر ردیف درخواست باید فقط یک بسته بندی انتخاب نمایید.");
            }
        }


        $session_data["selected_packing_ids"] = $selected_packing_ids;
        $session_data["count_select"] = $count_select;
        $session_data["amount_select"] = $amount_select;

        $session_data["exit_amount_of_packing_form"] = $exit_amount_of_packing_form;

        if ($dashboard_type == "customer") {
            return redirect()->route("wh.out.customer.view", ["customer" => $product_request_form->order->customer_id, "page" => $page]);
        }

        return $this->checkOtherProductRequestFrom($product_request_form, $session_data, $selected_packing_ids, $product_id_delivery, $page);


    }

    public function delivery_product_btn(ProductRequestForm $product_request_form, $page, $dashboard_type = "")
    {

        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, true, $dashboard_type == "customer");

        $selected_packing_ids = isset($session_data["selected_packing_ids"]) ? $session_data["selected_packing_ids"] : [];

        return $this->checkOtherProductRequestFrom($product_request_form, $session_data, $selected_packing_ids, 0, $page, $dashboard_type);

    }

    public function checkOtherProductRequestFrom($product_request_form, $session_data, $selected_packing_ids, $product_id_delivery, $page, $dashboard_type = "")
    {
        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $amount_select = isset($session_data["amount_select"]) ? $session_data["amount_select"] : [];

        $necessary_participant_product_ids = []; //  // آیا بسته همراه برای تحویل لازم است
        $product_request_form_item_amount_remaining = [[]];
        // لیست کالا - درجه درخواست
        foreach ($product_request_form->items as $item) {
            if (!isset($product_request_form_item_amount_remaining[$item->product_id][$item->degree_id])) {
                $product_request_form_item_amount_remaining[$item->product_id][$item->degree_id] = 0;
                $product_request_form_item_amount_request[$item->product_id][$item->degree_id] = 0;
            }
            $product_request_form_item_amount_remaining[$item->product_id][$item->degree_id] += $item->amount_remaining;
            $product_request_form_item_amount_request[$item->product_id][$item->degree_id] += $item->amount_request;

            // لیست کالا - بسته بندی

        }

        // از آنجایی که بررسی ها بر روی درجه کالا هست، اگر یک مقدار کمتر از min انتخاب شد، باید مطمئن شویم که برای همه کالا درجه ها از min کمتر است و بعد خطا بدهیم.
        $min_error_product = [];
        $min_error_product_info = [];

        // بررسی اینکه به ازای همه آیتم های درخواست مقداری انتخاب شده باشد، اگر مقدار صفر باشد، لازم نیست چک شود.
        foreach ($product_request_form->items as $product_request_form_item) {

            $packing_type_ids = $product_request_form_item->product_request_form_packing_types()->pluck("packing_type_id")->toArray();

            $list_packing_form_products = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
            whereIn("packing_type_id", $packing_type_ids)->
            whereIn("packing_form_id", $selected_packing_ids)->
            where("product_id", $product_request_form_item->product_id)->
            select("packing_form_id", "final_amount", "sub_packing_form_number")->
            get();

            $amount = 0;

            // چون ممکن است بخشی از یک بسته بندی انتخاب شود، اگر بخشی از بسته بندی انتخاب شده بود، فقط همان مقداری که انتخاب شده است را در نظر می گیرد.
            foreach ($list_packing_form_products as $packing_form_product) {

                $number_of_select = isset($count_select[$packing_form_product->packing_form_id]) ?
                    $count_select[$packing_form_product->packing_form_id] :
                    $packing_form_product->sub_packing_form_number;

                $amount_select_of_packing = isset($amount_select[$packing_form_product->packing_form_id]) ?
                    $amount_select[$packing_form_product->packing_form_id] : -1;

                if ($amount_select_of_packing == -1) { // اگر مقدار خاصی را انتخاب نکرده بودند، همان را تحویل میدهیم.
                    $amount += $packing_form_product->sub_packing_form_number == 0 ?
                        $packing_form_product->final_amount :
                        $packing_form_product->final_amount * $number_of_select / $packing_form_product->sub_packing_form_number;;

                }

                $amount += $amount_select_of_packing;
            }


            // محاسبه مقدار فرم های درحال تحویل که هنوز تراکنش آنها ثبت نشده است.
            $sum_form_in_sending = DashboardController::getCurrentDelivery($product_request_form_item);

            $amount += $sum_form_in_sending["sum_amount"];

            $product = $product_request_form_item->product;

            // جمع کل کالا - درجه های درخواست به ازای کالای ایتمی که درحال بررسی است.
            $amount_remaining_min = $product_request_form_item_amount_remaining[$product_request_form_item->product_id][$product_request_form_item->degree_id];
            $min = $amount_remaining_min -
                $amount_remaining_min * $product->goods_kind->allowed_percentage_to_be_lower / 100;

            $amount_remaining_max = array_sum($product_request_form_item_amount_remaining[$product_request_form_item->product_id]);
            $amount_request = array_sum($product_request_form_item_amount_request[$product_request_form_item->product_id]);

            // حداکثر مقداری که می تواند تحویل دهد: مقدار باقی مانده کل درخواست + مقدار درخواست * x درصد تقسیم بر 100
            $max = $amount_remaining_max +
                $amount_request * $product->goods_kind->allowed_percentage_to_be_higher / 100;

            //اگر مقدار درخواست مشخص شده باشد
            if (isset($product_request_form_item->amount_request)) {

                $min_error_product[$product_request_form_item->product_id] = false;
                if ($amount < $min && $amount != 0) {
                    $min_error_product[$product_request_form_item->product_id] = true;
                    $min_error_product_info[$product_request_form_item->product_id]["amount"] = $amount;
                    $min_error_product_info[$product_request_form_item->product_id]["amount_remaining_min"] = $amount_remaining_min;

                }

                if ($amount > $max && $amount != 0) {
                    // return $product_request_form_item->id."***".$amount."---".$max;
                    $necessary_participant_product_ids[] = $product_request_form_item->product_id;
                }
            }
        }

        // بررسی اینکه باید برای مقدار min خطا بدهیم یا خیر
        foreach ($min_error_product as $product_id => $item) {

            if ($item) {
                $product = Product::find($product_id);

                $special_license = self::HasSpecialLicenseMin($product_request_form, $product->id, $min_error_product_info[$product->id]["amount"]);
                if (!$special_license) {

                    return back()->withErrors("امکان تحویل کالا کمتر از مقدار درخواست تا " . $product->goods_kind->allowed_percentage_to_be_lower . " درصد  مجاز است و بیش از این مقدار مجاز نمی باشد." .
                        "<br/>" .
                        SpecialLicense::GetLink(
                            7,
                            $product_request_form, "ثبت مجوز جهت تحویل کالا برای " . $product->caption,
                            $product->id,
                            $min_error_product_info[$product->id]["amount_remaining_min"],
                            $min_error_product_info[$product->id]["amount"]
                        )
                    );
                }

            }
        }


        $product_request_form_product_ids = $product_request_form->items()->pluck("product_id")->toArray();
        $packing_form_item_list = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->get();

        // همه کالاهای داخل بسته بندی باید در درخواست موجود باشد، در غیر این صورت باید یک درخواست دیگری را انتخاب نماید.
        foreach ($packing_form_item_list as $item) {
            if (!in_array($item->product_id, $product_request_form_product_ids)) {
                $necessary_participant_product_ids[] = $item->product_id;
            }
        }

        // بررسی اینکه به ازای هر ردیف درخواست از چه کالایی چند بسته بندی مجاز است.
        foreach ($packing_form_item_list as $item) {
            if (!in_array($item->product_id, $product_request_form_product_ids)) {
                $necessary_participant_product_ids[] = $item->product_id;
            }
        }

        $session_data["necessary_participant_product_ids"] = $necessary_participant_product_ids;
        $session_data["participant_request_form_ids"] = [$product_request_form->id];
        $session_data["need_new_packing"] = [];
        Product\ProductRequest\ProductRequestFormSessionData::
        setData($product_request_form, $session_data);

        if (count($necessary_participant_product_ids) == 0) {

            if ($product_id_delivery) {
                return redirect()->route($this->dashbaord_path . "view", [$product_request_form, $page, $dashboard_type]);
            } else {
                return redirect()->route($this->route_path . "checkout", [$product_request_form, $page, $dashboard_type]);
            }
        } else {
            // باید یک درخواست همراه نیز انتخاب نماید.
            return redirect()->route($this->route_path . "select_other_product_request_form_item", [
                $product_request_form,
                $page,
                $dashboard_type
            ]);
        }
    }

    public function packing_list_data_for_select(ProductRequestForm $product_request_form, $product_id = null, $page = 1, $dashboard_type = "")
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return back()->withErrors("این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.");
        }

        if ($product_request_form->order) {
            if ($product_request_form->order->loading_status_id == 460000100 && $product_request_form->order->code() == $product_request_form->getReferenceNumber()) {
                return back()->withErrors("مجوز بارگیری برای سفارش " . $product_request_form->order->code() . " صادر نشده است،");
            }
        }

        // لیست بسته های انتخاب شده
        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, false, $dashboard_type == "customer");
        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }

        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $amount_select = isset($session_data["amount_select"]) ? $session_data["amount_select"] : [];
        $exit_amount_of_packing_form = isset($session_data["exit_amount_of_packing_form"]) ? $session_data["exit_amount_of_packing_form"] : [];


        // لیست بسته هایی که در انبار موجود است و کالای درخواست شده در آن قرار دارد.
        $packing_list = DeliveryController::getPackingInWarehouse($product_request_form, $product_id, 1, null, null, $dashboard_type == "customer");
        $packing_list_data = null;
        if (count($packing_list) == 0) {
            return back()->withErrors("هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد.");
        }


        $packing_ids = [];
        $packing_forms = [];
        foreach ($packing_list as $item) {
            $packing_ids[] = $item->id;
            $packing_forms[$item->id] = $item;
        }

        $packing_list_data = $this->getPackingListData($packing_ids, $packing_forms);

        if (count($packing_list) == 0) {
            return back()->withErrors("هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد.");
        }


        // آیا امکان انتخاب از کالا در داشبورد خروج از کالا امکان پذیر است؟
        $allow_select_partial_of_packing_in_output =
            $product_request_form->items()->first()->product->goods_kind->allow_select_partial_of_packing_in_output &&
            $product_request_form->warehouse->allow_select_partial_of_packing_in_output;


        $warehouse_shelving_list = WarehouseShelving::pluck("fullCode", "id");
        return view($this->view_path . "packing_list_data_for_select",
            compact("packing_list_data", "warehouse_shelving_list", "amount_select",
                "product_request_form", "selected_packing_ids", "packing_list", "allow_select_partial_of_packing_in_output",
                "count_select", "page", "product_id", "exit_amount_of_packing_form", "dashboard_type"
            ));

    }

    public function getPackingListData($packing_ids, $packing_forms)
    {
        $packing_list_data_raw = PackingFormItem::
        join("products", "products.id", "product_id")->
        join("units", "units.id", "unit_id")->
        whereIn("packing_form_id", $packing_ids)->
        selectRaw("production_form_item_id,packing_form_id,sum(final_amount) as sum_amount,  count(distinct(product_id)) as count_product_id, products.caption as product_caption, units.caption as unit_caption")->
        groupBy("packing_form_id")->
        get();

        $packing_list_data = [];
        foreach ($packing_list_data_raw as $item) {
            $packing_list_data[$item->packing_form_id]["sum_final_amount"] = $item->sum_amount;
            $packing_list_data[$item->packing_form_id]["count_product_id"] = $item->count_product_id;
            $packing_list_data[$item->packing_form_id]["product_caption"] = $item->product_caption;
            $packing_list_data[$item->packing_form_id]["unit_caption"] = $item->unit_caption;
            if ($item->packing_form->applicant_type_id) {
                $packing_list_data[$item->packing_form_id]["applicant_caption"] = $item->packing_form->applicant->fullCaption();
            }
            $packing_list_data[$item->packing_form_id]["count_item"] = $packing_forms[$item->packing_form_id]->getItemCount("items");
            $packing_list_data[$item->packing_form_id]["count_content"] = $packing_forms[$item->packing_form_id]->sub_packing_form_number;

            $packing_list_data[$item->packing_form_id]["production_serial"] = null;
            $packing_list_data[$item->packing_form_id]["production_parent_serial"] = null;
            $packing_list_data[$item->packing_form_id]["production_id"] = null;
            $packing_list_data[$item->packing_form_id]["parent_production_id"] = null;
            if ($item->production_form_item_id) {

                $packing_list_data[$item->packing_form_id]["production_serial"] =
                    $item->production_form_item->production->serial ?? "";

                $packing_list_data[$item->packing_form_id]["production_id"] = $item->production_form_item->production_id ?? "";

                if (isset($item->production_form_item->production) && $item->production_form_item->production->parent_production_id) {
                    $packing_list_data[$item->packing_form_id]["production_parent_serial"] =
                        $item->production_form_item->production->parent_production->serial ?? "";

                    $packing_list_data[$item->packing_form_id]["parent_production_id"] = $item->production_form_item->production->parent_production_id;
                }
            }
        }

        $packing_list_transport_item_row = TransportPackingForm::
        join("transport_item", "transport_item_id", "transport_item.id")->
        whereIn("packing_form_id", $packing_ids)->
        where("transport_packing_form.status_id", 0)-> // مرجوعی و عدل های باز شده ملاک نیست
        select("transport_item_id", "code", "packing_form_id")->
        get();

        foreach ($packing_list_transport_item_row as $item) {
            $packing_list_data[$item->packing_form_id]["transport_item_code"] = $item->code;
            $packing_list_data[$item->packing_form_id]["transport_item_id"] = $item->transport_item_id;
        }

        return $packing_list_data;
    }

    ###############################################################################
    public function select_other_product_request_form_item(ProductRequestForm $product_request_form, $page = 1, $dashboard_type = "")
    {

        $selected_product_amount = [];
        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return back()->withErrors("این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.");
        }

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, true, $dashboard_type == "customer");
        $selected_packing_ids = $session_data["selected_packing_ids"];

        if (count($selected_packing_ids) == 0) {
            return back()->withErrors("2 لطفا حداقل یک بسته بندی انتخاب نمایید.");
        }


        $necessary_participant_product_ids = isset($session_data["necessary_participant_product_ids"]) ?
            $session_data["necessary_participant_product_ids"] :
            $session_data["participant_request_form_ids"];
        $necessary_participant_product_ids_remove_license = [];
        //چک کردن اینکه برای همه آیتم ها مجوز خروج تایید شده وجود داشته باشد
        foreach ($necessary_participant_product_ids as &$necessary_participant_product_id) {
            $selected_product_amount[$necessary_participant_product_id] = PackingFormItem::
            where("product_id", $necessary_participant_product_id)->
            whereIn("packing_form_id", $selected_packing_ids)->
            sum("final_amount");
            $special_license = self::HasSpecialLicenseMax($product_request_form, $necessary_participant_product_id, $selected_product_amount[$necessary_participant_product_id]);
            if (!$special_license && $selected_product_amount[$necessary_participant_product_id] > 0) {
                $necessary_participant_product_ids_remove_license[] = $necessary_participant_product_id;
            }

        }
        // کالاهایی که مجوز خروج دارند از لیست حذف می کنیم.
        $necessary_participant_product_ids = $necessary_participant_product_ids_remove_license;

        // اگر هیچ کالایی نیست که مجوز نداشته باشد، بنابراین به صفحه تحویل درخواست می رویم.
        if (count($necessary_participant_product_ids) == 0) {
            return redirect()->route($this->route_path . "checkout", [$product_request_form, $page, $dashboard_type]);
        }


        $selected_packing_list = PackingForm::whereIn("id", $selected_packing_ids)->paginate(30);
        $selected_packing_packing_type_ids = PackingForm::whereIn("id", $selected_packing_ids)->pluck("packing_type_id")->toArray();

        //گرفتن دیگر لیست درخواست هایی که درخواست دهنده آنها با درخواست دهنده جاری یکی است.
        $product_request_form_ids = ProductRequestForm::
        where([
            "applicant_type_id" => $product_request_form->applicant_type_id,
            "applicant_id" => $product_request_form->applicant_id,
            "warehouse_id" => $product_request_form->warehouse_id
        ])->
        where("id", "!=", $product_request_form->id)->
        whereIn("status_id", $this->allowed_status)->
        pluck("id")->
        toArray();


        $packing_form_final_amount_list = PackingFormItem::
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("product_id")->
        whereIn("product_id", $necessary_participant_product_ids)->
        addSelect(DB::raw("product_id ,sum(final_amount) as final_amount"))->
        pluck("final_amount", "product_id")->
        toArray();

        $packing_ids = [];
        foreach ($selected_packing_list as $item) {
            $packing_ids[] = $item->id;
        }

        // آیا تیک بررسی انطباق درجه true است
        $checking_compatibility_grade_in_delivery = PackingFormItem::
        join("products", "products.id", "product_id")->
        join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
        whereIn("packing_form_id", $packing_ids)->
        sum("checking_compatibility_grade_in_delivery");

        $degree_ids = [];
        if ($checking_compatibility_grade_in_delivery) {
            $degree_ids = $product_request_form->items()->
            groupBy("degree_id")->pluck("degree_id")->toArray();

        }

        //آیتم های درخواست کالا که امکان انتخاب آنها وجود دارد.
        $product_request_form_item_list = ProductRequestFormItem::
        join("product_request_form_packing_type", "product_request_form_packing_type.product_request_form_item_id", "product_request_form_item.id")->
        whereIn("product_request_form_item.product_id", array_keys($packing_form_final_amount_list))->
        where("product_request_form_item.amount_remaining", ">", 0)-> // لیست درخواست هایی که باقی مانده برای تحویل دارند
        when(count($degree_ids) > 0, function ($query) use ($degree_ids) {
            return $query->whereIn("degree_id", $degree_ids);
        })->
        whereIn("product_request_form_packing_type.packing_type_id", $selected_packing_packing_type_ids)->
        whereIn("product_request_form_item.product_request_form_id", $product_request_form_ids)->
        orderBy("product_request_form_item.product_request_form_id")->
        get();

        if (count($product_request_form_item_list) == 0) {
            $message = "از آنجایی که بسته بندی منتخب شما شامل کالاهای دیگری به
            غیر از کالای درخواست شده (یا مقداری بیشتر از مقدار درخواست) می باشد،
             و درخواستی برای آنها از سوی درخواست کننده وجود ندارد،
             تحویل این بسته نیز امکان پذیر نمی باشد، لطفا بسته بندی دیگری را انتخاب نمایید.";
            $product_licence_required = 0;
            $product_ids_licence = [];
            foreach ($necessary_participant_product_ids as $necessary_participant_product_id) {
                if (ceil($selected_product_amount[$necessary_participant_product_id] > 0)) {
                    if (isset($product_ids_licence[$necessary_participant_product_id])) {
                        continue;
                    }
                    $product_ids_licence[$necessary_participant_product_id] = 1; // برای اینکه درخواست تکراری ندهد.
                    $product = Product::find($necessary_participant_product_id);

                    $request_amount = $product_request_form->items()->where("product_id", $necessary_participant_product_id)->
                    sum("amount_request");

                    $amount_sent = $product_request_form->items()->where("product_id", $necessary_participant_product_id)->
                    sum("amount_sent");
                    if ($amount_sent == 0) {
                        // اگر هنوز هیچ مقداری ارسال نکرده است، مقدار
                        //$amount_sent
                        //  را صفر می کنیم، چون این مقدار در
                        // $selected_product_amount
                        // دیده شده است.
                        $request_amount = 0;
                    }

                    $message .= "<br/>" . SpecialLicense::GetLink(1, $product_request_form->id, "ثبت درخواست مجوز برای  " . $product->caption, $product->id, ceil($request_amount), ceil($request_amount) + ceil($selected_product_amount[$necessary_participant_product_id]));
                    $product_licence_required++;
                }
            }
            if ($product_licence_required > 0) {
                return back()->withErrors($message);
            }
        }


        $product_list = Product::whereIn("id", $necessary_participant_product_ids)->get();

        return view($this->view_path . "select_other_product_request_form_item", compact("product_list", "product_request_form_item_list", "product_request_form", "selected_packing_list", "packing_form_final_amount_list", "page", "dashboard_type"));

    }

    public function submit_select_other_product_request_form_item(Request $request, ProductRequestForm $product_request_form, $page = 1, $dashboard_type = "")
    {

        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return back()->withErrors("این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.");
        }

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, true, $dashboard_type == "customer");

        $selected_packing_ids = $session_data["selected_packing_ids"];

        if (count($selected_packing_ids) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی انتخاب نمایید. 3");
        }

        $necessary_participant_product_ids = $session_data["necessary_participant_product_ids"];
        if (count($necessary_participant_product_ids) == 0) {
            return redirect()->route($this->route_path . "checkout", [$product_request_form, $page, $dashboard_type]);
        }

        if (!isset($request->data["product_request_form"])) {
            return back()->withErrors("لطفا یک درخواست را انتخاب نمایید.");
        }

        $participant_request_form_ids = array_keys($request->data["product_request_form"]);

        $participant_request_form_ids[] = $product_request_form->id;


// لیست مقدار کالاهایی که داخل بسته بندی های انتخاب شده است.
        $packing_form_final_amount_list = PackingFormItem::
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("product_id")->
        addSelect(DB::raw("product_id ,sum(final_amount) as final_amount"))->
        pluck("final_amount", "product_id")->
        toArray();

        // به دست آوردن درصد حداقل مقدار قابل تحویل به ازای هر رسته کالایی
        $allowed_percentage_to_be_higher = Product::join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
        whereIn("products.id", array_keys($packing_form_final_amount_list))->
        groupBy("products.id")->
        pluck("allowed_percentage_to_be_higher", "products.id")->
        toArray();


        // جمع کل کالاهای درخواست های انتخاب شده و درخواست اصلی
        $product_request_form_item_list = ProductRequestFormItem::
        whereIn("product_request_form_id", $participant_request_form_ids)->
        whereIn("product_id", array_keys($packing_form_final_amount_list))->
        groupBy("product_id")->
        addSelect(DB::raw("product_id ,sum(amount_remaining) as amount_remaining"))->
        pluck("amount_remaining", "product_id")->
        toArray();


        // به ازای هر کالایی که در بسته بندی ها است، چک شود که مقدار آن از حداکثر مقدار قابل تحویل بیشتر نباشد.
        foreach ($packing_form_final_amount_list as $product_id => $packing_form_amount) {

            if (!isset($product_request_form_item_list[$product_id])) {
                $product = Product::find($product_id);
                $packing_form__list = PackingFormItem::
                whereIn("packing_form_id", $selected_packing_ids)->
                where("product_id", $product_id)->
                get();

                $message = "";
                foreach ($packing_form__list as $item) {
                    $message .= "<br/>" . $item->code;
                }

                return back()->withErrors("کالای " . $product->caption . " در بسته بندی های انتخاب شده وجود دارد ولی هیچ درخواستی برای آن انتخاب نشده است." . "<br/> لیست بسته بندی ها:" . $message);
            }

            $max = $product_request_form_item_list[$product_id] * (1 + $allowed_percentage_to_be_higher[$product_id] / 100);

            if ($packing_form_amount > $max) {
                $product = Product::find($product_id);
                $special_license = self::HasSpecialLicenseMax($product_request_form, $product->id, $packing_form_amount);
                if (!$special_license) {
                    return back()->withErrors(
                        "مقدار کالای " . $product->caption .
                        " موجود در بسته بندی ها بیش از مجموع مقدار درخواست های اننتخاب شده است." .
                        SpecialLicense::GetLink(1, $product_request_form->id, "ثبت درخواست مجوز ", $product->id, ceil($product_request_form_item_list[$product_id]), ceil($packing_form_amount))
                    );
                }
            }

        }

        $session_data["participant_request_form_ids"] = $participant_request_form_ids;
        Product\ProductRequest\ProductRequestFormSessionData::
        setData($product_request_form, $session_data);

        return redirect()->route($this->route_path . "checkout", [$product_request_form, $page, $dashboard_type]);


    }

    public function computeDeliveryToRequest(ProductRequestForm $product_request_form, $lock_product_request_form = false, $with_other_request = false, $session_data = null)
    {


        $result_function = ["result" => false, "message" => ""];
        $result_check_out = ProductRequestForm::AllowCheckOutRequest($product_request_form);
        if (!$result_check_out["result"]) {
            $result_function["message"] = $result_check_out["error"];
            return $result_function;
        }

        if ($product_request_form->status_id == 7005201) { //  در حال پردازش برگ خروج)
            $result_function["message"] = "شما درخواست‌های زیادی در زمان کوتاهی داشته‌اید. لطفا پس از مدتی دوباره تلاش کنید!";
            $result_function["repeat_request"] = "شما درخواست‌های زیادی در زمان کوتاهی داشته‌اید. لطفا پس از مدتی دوباره تلاش کنید!";

            return $result_function;
        }


        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            $result_function["message"] = "این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.";

            return $result_function;
        }

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            $result_function["message"] = $result["message"];

            return $result_function;
        }

        // قفل کردن درخواست
        if ($lock_product_request_form) {
            //در ابتدا درخواست در حال پردازش قرار می دهیم.
          //  $product_request_form->status_id = 7005201; //  در حال پردازش برگ خروج
            $product_request_form->save();
        }

        if (!$session_data) {
            $session_data = Product\ProductRequest\ProductRequestFormSessionData::
            getData($product_request_form, true, $with_other_request);
        }
//        if ($product_request_form->id == 1612) {
//            return $session_data;
//        }

        $selected_packing_ids = $session_data["selected_packing_ids"];
        $suggested_packing_ids = $session_data["suggested_packing_ids"];
        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $amount_select = isset($session_data["amount_select"]) ? $session_data["amount_select"] : [];
        $exit_amount_of_packing_form = isset($session_data["exit_amount_of_packing_form"]) ? $session_data["exit_amount_of_packing_form"] : [];
        $need_new_packing = isset($session_data["need_new_packing"]) ? $session_data["need_new_packing"] : [];

        if (!isset($selected_packing_ids) || count($selected_packing_ids) == 0 || (count($selected_packing_ids) == 1 and $selected_packing_ids[0] == -1)) {
            $result_function["message"] = "لطفا حداقل یک بسته بندی انتخاب نمایید. 1";

            return $result_function;
        }
        // بررسی اینکه در تحویل همه بسته بندی های پیشنهادی اتنتخاب شده باشد.
        if (isset($suggested_packing_ids) && count($suggested_packing_ids) > 0) {
            foreach ($selected_packing_ids as $packing_form_id_item) {
                if (!in_array($packing_form_id_item, $suggested_packing_ids)) {
                    $packing_form = PackingForm::find($packing_form_id_item);
                    if ($packing_form) {
                        $message_error_suggested = "بسته بندی " . $packing_form->code . " جزء بسته بندی های مجاز تحویل درخواست نمی باشد." . "<br/>" .
                            "لیست بسته بندی های مجاز تحویل:";
                        $suggested_packing_form = PackingForm::whereIn("id", $suggested_packing_ids)->get();
                        foreach ($suggested_packing_form as $item) {
                            $message_error_suggested .= "<br/>" . $item->code;
                        }
                        $result_function["message"] = $message_error_suggested;

                        return $result_function;
                    }
                }
            }
        }

        // اگر درخواست کالا از انبارک به انبارک بود، باید به ازای هر بسته بندی که انتخاب می کند، مقدار خارج شده از آن را هم انتخاب کنید
        $exit_amount_of_packing_form = isset($session_data["exit_amount_of_packing_form"]) ? $session_data["exit_amount_of_packing_form"] : [];
        foreach ($exit_amount_of_packing_form as $exit_amount) {
            if ($exit_amount <= 0) {
                $result_function["message"] = "مقدار وارد شده برای بسته بندی جهت خروج معتبر نمی باشد.";

                return $result_function;
            }

        }
        // چک کردن بسته بندی های حمل و نقل
        $transport_packing = TransportPackingForm::
        where("product_request_form_id", $product_request_form->id)->
        whereIn("packing_form_id", $selected_packing_ids)->
        where("status_id", 0)->
        get();


        $transport_item_not_all_packing_form_selected = [];
        foreach ($transport_packing as $item) {

            // چک کردن اینکه همه بسته بندی های داخل بسته انتخاب شده باشند.
            foreach ($item->transport_item->transport_packing_list as $packing_transport_item) {
                if (!in_array($packing_transport_item->packing_form_id, $selected_packing_ids)) {
                    $transport_item_not_all_packing_form_selected[$item->transport_item->code()] = 1;
                }
            }

        }

        // چک کردن اینکه بسته بندی حمل و نقل انتخاب شده باشد.
        $transport_item_not_confirm = TransportItem::
        where("product_request_form_id", $product_request_form->id)->
        where("status_id", "!=", 6010002)->
        get();
        if (count($transport_item_not_confirm) > 0) {
            $msg = "";
            foreach ($transport_item_not_confirm as $item) {
                $msg .= $item->code() . ", ";
            }
            $msg = trim($msg, ", ");

            $result_function["message"] = "بسته بندی (های) حمل و نقل " . $msg .
                " ثبت نهایی نشده است، لطفا ابتدا آن(ها) را ثبت نهایی نمایید.";

            return $result_function;
        }
        if (count($transport_item_not_all_packing_form_selected) > 0) {
            $msg = "";
            foreach ($transport_item_not_all_packing_form_selected as $key => $val) {
                $msg .= $key . ", ";
            }
            $msg = trim($msg, ", ");

            $result_function["message"] = " لطفا همه بسته بندی های داخل بسته بندی حمل و نقل " . $msg .
                " را انتخاب نموده و سپس اقدام به تحویل کالا نمایید. ";

            return $result_function;
        }

        if (!isset($session_data["participant_request_form_ids"])) {
            $result_function["message"] = "نشست شما به پایان رسیده است، لطفا یکبار دیگر تلاش کنید.";

            return $result_function;
        }
        $participant_request_form_ids = $session_data["participant_request_form_ids"];

        $selected_packing_list = PackingForm::whereIn("id", $selected_packing_ids)->select("id", "code", "packing_type_id")->get();
        $lowest_level_of_selected_packing_form_ids = PackingForm::LowestLevelOfPackingFormIds($selected_packing_list, null, $count_select);

//        if ( count( $lowest_level_of_selected_packing_form_ids ) >3 ) {
//            $result_function["message"] = "در این وضعیت باید به حالت پردازش برود.";
//            $result_function["form_processing_time"]=round((60+count($lowest_level_of_selected_packing_form_ids)*.05)/60);
//            return $result_function;
//        }

        $selected_packing_type_list = PackingForm::whereIn("id", $selected_packing_ids)->pluck("packing_type_id")->toArray();

        // لیست همه آیتم های درخواست هایی که انتخاب شده است.
        $product_request_form_item_list = ProductRequestFormItem::whereIn("product_request_form_id", $participant_request_form_ids)->
        orderBy("amount_remaining", "desc")->
        get();

        // محاسبه مقدار فرم های درحال تحویل که هنوز تراکنش آنها ثبت نشده است.
        $product_request_form_item_current_delivery =
            DashboardController::getCurrentDeliveryRequest($product_request_form, $participant_request_form_ids);

        $packing_form_final_amount_list_pre = PackingFormItem::
        join("packing_forms", "packing_form_id", "packing_forms.id")->
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("packing_form_id", "product_id", "degree_id")->
        addSelect(DB::raw("packing_forms.packing_type_id,packing_form_id,sub_packing_form_number, product_id ,degree_id,sum(final_amount) as final_amount"))->
        get();

        // ایجاد لیست براساس کالا - درجه
        $packing_form_final_amount_list = [];
        foreach ($packing_form_final_amount_list_pre as $item) {

            // ممکن است کل یک بسته بندی انتخاب نشده باشد، در این صورت باید نسبت بگیریم مثلا اگر 4 بسته از یک پالت 200 تایی انتخاب شود، مقدار انتخاب شده برابر است با
            // final_amount * 4 / 200
            $sub_packing_form_number = $item->sub_packing_form_number;
            if ($sub_packing_form_number == 0 && !isset($item->packing_form->packing_type->first_packing_type_id)) {
                $sub_packing_form_number = 1;// بسته بندی فرعی وجود ندارد و هر بسته دقیقا یک بسته حساب می شود.
            }
            $number_of_selected = isset($count_select[$item->packing_form_id]) ? $count_select[$item->packing_form_id] : $sub_packing_form_number;


            if ($number_of_selected > $sub_packing_form_number || ($number_of_selected == $sub_packing_form_number && $number_of_selected === 0)) {
                $result_function["message"] = "تعداد بسته بندی های فرعی در بسته بندی" . $item->packing_form->packing_type->caption . " نامعتبر است، " . "<br/>" .
                    "تعداد بسته بندی های موجود: " . $sub_packing_form_number . " بسته" . "<br/>" .
                    "تعداد بسته بندی انتخاب شده: " . $number_of_selected . " بسته";

                return $result_function;
            }

            // بررسی اینکه بخشی از بسته بندی انتخاب شده است یا خیر
            $amount_select_of_packing = isset($amount_select[$item->packing_form_id]) ? $amount_select[$item->packing_form_id] : -1;

            $final_amount =
                // اگر مقدار بسته بندی مشخص شده است که همان را به عنوان مقدار نهایی در نظر می گیریم.
                $amount_select_of_packing > 0 ? $amount_select_of_packing :
                    (

                        // اگر بسته بندی به از انبارک خارج می شود، مقدار خروج مشخص است
                    isset($exit_amount_of_packing_form[$item->packing_form_id]) ?
                        $exit_amount_of_packing_form[$item->packing_form_id] :
                        (
                        $item->sub_packing_form_number == 0 ? // اگر بسته بندی، شامل بسته بندی فرعی است؟
                            $item->final_amount :
                            $item->final_amount * $number_of_selected / $item->sub_packing_form_number
                        )
                    );
//            if ( $item->product_id == 1403 ) {
//                echo $item . "<br/>";
//            }

            if (!isset($packing_form_final_amount_list[$item->product_id][$item->degree_id])) {
                $packing_form_final_amount_list[$item->product_id][$item->degree_id] = 0;
                $packing_form_final_amount_remaining[$item->product_id][$item->degree_id] = 0;
            }
            $packing_form_final_amount_list[$item->product_id][$item->degree_id] += $final_amount;;
            $packing_form_final_amount_remaining[$item->product_id][$item->degree_id] += $final_amount;

        }

        //  return $packing_form_final_amount_remaining;

        $prf_item_value = [];// مقدار هر آیتم درخواست که قرار است تحویل شود
        foreach ($product_request_form_item_list as $item) {
            $prf_item_value[$item->id] = 0;
        }

        // به ازای هر ردیف درخواست، بسته بندی های مجاز آن در این آرایه ذخیره می شوند
        $packing_form_packing_type_list = [];


        // به دست آوردن درصد حداکثر مقدار قابل تحویل به ازای هر رسته کالایی
        $allowed_percentage_to_be_higher = Product::join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
        whereIn("products.id", array_keys($packing_form_final_amount_list))->
        groupBy("products.id")->
        pluck("allowed_percentage_to_be_higher", "products.id")->
        toArray();

        // بررسی اینکه کالا در انبارگردانی نباشد
        $warehouse_ids = [];
        $warehouse_ids[] = $product_request_form->warehouse_id;
        if ($product_request_form->applicant_type_id == 40) {
            $warehouse_ids[] = $product_request_form->applicant_id;
        }
        $product_ids_for_check = [];
        foreach ($product_request_form->items as $item) {
            $product_ids_for_check[$item->product_id] = $item->product_id;
        }
        $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

        if (!$result_warehouse["result"]) {
            $result_function["message"] = $result_warehouse["error"];
            return $result_function;
        }

        //اختصاص مقدار به درخواست اصلی - بررسی انطباق درجه
        foreach ($product_request_form->items as $item) {

            $packing_form_packing_type_list[$item->id] =
                Product\ProductRequest\ProductRequestFormPackingType::
                where("product_request_form_item_id", $item->id)->
                pluck("packing_type_id")->toArray();

            // بررسی انطباق بسته بندی های مورد درخواست
            $packing_type_check = $this->exist_packing_type($selected_packing_type_list, $packing_form_packing_type_list[$item->id]);

            if (isset($packing_form_final_amount_list[$item->product_id] [$item->degree_id]) && $packing_type_check) {

                if ($item->amount_remaining == null) {
                    $item->amount_remaining = $packing_form_final_amount_remaining[$item->product_id] [$item->degree_id];
                }
                // برای درخواست اصلی مقدار درحال تحویل را کم می کنیم که بتواند با درخواست های دیگر تحویل بدهد.
                // این بخش برای مشتریان تلاش رنگ اضافه شد که یک 1000 کیلو خروج کشیدند و می خواستند مابقی را روی درخواست دیگر خروج بکشند.
                $amount_remaining_item = $item->amount_remaining
                    - (isset($product_request_form_item_current_delivery[$item->id]) ? $product_request_form_item_current_delivery[$item->id] : 0);

                $value = min($amount_remaining_item, $packing_form_final_amount_remaining[$item->product_id] [$item->degree_id]);

                $prf_item_value[$item->id] += $value;
                $packing_form_final_amount_remaining[$item->product_id][$item->degree_id] -= $value;


            }
        }


        //اختصاص مقدار به درخواست اصلی - بدون بررسی انطباق درجه
        foreach ($product_request_form->items as $item) {

            // بررسی انطباق بسته بندی های مورد درخواست
            $packing_type_check = $this->exist_packing_type($selected_packing_type_list, $packing_form_packing_type_list[$item->id]);

            if (isset($packing_form_final_amount_list[$item->product_id]) && $packing_type_check) {
                // به ازای هر درجه تکرار می شود
                for ($k = 0; $k < count($packing_form_final_amount_list[$item->product_id]); $k++) {

                    // گرفتن درجه ای که بیشترین باقی مانده را دارد
                    $degree_id_max_remaining = $this->index_of_highest_value($packing_form_final_amount_remaining[$item->product_id]);

                    if ($item->amount_remaining == null) {
                        $item->amount_remaining = $packing_form_final_amount_remaining[$item->product_id] [$degree_id_max_remaining];
                    }

                    $value = min($item->amount_remaining, $packing_form_final_amount_remaining[$item->product_id] [$degree_id_max_remaining]);

                    $amount_remaining_item = $item->amount_remaining
                        - (isset($product_request_form_item_current_delivery[$item->id]) ? $product_request_form_item_current_delivery[$item->id] : 0);

                    // برای اینکه بیش از مقدار درخواست به آن اختصاص ندهد
                    if ($prf_item_value[$item->id] + $value > $amount_remaining_item) {
                        $value = $amount_remaining_item - $prf_item_value[$item->id];
                    }
                    if ($value < 0) {
                        $value = 0;
                    }


                    $prf_item_value[$item->id] += $value;
                    $packing_form_final_amount_remaining[$item->product_id][$degree_id_max_remaining] -= $value;

                }

            }

        }


        // اختصاص مقدار به درخواست های فرعی - بررسی انطباق درجه ها
        foreach ($product_request_form_item_list as $item) {

            if ($item->product_request_form_id == $product_request_form->id) {
                continue;
            }

            $packing_form_packing_type_list[$item->id] =
                Product\ProductRequest\ProductRequestFormPackingType::
                where("product_request_form_item_id", $item->id)->
                pluck("packing_type_id")->toArray();

            // بررسی انطباق بسته بندی های مورد درخواست
            $packing_type_check = $this->exist_packing_type($selected_packing_type_list, $packing_form_packing_type_list[$item->id]);

            if ($packing_type_check && isset($packing_form_final_amount_list[$item->product_id][$item->degree_id]) && $packing_form_final_amount_remaining[$item->product_id][$item->degree_id] != 0) {

                $value = min($item->amount_remaining, $packing_form_final_amount_remaining[$item->product_id][$item->degree_id]);

                $prf_item_value[$item->id] += $value;
                $packing_form_final_amount_remaining[$item->product_id][$item->degree_id] -= $value;
            }

        }

        // اختصاص مقدار به درخواست های فرعی - بدون بررسی انطباق درجه ها
        foreach ($product_request_form_item_list as $item) {
            if ($item->product_request_form_id == $product_request_form->id) {
                continue;
            }

            // بررسی انطباق بسته بندی های مورد درخواست
            $packing_type_check = $this->exist_packing_type($selected_packing_type_list, $packing_form_packing_type_list[$item->id]);

            if ($packing_type_check && isset($packing_form_final_amount_list[$item->product_id])) {

                for ($k = 0; $k < count($packing_form_final_amount_list[$item->product_id]); $k++) {

                    $degree_id_max_remaining = $this->index_of_highest_value($packing_form_final_amount_remaining[$item->product_id]);

                    if ($packing_form_final_amount_remaining[$item->product_id][$degree_id_max_remaining] != 0) {

                        $value = min($item->amount_remaining, $packing_form_final_amount_remaining[$item->product_id][$degree_id_max_remaining]);

                        // برای اینکه بیش از مقدار درخواست به آن اختصاص ندهد
                        if ($prf_item_value[$item->id] + $value > $item->amount_remaining) {
                            $value = $item->amount_remaining - $prf_item_value[$item->id];
                        }
                        $prf_item_value[$item->id] += $value;
                        $packing_form_final_amount_remaining[$item->product_id][$degree_id_max_remaining] -= $value;

                    }
                }
            }

        }

        // به دست آوردن مقدار هر سرشکن کردن
        $value_each_product_request_form_item = [];
        foreach ($product_request_form_item_list as $item) {

            if (isset($packing_form_final_amount_remaining[$item->product_id])) {

                // به ازای همه درجه های کالا بررسی شود
                foreach ($packing_form_final_amount_remaining[$item->product_id] as $degree_id => $value) {
                    if (
                        isset($packing_form_final_amount_list[$item->product_id][$degree_id])
                    ) {

                        // صورت : مقدار کل بسته بندی های کالای product_id * مقدار باقی مانده کالای product_id و درجه degree_id
                        // مخرج:


                        $value = (array_sum($packing_form_final_amount_remaining[$item->product_id]) *
                                $packing_form_final_amount_list[$item->product_id][$degree_id]
                            )
                            /
                            (array_sum($packing_form_final_amount_list[$item->product_id]));


                        $value_each_product_request_form_item[$item->product_id][$degree_id][$item->id] = $value;


                    } else {
                        $value_each_product_request_form_item[$item->product_id][$degree_id][$item->id] = 0;
                    }
                }
            }
        }

        // سرشکن کردن مقدار مازاد
        foreach ($product_request_form_item_list as $item) {

            if (isset($packing_form_final_amount_remaining[$item->product_id])) {

                // به ازای همه درجه های کالا بررسی شود
                foreach ($packing_form_final_amount_remaining[$item->product_id] as $degree_id => $value) {
                    if (
                        isset($packing_form_final_amount_list[$item->product_id][$degree_id]) &&
                        isset($prf_item_value[$item->id]) && $prf_item_value[$item->id] != 0
                    ) {

                        $value = $value_each_product_request_form_item[$item->product_id][$degree_id][$item->id];
                        $prf_item_value[$item->id] += $value;
                        $packing_form_final_amount_remaining[$item->product_id][$degree_id] -= $value;

                    }
                }
            }
        }

        // اگر مقدار درخواست یک ردیف null باشد، باید به ازای هر ردیف یک آیتم انتخاب شده باشد
        $max_for_each_product = [];
        foreach ($product_request_form_item_list as $item) {

            if ($item->amount_request > 0 && $item->amount_sent == 0 && $item->amount_remaining == null && $prf_item_value[$item->id] == 0) {
                $result_function["message"] = "برای کالای " . $item->product->caption . " هیچ بسته بندی انتخاب نشده است.";

                return $result_function;
            }

            // محاسبه حداکثر مقدار قابل تحویل به ازای هر کالا
            // چون ممکن است در داشبورد به تفکیک مشتریان، دو کالا انتخاب کنند که مقدار هر کدام 100 می باشد بنابراین max تحویل می شود 200*percent و نباید برای 147 متر خطا بدهیم.

            if ($item->amount_request != null && isset($prf_item_value[$item->id]) && isset($allowed_percentage_to_be_higher[$item->product_id])) {

                $max = $item->amount_request - $item->amount_sent + ($item->amount_request * $allowed_percentage_to_be_higher[$item->product_id] / 100);
                if (!isset($max_for_each_product[$item->product_id])) {
                    $max_for_each_product[$item->product_id] = 0;
                }
                $max_for_each_product[$item->product_id] += $max;
            }

        }


        // بررسی اینکه همه موارد مقدار ماکسیسم را رعایت کرده اند یا خیر
        foreach ($product_request_form_item_list as $item) {

            // محاسبه مقدار فرم های درحال تحویل که هنوز تراکنش آنها ثبت نشده است.
            $sum_form_in_sending = DashboardController::getCurrentDelivery($item);

            if ($item->amount_request != null && isset($prf_item_value[$item->id]) && isset($allowed_percentage_to_be_higher[$item->product_id])) {

                $max = $max_for_each_product[$item->product_id];
                $value = $prf_item_value[$item->id] + $sum_form_in_sending['sum_amount'];
                if ($value > $max) {

                    $product = $item->product;
                    // اگر مجوز تایید شده تا مقدار x داشت، دیگر خطا نمی دهد.
                    $special_license = self::HasSpecialLicenseMax($product_request_form, $product->id, $value);
                    if (!$special_license) {

                        // اگر مجوز تایید شده نداشت، خطا بدهد.
                        $result_function["message"] = "امکان تحویل کالا بیش از مقدار درخواست تا " .
                            $product->goods_kind->allowed_percentage_to_be_higher .
                            " درصد  مجاز است و بیش از این مقدار مجاز نمی باشد." .
                            SpecialLicense::GetLink(1, $product_request_form->id, "ثبت درخواست مجوز ", $product->id, ceil($item->amount_request), ceil($value + $item->amount_sent));

                        return $result_function;
                    }
                }
            }

            if ($item->amount_remaining == null && $item->amount_request == null && round($sum_form_in_sending["sum_amount"], 2) != 0) {
                $result_function["message"] = "امکان تحویل کالا بیش از یک بار برای درخواست هایی که مقدار درخواست آن مشخص نشده است، وجود ندارد. " . "<br/>" . $item->product->caption . "=>" . $sum_form_in_sending["sum_amount"];

                return $result_function;
            }


        }

        // بررسی اینکه حداقل تعداد بسته بندی ها رعایت شده است یا خیر

        // به ازای هر کالا، حداقل تعداد بسته بندی چند است.
        $min_number_of_packing_forms_list = [];
        $max_number_of_packing_forms_list = [];
        $min_or_max_number_of_packing_forms_list = [];
        $lot_number_checked_list = [];
        foreach ($product_request_form_item_list as $item) {

            // محاسبه حداقل تعداد
            if (!isset($min_number_of_packing_forms_list[$item->product_id])) {
                $min_number_of_packing_forms_list[$item->product_id] = 0;
            }
            $min_number_of_packing_forms_list[$item->product_id] += $item->min_number_of_packing_forms ?? 0;

            // محاسبه حداکثر تعداد
            if (!isset($max_number_of_packing_forms_list[$item->product_id])) {
                $max_number_of_packing_forms_list[$item->product_id] = 0;
            }
            $max_number_of_packing_forms_list[$item->product_id] += $item->max_number_of_packing_forms ?? 0;


            // محاسبه نیاز حداقل/حداکثر تعداد
            if (!isset($min_or_max_number_of_packing_forms_list[$item->product_id])) {
                $min_or_max_number_of_packing_forms_list[$item->product_id] = $item->product_id;
            }


// بررسی لات
            if (!isset($lot_number_checked_list[$item->product_id])) {
                $lot_number_checked_list[$item->product_id] = true;
            }
            $lot_number_checked_list[$item->product_id] = $lot_number_checked_list[$item->product_id] && $item->can_deliver_material_with_different_lot;

        }

        // به ازای هر کالا، چند آیتم بسته بندی انتخاب شده است.
        $packing_form_item_product_count_list = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("product_id")->
        pluck("product_id", "product_id")->
        toArray();

        foreach ($min_or_max_number_of_packing_forms_list as $product_id) {

            $count_min = isset($min_number_of_packing_forms_list[$product_id]) ? $min_number_of_packing_forms_list[$product_id] : 0;
            $count_max = isset($max_number_of_packing_forms_list[$product_id]) ? $max_number_of_packing_forms_list[$product_id] : 0;

            if ($count_min > 0 || $count_max > 0) {
//                if ( ! isset( $packing_form_item_product_count_list[ $product_id ] ) ) {
//                    $product                    = Product::find( $product_id );
//                    $result_function["message"] = "کالای " . $product->caption . " در بسته بندی های انتخاب شده وجود ندارد.";
//
//                    return $result_function;
//                }

                $selected_packing_form_count = 0;
                // به ازای هر کالا، چند  بسته بندی انتخاب شده است.
                $list_packing_form_products = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
                whereIn("packing_form_id", $selected_packing_ids)->
                where("product_id", $product_id)->
                select("packing_form_id", "sub_packing_form_number")->
                get();
                foreach ($list_packing_form_products as $packing_form_product) {
                    // اگر بسته بندی دارای بسته های فرعی باشد، که حتما $count_select مقدار دارد، در غیر این صورت 1/تعدد کل بسته های فرعی است یعنی یک بست بندی
                    $selected_packing_form_count += isset($count_select[$packing_form_product->packing_form_id]) ? $count_select[$packing_form_product->packing_form_id] :
                        max(1, $packing_form_product->sub_packing_form_number);
                }


                if ($count_min > 0 && $count_min > $selected_packing_form_count && $selected_packing_form_count != 0) {

                    $product = Product::find($product_id);

                    // لیست بسته بندی های مجاز کالا
                    $list_product_request_packing_type = Product\ProductRequest\ProductRequestFormPackingType::
                    whereIn("product_request_form_id", $participant_request_form_ids)->
                    where("product_id", $product_id)->
                    get();
                    $message = "";
                    $k = 0;
                    foreach ($list_product_request_packing_type as $product_request_packing_type) {
                        if ($k > 5) {
                            $message .= ++$k . "- " . " و ...";
                            break;
                        } else {
                            $message .= ++$k . "- " . $product_request_packing_type->packing_type->caption . "<br/>";
                        }
                    }
                    $result_function["message"] = "در تحویل   " . $product->caption . " (کد کالا:" . $product->code . ")" . " باید حداقل " . ($count_min) . " بسته بندی از نوع بسته بندی های زیر باشد." .
                        "<br/>" . $message;

                    return $result_function;
                }

//                $result_function["message"]="$count_max < $selected_packing_form_count";
//                return $result_function;
                if ($count_max > 0 && $count_max < $selected_packing_form_count && $selected_packing_form_count != 0) {

                    $product = Product::find($product_id);

                    // لیست بسته بندی های مجاز کالا
                    $list_product_request_packing_type = Product\ProductRequest\ProductRequestFormPackingType::
                    whereIn("product_request_form_id", $participant_request_form_ids)->
                    where("product_id", $product_id)->
                    get();
                    $message = "";
                    $k = 0;
                    foreach ($list_product_request_packing_type as $product_request_packing_type) {
                        if ($k > 5) {
                            $message .= ++$k . "- " . " و ...";
                            break;
                        } else {
                            $message .= ++$k . "- " . $product_request_packing_type->packing_type->caption . "<br/>";
                        }
                    }
                    $result_function["message"] = "در تحویل   " . $product->caption . " (کد کالا:" . $product->code . ")" . " باید حداکثر " . ($count_max) . " بسته بندی از نوع بسته بندی های زیر باشد." .
                        "<br/>" . $message;

                    return $result_function;
                }
            }


        }


        // بررسی اینکه می توان در یک درخواست، کالا با لات های مختلف تحویل نمایید.

        //تعداد لات موجود به ازای هر کالا
        $packing_form_item_product_count_list = PackingFormItem::whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("product_id")->
        selectRaw("count(distinct(lot_number_id)) as count, product_id,id")->
        pluck("count", "product_id")->
        toArray();

        foreach ($lot_number_checked_list as $product_id => $lot_number_checked) {
            if (!$lot_number_checked) {
//                if(!isset($packing_form_item_product_count_list[$product_id])  ){
//                    $product                    = Product::find( $product_id );
//                    $result_function["message"] = "کالای " . $product->caption . " در بسته بندی های انتخاب شده وجود ندارد.";
//
//                    return $result_function;
//                }
                if (isset($packing_form_item_product_count_list[$product_id]) && $packing_form_item_product_count_list[$product_id] > 1) {
                    $product = Product::find($product_id);
                    $result_function["message"] = "برای کالای " . $product->caption . " در هر بار تحویل باید بسته بندی هایی با لات های همانند انتخاب شوند.";

                    return $result_function;
                }

            }


        }


        $selected_packing_list_paginate = PackingForm::whereIn("id", $selected_packing_ids)->select("id", "code", "packing_type_id")->paginate(10);

        $product_request_form_ids = [];
        $product_request_form_ids[] = $product_request_form->id;
        // لیست درخوست هایی که همراه هستند را مشخص می کنیم.
        foreach ($product_request_form_item_list as $item) {
            if ($prf_item_value[$item->id] != 0) {
                $product_request_form_ids[] = $item->product_request_form_id;
            } else {
                unset($prf_item_value[$item->id]);
            }
        }
        $product_request_form_ids = array_unique($product_request_form_ids);


        $result_function["result"] = true;
        $result_function["prf_item_value"] = $prf_item_value;
        $result_function["product_request_form_ids"] = $product_request_form_ids;
        $result_function["selected_packing_list"] = $selected_packing_list; // لیست فرم هایی که همراه تحویل می شوند
        $result_function["selected_packing_list_paginate"] = $selected_packing_list_paginate;
        //  $result_function["master_selected_packing_form_ids"]          = $master_selected_packing_form_ids;
        $result_function["product_request_form_item_list"] = $product_request_form_item_list;
        $result_function["selected_packing_ids"] = $selected_packing_ids;
        $result_function["participant_request_form_ids"] = $participant_request_form_ids; // لیست فرم هایی که پیشنهاد شده همراه تحویل شوند.
        $result_function["packing_form_final_amount_list_pre"] = $packing_form_final_amount_list_pre;
        $result_function["count_select"] = $count_select;
        $result_function["amount_select"] = $amount_select;
        $result_function["need_new_packing"] = $need_new_packing;
        $result_function["lowest_level_of_selected_packing_form_ids"] = $lowest_level_of_selected_packing_form_ids;
        $result_function["exit_amount_of_packing_form"] = $exit_amount_of_packing_form;

        return $result_function;

    }

    public function submit_select_new_packing_type(Request $request, ProductRequestForm $product_request_form, $page = 1)
    {


        if (!$request->data) {
            return back()->withErrors("لطفا اطلاعات را به درستی وارد نمایید.");
        }

        $need_new_packing = $request->data["need_new_packing"];
        foreach ($need_new_packing as $packing_form_id => $value) {
            if ($value > 0) {
                $key = "select_packing_form_" . $packing_form_id;
                if (isset($request->$key)) {
                    $need_new_packing[$packing_form_id] = $request->$key;
                } else {
                    return back()->withErrors("لطفا برای هر ردیف اطلاعات را به صورت کامل تکمیل نمایید");
                }
            }


        }


        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form);

        $session_data["need_new_packing"] = $need_new_packing;
        Product\ProductRequest\ProductRequestFormSessionData::
        setData($product_request_form, $session_data);

        return redirect()->route($this->route_path . "checkout", [$product_request_form, $page]);
    }

    public function checkout(ProductRequestForm $product_request_form, $page = 1, $dashboard_type = "")
    {
        ini_set('memory_limit', '200M');
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $print_number_option = Option::get("print_number", $worker->default_print_number);

        // چک کردن دسترسی تحویل کالا
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید.");
        }

        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, true, $dashboard_type == "customer");


        $result = $this->computeDeliveryToRequest($product_request_form, false, $dashboard_type == "customer", $session_data);

        if (!$result["result"]) {
//            if(isset($result["form_processing_time"])){
//
//                $time=$result["form_processing_time"];
//                return view($this->view_path."form_processing",compact("time","product_request_form","page","print_number_option"));
//            }
            if (isset($result["repeat_request"])) {
                return redirect()->route($this->dashbaord_path . "index")->withErrors($result["repeat_request"]);
            }
            if ($dashboard_type == "customer") {
                return redirect()->route("wh.out.customer.view", [$product_request_form->order->customer_id, "page" => $page])->withErrors($result["message"]);

            } else {
                return redirect()->route($this->dashbaord_path . "view", [$product_request_form, $page])->withErrors($result["message"]);
            }
        }


        // در صورتی که نیاز است تا توع بسته بندی های جدید دریافت شود و قبلا دریاف نشده به لینک select_new_packing_type ریدایرکت می شود.
        if ((count($result["count_select"]) > 0 || count($result["amount_select"]) > 0) && count($result["need_new_packing"]) == 0) {

            $count_select = $result["count_select"];
            $amount_select = $result["amount_select"];
            $selected_packing_list = $result["selected_packing_list"];
            $packing_type_list_option = [];
            $is_any_packing_form_must_change = false;
            foreach ($selected_packing_list as $item) {
                if (isset($count_select[$item->id]) || isset($amount_select[$item->id])) {
                    $is_any_packing_form_must_change = true;
                    $product_ids = PackingFormItem::
                    where("packing_form_id", $item->id)->
                    distinct("product_id")->
                    pluck("product_id")->
                    toArray();


                    // گرفتن لیست بسته بندی های مجاز با دو شرط : 1- کالاهای داخل بسته بندی 2- اینکه اولین لایه بسته بندی آن با بسته بندی فرعی یکی باشد.
                    $allowed_packing_type_list = PackingType::join("packing_type_product", "packing_type_id", "packing_types.id")->
                    whereIn("product_id", $product_ids)->
                    where(function ($query) use ($item){
                        return $query->
                        where("first_packing_type_id", $item->packing_type->first_packing_type_id ?? 0)->
                        orWhereNull("first_packing_type_id");
                    })->
                    select("packing_types.*")->
                    get();

                    $packing_type_list_option[$item->id] = [];
                    $packing_type_list_option[$item->id][] = [
                        "id" => "",
                        "text" => "لطفا یک بسته بندی انتخاب کنید",
                        "value" => ""
                    ];
                    foreach ($allowed_packing_type_list as $packing_type) {
                        $packing_type_list_option[$item->id][] = [
                            "id" => $packing_type->id,
                            "text" => $packing_type->fullCaption(),
                            "value" => $packing_type->id
                        ];
                    }
                }
            }

            if ($is_any_packing_form_must_change) {
                // ممکن است کاربر یک بسته بندی را انتخاب کند و بخشی از آن را انتخاب کرده باشد ولی بعد آن را حذف نمایید، و بسته بندی دیگری را انتخاب نمایید،
                // اگر این اتقاق برای همه بسته بندی ها بفتد نباید به صفحه باز شدن بسته بندی برویم.
                return view($this->view_path . "select_new_packing_type", compact("product_request_form", "selected_packing_list", "count_select","amount_select", "page", "packing_type_list_option"));

            } else {
                $count_select = [];
                $result["count_select"] = $count_select;
            }


        }

        $prf_item_value = $result["prf_item_value"];
        $product_request_form_item_list = $result["product_request_form_item_list"];
        $count_select = $result["count_select"]; // انتخاب تعداد بسته بندی فرعی
        $amount_select = $result["amount_select"]; // انتخاب بخشی از بسته بندی، ممکن است که واحد فرعی داشته باشد یا نداشته باشد.
        $need_new_packing = $result["need_new_packing"];
        $selected_packing_list = $result["selected_packing_list_paginate"];
        foreach ($need_new_packing as $packing_form_id => $new_packing_type_id) {
            $need_new_packing[$packing_form_id] = PackingType::find($new_packing_type_id);
        }


        return view($this->view_path . "checkout", compact("dashboard_type", "product_request_form", "prf_item_value", "selected_packing_list", "product_request_form_item_list", "page", "count_select", "amount_select", "need_new_packing", "print_number_option", "dashboard_type"));

    }

    public function confirm(Request $request, ProductRequestForm $product_request_form, $dashboard_type = "")
    {
        ini_set('memory_limit', '200M');
        $product_request_form_before_status_id = $product_request_form->status_id;
//        if ($product_request_form->id == 1612) {
//            return $result = $this->computeDeliveryToRequest($product_request_form, true, $dashboard_type == "customer");
//
//        }
        $result = $this->computeDeliveryToRequest($product_request_form, true, $dashboard_type == "customer");
        if (!$result["result"]) {

            ProductRequestForm::where("id", $product_request_form->id)->
            update(["status_id" => $product_request_form_before_status_id]);

            if (isset($result["repeat_request"])) {
                return redirect()->route($this->dashbaord_path . "index")->withErrors($result["repeat_request"]);
            }
            return back()->withErrors($result["message"]);
        }


        $prf_item_value = $result["prf_item_value"];

        $product_request_form_item_list = $result["product_request_form_item_list"];


        $selected_packing_ids = $result["selected_packing_ids"];
        $lowest_level_of_selected_packing_form_ids = $result["lowest_level_of_selected_packing_form_ids"];

        $participant_request_form_ids = $result["product_request_form_ids"]; // لیست فرم های همراه که در یک برگ خروج هستند
        $count_select = $result["count_select"];
        $amount_select = $result["amount_select"];
        $need_new_packing = $result["need_new_packing"];


        // این قطعه کد برای زمانی است که تحویل به صورت کامل انجام نمی شود و لازم است تا بررسی صورت بگیرد.
        if ($product_request_form->id == -1) {
            //انتخاب بسته بندی های پایین ترین سطح
            $selected_packing_form_item_list_lower = PackingFormItem::
            whereIn("packing_form_id", $lowest_level_of_selected_packing_form_ids)->
            get();;
            foreach ($product_request_form_item_list as $product_request_form_item) {

                if (isset($prf_item_value[$product_request_form_item->id])) {

                    foreach ($selected_packing_form_item_list_lower as $packing_form_item) {

                        if ($packing_form_item->product_id == $product_request_form_item->product_id && $prf_item_value[$product_request_form_item->id] > 0 && $packing_form_item->final_amount > 0) {

                            $amount = min($prf_item_value[$product_request_form_item->id], $packing_form_item->final_amount);


                            $prf_item_value[$product_request_form_item->id] -= $amount;
                            $packing_form_item->final_amount -= $amount;

                            if ($packing_form_item->final_amount + 0 > .0000001) {
                                echo "error in packing form: " . $packing_form_item->code . "<br/>";
                            }
                            echo "prf=" . $prf_item_value[$product_request_form_item->id] . " amount=" . $amount . ", " . $packing_form_item->code . "=>" . $packing_form_item->final_amount . "<br/>";
                        }
                    }
                }


            }
            echo "<br/>";
            ProductRequestForm::where("id", $product_request_form->id)->
            update(["status_id" => $product_request_form_before_status_id]);

            return $prf_item_value;
            // return $result;
        }
        // بررسی مجوز برای همه درخواست هایی انتخاب شده
        foreach ($product_request_form_item_list as $item) {
            if ($item->product_request_form->order && $item->product_request_form->order->loading_status_id == 460000100 && $item->product_request_form->order->code() == $item->product_request_form->getReferenceNumber()) {

                ProductRequestForm::where("id", $product_request_form->id)->
                update(["status_id" => $product_request_form_before_status_id]);

                return back()->withErrors(" برای سفارش شماره " . $item->product_request_form->order->code() . " مجوز بارگیری صادر نشده است، لطفا با واحد مالی تماس بگیرید.");
            }
        }


        // درصورتی که انبار مبدا از نوع انبارک هست، برای تایید نهایی کار متفاوتی باید انجام شود بنابراین ادامه کار را با الگوریتم جدید ادامه می دهیم.
        if ($product_request_form->warehouse->warehouse_type_id > 1) {
            return $this->confirm_warehouse_type($request, $product_request_form, $result, $product_request_form_before_status_id);
        }


// در صورتی که بسته بندی باز شده است، لازم است تا شناسه بسته بندی اصلی هر بسته بروز شود
        if (count($need_new_packing) > 0) {

            $packing_form_where_cut_result = PackingForm::MasterPackingCutIntoPieces($selected_packing_ids, $count_select,$amount_select, $need_new_packing);
            if ($packing_form_where_cut_result["packing_form_where_must_add_to_form"]) {
                foreach ($packing_form_where_cut_result["packing_form_where_must_add_to_form"] as $new_packing_form) {
                    $lowest_level_of_selected_packing_form_ids[] = $new_packing_form->id;
                }

            }

            foreach ($need_new_packing as $packing_form_id_where_cut_in_to_pieces => $val) {
                //چون این بسته بندی تغییر یافته است.
                if (($key = array_search($packing_form_id_where_cut_in_to_pieces, $lowest_level_of_selected_packing_form_ids)) !== false) {
                    unset($lowest_level_of_selected_packing_form_ids[$key]);
                }

            }

        }

        //انتخاب بسته بندی های پایین ترین سطح
        $selected_packing_form_item_list_lower = PackingFormItem::
        whereIn("packing_form_id", $lowest_level_of_selected_packing_form_ids)->
        get();;

        PackingForm::whereIn("id", $lowest_level_of_selected_packing_form_ids)->update(["warehouse_status_id" => 4203]); // در مسیر تحویل به درخواست دهنده

        // ثبت فرم خروج از انبار
        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "trans_kind" => $product_request_form->getTranKind(),
            "ic" => $product_request_form->getIC(),
            "status_id" => $product_request_form->getFirstFormStatus()
        ]);

        $form->getCode("DCEF");// Warehouse Exit Form

        $save_final_amount_of_packing = []; // ذخیره مقدار اولیه بسته بندی ها برای محاسبه مقدار فرعی

        foreach ($product_request_form_item_list as $product_request_form_item) {

            if (isset($prf_item_value[$product_request_form_item->id])) {
                foreach ($selected_packing_form_item_list_lower as $packing_form_item) {

                    if ($packing_form_item->product_id == $product_request_form_item->product_id && $prf_item_value[$product_request_form_item->id] > 0 && $packing_form_item->final_amount > 0) {

                        $amount = min($prf_item_value[$product_request_form_item->id], $packing_form_item->final_amount);

                        if (!isset($save_final_amount_of_packing[$packing_form_item->id])) {
                            $save_final_amount_of_packing[$packing_form_item->id] = $packing_form_item->final_amount;
                        }

                        $sub_amount = round($packing_form_item->sub_amount * ($amount / $save_final_amount_of_packing[$packing_form_item->id]), 4);


                        $form_item = FormItem::create([
                            "form_id" => $form->id,
                            "product_id" => $packing_form_item->product_id,
                            "amount" => $amount,
                            "sub_amount" => $sub_amount,
                            "carrier_id" => $packing_form_item->packing_form->carrier_id,
                            "degree_id" => $packing_form_item->degree_id,
                            "lot_number_id" => $packing_form_item->lot_number_id,
                            "packing_type_id" => $packing_form_item->packing_form->packing_type_id,
                            "packing_form_item_id" => $packing_form_item->id,
                            "io_line_code" => $product_request_form_item->input_line_code ?? 1,
                            "product_request_form_item_id" => $product_request_form_item->id,
                            "description" => $product_request_form_item->product_request_form->getFormItemDescription($packing_form_item, $product_request_form_item, $form)
                        ]);

                        $prf_item_value[$product_request_form_item->id] -= $amount;
                        $packing_form_item->final_amount -= $amount;

                        $packing_form_item->final_amount = $packing_form_item->final_amount;
                    }
                }
            }


        }

        // چون ممکن است، بسته بندی های بالایی تغییر کرده باشند، دوباره لیست را بروز می کنیم.
        $lowest_level_of_selected_packing_form_list = PackingForm::whereIn("id", $lowest_level_of_selected_packing_form_ids)->get();
        $master_selected_packing_form_ids = PackingForm::MasterPackingFormIds($lowest_level_of_selected_packing_form_list);
        $master_is_null_packing_forms = PackingForm::MasterIsNullPackingForms($lowest_level_of_selected_packing_form_list);


        // تغییر وضعیت انبار بسته بندی های اصلی و بسته بندی هایی که بسته بندی فرعی دارند.
        PackingForm::whereIn("id", $master_selected_packing_form_ids)->update(["warehouse_status_id" => 4203]); // در مسیر تحویل به درخواست دهنده
        foreach ($master_is_null_packing_forms as $packing_form_master_is_null) {
            $packing_form_master_is_null->warehouse_status_id = 4203;
            $packing_form_master_is_null->save();
        }

        $product_request_form_list = ProductRequestForm::whereIn("id", $participant_request_form_ids)->get();

        foreach ($product_request_form_list as $prf) {
            // ثبت فرم انبار برای فرم درخواست کالا
            ProductRequestFormForm::create([
                "product_request_form_id" => $prf->id,
                "form_id" => $form->id
            ]);
            // آپدیت اطلاعات فرم درخواست کالا
            $prf->status_id = ProductRequestForm::$perfix_status_code . "004";
            $prf->save();
            event(new ProductRequestFormLogEvent($prf, "", $form->id, 7005004));

            // بروز رسانی وضعیت های درخواست دهنده
            $prf->updateApplicantStatus(5310108, ["form" => $form]);// تحویل مواد اولیه
        }


// اپدیت اطلاعات فرم انبار
        $form->warehouse_id = $product_request_form->warehouse_id;
        $form->save();
        event(new FormLogEvent($form));

        ProductRequestFormSessionData::removeData($product_request_form, $participant_request_form_ids);

        $packing_where_final_amount_not_zero = [];
        $message_exception = "";
        foreach ($selected_packing_form_item_list_lower as $item) {
            if (abs(round($item->final_amount, 10)) > .0000001) {
                $packing_where_final_amount_not_zero[] = $item;
                $message_exception .= $item->code . "->" . $item->final_amount;
            }
        }

        // اگر بسته ها به صورت کامل تحویل نمی شوند، فرم را عدم تایید می شود.
        if (count($packing_where_final_amount_not_zero) > 0) {

            $product_request_form->rejectRequest($form);

            SMSMessage::ExceptionError(" مقدار تحویل کالا در برگ خروج صفر نشده است." . $form->code . ": " . $message_exception);


            return redirect()->route($this->dashbaord_path . "index")->withErrors("ثبت برگ خروج " . $form->code . " با خطا مواجه شده لطفا به قید فوریت با واحد پشتیبانی تماس بگیرید." . "<br/>" . $message_exception);
        }


        // پرینت فرم خروج از انبار
        if ($request->print != "" && $request->print != "back") {
            $efc = new  ExitFormController();
            $id = trim($request->print, "print_");
            $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($id);

            // بعد از اینکه وضعیت پردازش را قرار دادیم، وضعیت فرم هنوز در حال پردازش می شد، ،
            // وضعیت فرم را تغییر دادیم تا بتواند پرینت بگیرد و دسترسی داشته باشد.
            $product_request_form->status_id = ProductRequestForm::$perfix_status_code . "004";
            $efc->print($product_request_form, $form, $packing_type_label_printing_type, $request->print_number, $request->print_type);
        }

        if (isset($packing_form_where_cut_result["packing_form_where_cut_in_to_pieces"]) &&
            count($packing_form_where_cut_result["packing_form_where_cut_in_to_pieces"]) > 0) {
            session([
                "packing_form_where_cut_in_to_pieces" => $packing_form_where_cut_result["packing_form_where_cut_in_to_pieces"],
                "need_new_packing" => $need_new_packing
            ]);

            return redirect()->route($this->route_path . "print_new_packing", $product_request_form)->with(["success" => "فرم تحویل کالا با موفقیت ثبت گردید."]);
        }


        if ($dashboard_type == "customer") {
            return redirect()->route("wh.out.customer.index")->with(["success" => "برگ خروج کالا با موفقیت ثبت گردید." . "<br/>" . "شماره برگ خروج از انبار : " . $form->code]);

        } else {
            return redirect()->route($this->dashbaord_path . "index")->with(["success" => "برگ خروج کالا با موفقیت ثبت گردید." . "<br/>" . "شماره برگ خروج از انبار : " . $form->code]);
        }
    }

    public function confirm_warehouse_type(Request $request, ProductRequestForm $product_request_form, $result, $product_request_form_before_status_id)
    {


        $selected_packing_ids = $result["selected_packing_ids"];

        $prf_item_value = $result["prf_item_value"];
        $product_request_form_item_list = $result["product_request_form_item_list"];
        $participant_request_form_ids = $result["participant_request_form_ids"];


        $count_select = $result["count_select"];
        $exit_amount_of_packing_form = $result["exit_amount_of_packing_form"];
        $need_new_packing = $result["need_new_packing"];

        $selected_packing_forms = PackingForm::whereIn("id", $selected_packing_ids)->get()->keyBy("id");
        $entry_packing_forms = [];
        $entry_packing_form_ids = [];
        $entry_packing_form_item_list = [];
        $exit_amount_of_packing_form_new = []; // مقدار باقی مانده تحویل هر بسته بندی جدید


        // ایجاد فرم ورود و ایجاد بسته بندی ها
        // ثبت فرم خروج از انبارک جهت تغییر بسته بندی
        $form_output_change_warehouse = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "trans_kind" => 201, // خروج تغییر بسته بندی
            "ic" => "",
            "status_id" => 500000200, //تایید شده
            "form_type_id" => 0,
            "warehouse_id" => $product_request_form->warehouse_id
        ]);
        $form_output_change_warehouse->getCode("DCEF");// Warehouse Exit Form

        // ثبت فرم ورود به انبارک جهت تغییر بسته بندی
        $form_input_change_warehouse = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "trans_kind" => 101, // ورود تغییر بسته بندی
            "ic" => "",
            "status_id" => 500000200, //تایید شده
            "form_type_id" => 304,
            "warehouse_id" => $product_request_form->warehouse_id
        ]);
        $form_input_change_warehouse->getCode("DCRF");//


        foreach ($exit_amount_of_packing_form as $packing_form_id => $amount) {

            $packing_type_id = isset($need_new_packing[$packing_form_id]) ?
                $need_new_packing[$packing_form_id] :
                $selected_packing_forms[$packing_form_id]->packing_type_id;

            $sub_packing_form_number = isset($count_select[$packing_form_id]) ?
                $count_select[$packing_form_id] :
                $selected_packing_forms[$packing_form_id]->sub_packing_form_number;

            $first_packing_form_item = $selected_packing_forms[$packing_form_id]->items()->first();

            // ایجاد فرم بسته بندی
            $entry_packing_form = PackingForm::create([
                "packing_type_id" => $packing_type_id,
                "carrier_id" => null,
                "status_id" => 7007003, // تحویل شده به انبار
                "sub_packing_form_number" => $sub_packing_form_number
            ]);

            $entry_packing_form->getCode();
            $entry_packing_forms[] = $entry_packing_form;
            $entry_packing_form_ids[] = $entry_packing_form->id;
            //ایجاد بسته بندی
            event(new PackingLogEvent($entry_packing_form, 7007001));
            event(new PackingLogEvent($entry_packing_form, 7007006, null, "", $form_input_change_warehouse));


            $new_packing_form_item = PackingFormItem::create([
                "packing_form_id" => $entry_packing_form->id,
                "product_id" => $first_packing_form_item->product_id,
                "lot_number_id" => $first_packing_form_item->lot_number_id,
                "degree_id" => $first_packing_form_item->degree_id,
                "amount" => $exit_amount_of_packing_form[$packing_form_id],
                "amount_after_control" => $exit_amount_of_packing_form[$packing_form_id],
                "final_amount" => $exit_amount_of_packing_form[$packing_form_id],
                "sub_amount" => 0,
                "init_sub_amount" => 0,
                "status_id" => 7007003, // تحویل شده به انبار
                "band_code" => $first_packing_form_item->band_code
            ]);
            $new_packing_form_item->getCode($first_packing_form_item->band_code, 1);
            $entry_packing_form_item_list[] = $new_packing_form_item;
            $exit_amount_of_packing_form_new[$entry_packing_form->id] = $new_packing_form_item->final_amount;

            // ورود بسته بندی جدید به انبارک
            $form_item = FormItem::create([
                "form_id" => $form_input_change_warehouse->id,
                "product_id" => $new_packing_form_item->product_id,
                "amount" => $new_packing_form_item->final_amount,
                "sub_amount" => $new_packing_form_item->sub_amount,
                "carrier_id" => null,
                "degree_id" => $new_packing_form_item->degree_id,
                "lot_number_id" => $new_packing_form_item->lot_number_id,
                "packing_type_id" => $entry_packing_form->packing_type_id,
                "packing_form_item_id" => $new_packing_form_item->id,
                "io_line_code" => null,
                "product_request_form_item_id" => null,
                "description" => " ورود تغییر بسته بندی داخل انبارک" .
                    "با کد بسته بندی " . ($entry_packing_form->getCode()) .
                    " و " . " فرم ورود " . ($form_input_change_warehouse->code)
            ]);


            // خروج به اندازه بسته بندی جدید از بسته بندی های داخل انبار
            $master_of_packing_form = PackingForm::find($packing_form_id);
            if ($master_of_packing_form->packing_form_contents()->count() > 0) {
                $exit_packing_form = $master_of_packing_form->packing_form_contents()->first();
            } else {
                $exit_packing_form = $master_of_packing_form;
            }

            $exit_packing_form_item = $exit_packing_form->items()->first();

            $form_item = FormItem::create([
                "form_id" => $form_output_change_warehouse->id,
                "product_id" => $new_packing_form_item->product_id,
                "amount" => $new_packing_form_item->final_amount,
                "sub_amount" => $new_packing_form_item->sub_amount,
                "carrier_id" => null,
                "degree_id" => $new_packing_form_item->degree_id,
                "lot_number_id" => $new_packing_form_item->lot_number_id,
                "packing_type_id" => $exit_packing_form->packing_type_id,
                "packing_form_item_id" => $exit_packing_form_item->id,
                "io_line_code" => null,
                "product_request_form_item_id" => null,
                "description" => " خروج تغییر بسته بندی داخل انبار" .
                    "با کد بسته بندی " . ($exit_packing_form->getCode()) .
                    " و " . " فرم ورود " . ($form_output_change_warehouse->code)
            ]);

            //مقدار بسته بندی اصلی را ویرایش می کنیم.
            $exit_packing_form_item->final_amount -= $new_packing_form_item->final_amount;
            $exit_packing_form_item->sub_amount -= $new_packing_form_item->sub_amount;
            $exit_packing_form_item->save();

            if ($master_of_packing_form->id == $exit_packing_form->id) {

                //تعداد بسته بندی اصلی را ویرایش می کنیم.
                $exit_packing_form->sub_packing_form_number -= $entry_packing_form->sub_packing_form_number;
                $exit_packing_form->save();
            } else { // بسته شامل بسته بندی های مجازی ایجاد شده است.
                //تعداد بسته بندی اصلی را ویرایش می کنیم.
                $master_of_packing_form->sub_packing_form_number -= $entry_packing_form->sub_packing_form_number;
                $master_of_packing_form->save();

                // مقدار آیتم بسته بندی اصلی را هم کم می کنیم.
                $master_of_packing_form_item = $master_of_packing_form->items()->first();
                $master_of_packing_form_item->final_amount -= $new_packing_form_item->final_amount;
                $master_of_packing_form_item->sub_amount -= $new_packing_form_item->sub_amount;
                $master_of_packing_form_item->save();

            }

            event(new PackingLogEvent($master_of_packing_form, 7007008, null, $entry_packing_form->code));
            event(new PackingLogEvent($master_of_packing_form, 7007010, null, $new_packing_form_item->final_amount . " " .
                    $new_packing_form_item->product->unit->caption . " - " .
                    $master_of_packing_form->sub_packing_form_number . " بسته بندی فرعی ",
                    $form_output_change_warehouse)
            );

            // بروز رسانی وزن بسته بندی تغییر یافته
            $result_weight = PackingForm::UpdateWeight($master_of_packing_form);
            if ($result_weight["result"]) {
                $master_of_packing_form->weight = $result_weight["weight"];
                $master_of_packing_form->gross_weight = $result_weight["gross_weight"];
                $master_of_packing_form->save();
            }

            // بسته بندی که از روی آن تغییر بسته بندی انجام شده
            $entry_packing_form->packing_form_parent_id = $master_of_packing_form->id;

            // بروز رسانی وزن بسته بندی جدید
            $result_weight = PackingForm::UpdateWeight($entry_packing_form);
            if ($result_weight["result"]) {
                $entry_packing_form->weight = $result_weight["weight"];
                $entry_packing_form->gross_weight = $result_weight["gross_weight"];
            }

            $entry_packing_form->save();


        }


        //         ثبت برگ خروج بخشی از بسته بندی ها از انبارک به انبارک
        event(new PutInWarehouseEvent($form_output_change_warehouse, null, null, false));

        // ثبت برگ ورود بسته بندی های جدید به انبارک
        event(new PutInWarehouseEvent($form_input_change_warehouse));


        // وضعیت بسته بندی های داخل انبارک را تغییر نمی دهیم.

        // ثبت برگ خروج از انبار
        $form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "trans_kind" => 11, // خروج متفرقه
            "ic" => "",
            "status_id" => $product_request_form->getFirstFormStatus()
        ]);

        $form->getCode("DCEF");// Warehouse Exit Form


        foreach ($product_request_form_item_list as $product_request_form_item) {

            if (isset($prf_item_value[$product_request_form_item->id])) {
                foreach ($entry_packing_form_item_list as $packing_form_item) {

                    if (
                        $packing_form_item->product_id == $product_request_form_item->product_id &&
                        $prf_item_value[$product_request_form_item->id] > 0
                        && $packing_form_item->final_amount > 0
                    ) {

                        $amount = min(
                            $prf_item_value[$product_request_form_item->id],
                            $exit_amount_of_packing_form_new[$packing_form_item->packing_form_id]
                        );

                        $sub_amount = round($packing_form_item->sub_amount * ($amount / $packing_form_item->final_amount), 4);

                        $sub_amount = $sub_amount < 0 ? 0 : $sub_amount;

                        $form_item = FormItem::create([
                            "form_id" => $form->id,
                            "product_id" => $packing_form_item->product_id,
                            "amount" => $amount,
                            "sub_amount" => $sub_amount,
                            "carrier_id" => $packing_form_item->packing_form->carrier_id,
                            "degree_id" => $packing_form_item->degree_id,
                            "lot_number_id" => $packing_form_item->lot_number_id,
                            "packing_type_id" => $packing_form_item->packing_form->packing_type_id,
                            "packing_form_item_id" => $packing_form_item->id,
                            "io_line_code" => $product_request_form_item->input_line_code ?? 1,
                            "product_request_form_item_id" => $product_request_form_item->id,
                            "description" => $product_request_form_item->product_request_form->getFormItemDescription($packing_form_item, $product_request_form_item, $form)
                        ]);

                        $prf_item_value[$product_request_form_item->id] -= $amount;
                        $packing_form_item->final_amount -= $amount;

                        $packing_form_item->final_amount = $packing_form_item->final_amount;
                    }
                }
            }


        }


        $product_request_form_list = ProductRequestForm::whereIn("id", $participant_request_form_ids)->get();

        foreach ($product_request_form_list as $prf) {
            // ثبت فرم انبار برای فرم درخواست کالا
            ProductRequestFormForm::create([
                "product_request_form_id" => $prf->id,
                "form_id" => $form->id
            ]);
            // آپدیت اطلاعات فرم درخواست کالا
            $prf->status_id = ProductRequestForm::$perfix_status_code . "004";
            $prf->save();
            event(new ProductRequestFormLogEvent($prf, "", $form->id, 7005004));

            // بروز رسانی وضعیت های درخواست دهنده
            $prf->updateApplicantStatus(5310108, ["form" => $form]);// تحویل مواد اولیه
        }


// اپدیت اطلاعات فرم انبار
        $form->warehouse_id = $product_request_form->warehouse_id;
        $form->save();
        event(new FormLogEvent($form));

        // تغییر وضعیت انبار بسته بندی های اصلی و بسته بندی هایی که بسته بندی فرعی دارند.
        PackingForm::whereIn("id", $entry_packing_form_ids)->update(["warehouse_status_id" => 4203]); // در مسیر تحویل به درخواست دهنده

        ProductRequestFormSessionData::removeData($product_request_form);

        // پرینت فرم خروج از انبار
        if ($request->print != "" && $request->print != "back") {
            $efc = new  ExitFormController();
            $id = trim($request->print, "print_");
            $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($id);
            $efc->print($product_request_form, $form, $packing_type_label_printing_type, $request->print_number, $request->print_type);
        }


        session([
            "packing_form_where_cut_in_to_pieces" => $entry_packing_forms,
            "need_new_packing" => [-1]
        ]);

        ProductRequestForm::where("id", $product_request_form->id)->
        update(["status_id" => $product_request_form_before_status_id]);


        return redirect()->route($this->route_path . "print_new_packing", $product_request_form)->with(["success" => "برگ خروج کالا با موفقیت ثبت گردید." . "<br/>" . "شماره برگ خروج از انبار : " . $form->code]);


    }

    public function print_new_packing(ProductRequestForm $product_request_form, $page = 1)
    {
        $packing_list = session("packing_form_where_cut_in_to_pieces");
        $need_new_packing = session("need_new_packing");

        if (isset($packing_list)) {
            $need_new_packing_list = PackingForm::whereIn("id", array_keys($need_new_packing))->get();

            return view($this->view_path . "print_new_packing", compact("product_request_form", "packing_list", "page", "need_new_packing_list"));
        } else {
            return redirect()->route($this->dashbaord_path . "index")->withErrors("بسته بندی جهت پرینت وجود ندارد");
        }
    }

    public function submit_print_new_packing(Request $request, $page = 1)
    {
        $data = $request->data;
        $worker = Worker::find(Auth::id());
        if ($data && count($data) > 0) {

            $packing_forms = PackingForm::whereIn("id", array_keys($data["packing_form"]))->get();
            foreach ($packing_forms as $packing_form_print) {
                PrintQRController::direct_print($packing_form_print, $worker);
            }
        }
        session([
            "packing_form_where_cut_in_to_pieces" => null
        ]);

        return redirect()->route($this->dashbaord_path . "index")->with(["success" => "بسته بندی های مورد نظر با موفقیت پرینت شدند"]);

    }

    public function warehouse_delivery_changeSelectedPacking(Request $request)
    {

        $product_request_form_id = $request->product_request_form_id + 0;
        $packing_form_id = $request->packing_form_id + 0;
        $packing_form_code = $request->packing_form_code;
        $all_checked = $request->all_checked;
        $all_page_checked = $request->all_page_checked;
        $count_select_packing_form = $request->count_select_packing_form + 0;
        $amount_select_packing_form = $request->amount_select_packing_form + 0;
        $checked = $request->checked;
        $product_id = $request->product_id;
        $dashboard_type = $request->dashboard_type;
        $only_add = isset($request->only_add) ? true : false;

        $product_request_form = ProductRequestForm::find($product_request_form_id);
        if (!$product_request_form) {
            return "درخواست کالا از انبار در سیستم وجود ندارد، لطفا با پشتیبانی تماس بگیرید.";
        }
        if (!in_array($product_request_form->status_id, $this->allowed_status)) {
            return "این درخواست قبلا تحویل شده و یا امکان تحویل آن با توجه به وضعیت درخواست وجود ندارد.";
        }


        // لیست بسته هایی که در انبار موجود است و کالای درخواست شده در آن قرار دارد.
        $packing_list = DeliveryController::getPackingInWarehouse($product_request_form, null, null, "", null, $dashboard_type);

        if (count($packing_list) == 0) {
            return "هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد.";
        }

        // تیک همه با هم
        if (isset($all_checked)) {
            $packing_show_in_page_list = $request->packing_show_in_page_list; //لیسیت بسته بندی هایی که داحل یک صفحه هستند.

            foreach ($packing_show_in_page_list as $packing_form_id_show) {

                $this->add_or_remove_package($product_request_form, $packing_list, $packing_form_id_show, $all_checked, false, false, false, $dashboard_type);
            }

            return 1;
        }
        // تیک همه صفحات با هم
        if (isset($all_page_checked)) {

            $packing_show_in_page_list = DeliveryController::getPackingInWarehouse($product_request_form, $product_id, null, "", null, $dashboard_type);

            foreach ($packing_show_in_page_list as $packing_form) {

                $this->add_or_remove_package($product_request_form, $packing_list, $packing_form->id, $all_page_checked, false, false, false, $dashboard_type);
            }


            return count($packing_show_in_page_list);
        }

        if ($packing_form_code) {
            $packing_form = PackingForm::where("code", "DCPK/" . $packing_form_code)->first();
            $packing_form_id = $packing_form->id ?? 0;
        }

        // اگر تعداد بسته بندی ارسال شده بود آن را ست می کنیم.
        if ($count_select_packing_form || $amount_select_packing_form) {


            $session_data = Product\ProductRequest\ProductRequestFormSessionData::
            getData($product_request_form, false, $dashboard_type == "customer");
            $selected_packing_ids = $session_data["selected_packing_ids"];
            if (!$selected_packing_ids) {
                $selected_packing_ids[] = -1;
            }

            $packing_form = PackingForm::find($packing_form_id);
        }

        //تعداد کالا تغییر کرده
        if ($count_select_packing_form) {

            $count_select_packing_form = intval($count_select_packing_form);
            $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
            if (!in_array($packing_form_id, $selected_packing_ids)) {
                return "بسته بندی انتخاب شده معتبر نمی باشد.";
            }


            // بررسی تعداد بسته بندی
            if ($count_select_packing_form <= 0 || $count_select_packing_form >= $packing_form->sub_packing_form_number) {
                unset($count_select[$packing_form_id]);

            } else {
                $count_select[$packing_form_id] = $count_select_packing_form;
            }
            $session_data["count_select"] = $count_select;
        }

        // مقدار انتخاب شده کالا تغییر کرده
        if ($amount_select_packing_form) {
            $amount_select = isset($session_data["amount_select"]) ? $session_data["amount_select"] : [];

            // بررسی مقدار بسته بندی انتخاب شده
            if ($amount_select_packing_form <= 0 || $amount_select_packing_form >= $packing_form->getFinalAmount()) {
                unset($amount_select[$packing_form_id]);

            } else {
                $amount_select[$packing_form_id] = $amount_select_packing_form;
            }


            $session_data["amount_select"] = $amount_select;


        }

        if ($count_select_packing_form || $amount_select_packing_form) {
            Product\ProductRequest\ProductRequestFormSessionData::
            setData($product_request_form, $session_data);
        }


        return $this->add_or_remove_package($product_request_form, $packing_list, $packing_form_id, 0, $checked, $only_add, false, $dashboard_type);

    }

    public function remove_packing_form_request(ProductRequestForm $product_request_form, PackingForm $packing_form, $dashboard_type = "")
    {

        // لیست بسته هایی که در انبار موجود است و کالای درخواست شده در آن قرار دارد.
        $packing_list = DeliveryController::getPackingInWarehouse($product_request_form, null, null, "", null, $dashboard_type);

        $this->add_or_remove_package($product_request_form, $packing_list, $packing_form->id, 0, 0, 0, 1, $dashboard_type);

        return back()->with("یک بسته بندی با موفقیت از درخواست حذف گردید.");
    }

    public function add_or_remove_package(
        $product_request_form,
        $packing_list,
        $packing_form_id,
        $all_checked = 0,
        $checked = false,
        $only_add = false,
        $remove_force = false,
        $dashboard_type = ""
    )
    {

        // لیست بسته های انتخاب شده
        $session_data = Product\ProductRequest\ProductRequestFormSessionData::
        getData($product_request_form, false, $dashboard_type == "customer");
        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }

        // در ماژول تغییر بسته بندی جدید، همه بسته بندی ها را به صورت یکجا به درخواست اضافه می کنیم.
        // برای همین باید لیست بسته بندی ها نال باشد و packing_form_id آرایه باشد.
        if ($packing_list == null && is_array($packing_form_id)) {

            $packing_form_ids = $packing_form_id;
            foreach ($packing_form_ids as $packing_ids) {
                if (!in_array($packing_ids, $selected_packing_ids)) {
                    $selected_packing_ids[] = $packing_ids;
                }
            }
            $session_data["selected_packing_ids"] = $selected_packing_ids;
            Product\ProductRequest\ProductRequestFormSessionData::
            setData($product_request_form, $session_data);

            return [
                "result" => 1,
                "message" => "همه بسته بندی ها به درخواست اضافه شدند."
            ];

        }

        $exist = false;
        foreach ($packing_list as $item) {
            if ($item->id == $packing_form_id) {
                $exist = true;
            }
        }

        $other_packing_from = [];
        $transport = TransportPackingForm::
        where("packing_form_id", $packing_form_id)->
        where("status_id", 0)-> // مرجوعی و عدل های باز شده ملاک نیست
        first();
        if ($transport) {
            $other_packing_from = TransportPackingForm::
            where("transport_item_id", $transport->transport_item_id)->
            where("status_id", 0)-> // مرجوعی و عدل های باز شده ملاک نیست
            pluck("packing_form_id")->
            toArray();
        }

        if (!$exist || $remove_force) {
            if ($remove_force) {
                // بسته بندی داخل درخواست انتخاب شده است ولی وضعیت آن غیر مجاز است و یا کد کالای آن نامعتبر است و به صورت اجباری باید از لیست حذف شود.
                // حذف بسته

                if ($dashboard_type == "customer" && $product_request_form->applicant_type_id == 30) {
                    // اگر داشبورد مشتری است، باید بسته بندی از کل درخواست ها حذف شود.

                    // اگر مشتری است، کل انتخاب هایی که برای مشتری انجام شده است را بر می گردانیم برای اینکه بتواند یک خروجی بکشد.
                    $session_data_list = ProductRequestFormSessionData::
                    join("product_request_forms", "product_request_forms.id", "product_request_form_packing_session_data.product_request_form_id")->
                    where("applicant_type_id", $product_request_form->applicant_type_id)->
                    where("applicant_id", $product_request_form->applicant_id)->
                    whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
                    select("product_request_form_packing_session_data.data", "product_request_form_id")->
                    with("product_request_form")->
//                        where("product_request_form_idfgd",1764)->
                    get();


                    foreach ($session_data_list as $session_data_item) {
                        $data_item = json_decode($session_data_item->data, true);
                        if (isset($data_item["selected_packing_ids"])) {
                            // لیست بسته بندی هایی که انتخاب کرده است.


                            self::RemovePackingFormFromSelectedList($session_data_item->product_request_form, $data_item["selected_packing_ids"], $packing_form_id, $other_packing_from);
                        }


                    }
                } else {
                    self::RemovePackingFormFromSelectedList($product_request_form, $selected_packing_ids, $packing_form_id, $other_packing_from);
                }
                return "بسته بندی انتخاب شده، جزء بسته های مجاز برای انتخاب نمی باشد." . $packing_form_id;
            }

        }


        if ($all_checked == 0) {
            ///  اگر بسته داخل بسته ها هست و یا چک همه غیر فعال است، آن را حذف کن
            if (in_array($packing_form_id, $selected_packing_ids)) {
                // حذف بسته
                if ($only_add == false) {
                    $old_selected_packing_ids = $selected_packing_ids;

                    $selected_packing_ids = [];
                    foreach ($old_selected_packing_ids as $item) {

                        if (!($item == $packing_form_id || in_array($item, $other_packing_from))) {
                            $selected_packing_ids[] = $item;
                        }
                    }
                }
            } ///  اگر بسته داخل بسته ها نیست و یا چک همه  فعال است، آن را اضافه کن
            elseif (!in_array($packing_form_id, $selected_packing_ids) && $checked == true) {
                // افزودن بسته
                $selected_packing_ids[] = $packing_form_id;
                foreach ($other_packing_from as $pid) {
                    $selected_packing_ids[] = $pid;
                }
            }

        } elseif ($all_checked == 1) {
            if (!in_array($packing_form_id, $selected_packing_ids)) {
                // افزودن بسته
                $selected_packing_ids[] = $packing_form_id;
                foreach ($other_packing_from as $pid) {
                    $selected_packing_ids[] = $pid;
                }
            }
        } elseif ($all_checked == -1) {
            if (in_array($packing_form_id, $selected_packing_ids)) {
                // حذف بسته

                $old_selected_packing_ids = $selected_packing_ids;

                $selected_packing_ids = [];
                foreach ($old_selected_packing_ids as $item) {

                    if (!($item == $packing_form_id || in_array($item, $other_packing_from))) {
                        $selected_packing_ids[] = $item;
                    }
                }
            }
        }


        $session_data["selected_packing_ids"] = $selected_packing_ids;
        Product\ProductRequest\ProductRequestFormSessionData::
        setData($product_request_form, $session_data);

        return "checked=" . $checked;

    }

    public static function RemovePackingFormFromSelectedList($product_request_form, $selected_packing_ids, $packing_form_id, $other_packing_from)
    {
        $old_selected_packing_ids = $selected_packing_ids;

        $selected_packing_ids = [];
        foreach ($old_selected_packing_ids as $item) {

            if (!($item == $packing_form_id || in_array($item, $other_packing_from))) {

                $selected_packing_ids[] = $item;
            }
        }

        $session_data["selected_packing_ids"] = $selected_packing_ids;
        Product\ProductRequest\ProductRequestFormSessionData::
        setData($product_request_form, $session_data);

    }

    public static function getPackingInWarehouse(
        Product\ProductRequest\ProductRequestForm $product_request_form,
                                                  $product_id = null, $paginate = null,
                                                  $type = "", $product_request_form_packing_types_with_master_packing_default = null, $dashboard_type = "")
    {

        if ($dashboard_type == "customer") {
            $product_request_form_ids = ProductRequestForm::
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            where("applicant_type_id", 30)->
            where("applicant_id", $product_request_form->order->customer_id)->
            pluck("id")->toArray();
            $product_request_form_ids[] = -1;
        } else {
            $product_request_form_ids = [];
            $product_request_form_ids[] = $product_request_form->id;
        }

        $product_ids = ProductRequestFormItem::
        whereIn("product_request_form_id", $product_request_form_ids)->
        where("amount_remaining", ">", 0)->
        pluck("product_id")->toArray();
        $product_ids[] = -1;

        $product_request_form_packing_types =
            Product\ProductRequest\ProductRequestFormPackingType::
            whereIn("product_request_form_id", $product_request_form_ids)->
            whereIn("product_id", $product_ids)->
            when($product_id, function ($query) use ($product_id) {
                return $query->where("product_id", $product_id);
            })->
            get();

        // لیست انواع بسته بندی که بسته بندی های لایه بالاتر هم در آن قرار دارند.
        $product_request_form_packing_types_with_master_packing = [];

        foreach ($product_request_form_packing_types as $item) {

            $product_request_form_packing_types_with_master_packing[] = [
                "product_id" => $item->product_id,
                "packing_type_id" => $item->packing_type_id,
                "degree_id" => $item->product->goods_kind->checking_compatibility_grade_in_delivery ? $item->degree_id : -1
            ];


            // نوع بسته بندی های سطوح بالاتر که لایه اول آنها
            //  $item->packing_type
            // است را هم در نظر می گیریم.
            $higher_packing_type_list = PackingType::getHigherPackingType($item->packing_type);
            foreach ($higher_packing_type_list as $higher_packing_type) {
                $degree_id = $item->product->goods_kind->checking_compatibility_grade_in_delivery ? $item->degree_id : -1;
                $key = $item->product_id . "_" . $higher_packing_type->id . "_" . $degree_id;

                $product_request_form_packing_types_with_master_packing[$key] = [
                    "product_id" => $item->product_id,
                    "packing_type_id" => $higher_packing_type->id,
                    "degree_id" => $degree_id
                ];

            }


        }

        if ($product_request_form_packing_types_with_master_packing_default) {
            $product_request_form_packing_types_with_master_packing[] = $product_request_form_packing_types_with_master_packing_default;

        }


        $query = PackingForm::join("packing_form_item", "packing_form_id", "packing_forms.id")->
        where("warehouse_status_id", 4201)->
        whereIn("product_id", $product_ids)->
        whereNull("packing_form_master_id")->
        where("warehouse_id", $product_request_form->warehouse_id)-> // کالا باید در انباری باشد که درخواست کالا از آن انبار است.
        groupBy("packing_forms.id")->
        orderByDesc("packing_forms.created_at")->
        select("packing_forms.id", "warehouse_shelving_id", "packing_forms.pin1", "packing_forms.code", "packing_type_id", "carrier_id", "sub_packing_form_number");


        // به ازای هر کالا و بسته بندی شرط product=x && packing_type_id=y چک می شود.
        $query->where(function ($query2) use ($product_request_form_packing_types_with_master_packing) {

            foreach ($product_request_form_packing_types_with_master_packing as $item) {

                $query2 = $query2->orWhere(function ($query3) use ($item) {
                    $degree_id = $item["degree_id"];

                    return $query3->where([
                        "product_id" => $item["product_id"],
                        "packing_type_id" => $item["packing_type_id"]
                    ])->when($degree_id > 0, function ($query_degree) use ($degree_id) {
                        return $query_degree->where("degree_id", $degree_id);
                    });
                });

            }

            return $query2;
        });

        if ($type == "only_packing_form_ids") {
            return $query->pluck("packing_forms.id");
        }
        if ($type == "only_packing_form_ids_barcode_type") {
            // با توجه به نوع ورود / خروج از انبار که با کد بسته بندی یا کد چاپی باشد.
            if ($product_request_form->warehouse->allow_entry_with_pin) {
                return $query->pluck("packing_forms.pin1");
            }
            return $query->pluck("packing_forms.id");
        }
        if ($paginate == null) {
            return $query->get();
        } else {
            return $query->paginate(DeliveryController::$PaginateNumber);
        }

    }

    public function index_of_highest_value($array)
    {
        $maxs = array_keys($array, max($array));

        return $maxs[0];
    }

    public function exist_packing_type($packing_type_ids, $packing_type_ids_for_request_item)
    {

        // گرفته بسته بندی های سطح بالای هر بسته بندی که درخواست شده
        $packing_type_request_item_list = PackingType::whereIn("id", $packing_type_ids_for_request_item)->get();
        foreach ($packing_type_request_item_list as $item) {

            $higher_packing_list = PackingType::getHigherPackingType($item);
            foreach ($higher_packing_list as $higher_packing) {
                $packing_type_ids_for_request_item[] = $higher_packing->id;
            }
        }

        foreach ($packing_type_ids as $id) {
            if (in_array($id, $packing_type_ids_for_request_item)) {
                return true;
            }
        }

        return false;
    }

    public static function HasSpecialLicenseMax(ProductRequestForm $product_request_form, $product_id, $value)
    {

        $special_license_list = SpecialLicense::where([
            "special_license_type_id" => 1,
            "reference_id" => $product_request_form->id,
            "status_id" => 6040002, // تایید شده
            "param1" => $product_id
        ])->get();

        foreach ($special_license_list as $special_license)
            if ($special_license->param3 >= $value) {
                return true; // مقدار در حال خروج از مقدار مجوز کمتر یا مساوی است.
            }

        return false;
    }

    public
    static function HasSpecialLicenseMin(ProductRequestForm $product_request_form, $product_id, $value)
    {

        $special_license_list = SpecialLicense::where([
            "special_license_type_id" => 7,
            "reference_id" => $product_request_form->id,
            "status_id" => 6040002, // تایید شده
            "param1" => $product_id
        ])->get();
        foreach ($special_license_list as $special_license) {
            if ($special_license->param3 <= $value) {
                return true; // مقدار در حال خروج از مقدار مجوز بیشتر یا مساوی است.
            }
        }
        return false;

    }

}
