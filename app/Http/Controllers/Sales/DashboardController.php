<?php

namespace App\Http\Controllers\Sales;

use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\AccountBalance;
use App\Models\Customer\ChannelTypePost;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\Form\Form;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Order\OrderLog;
use App\Models\Order\Permision\OrderPermissionPost;
use App\Models\Post\PostStatus;
use App\Models\Post\PostUser;
use App\Models\Utility\Address\ProvincePost;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Models\Order\OrderList;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function Psy\debug;

class DashboardController extends Controller
{


    public function index(Request $request)
    {


        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $waiting_status_id = $request->waiting_status_id;
            $leading_permission_status_id = $request->leading_permission_status_id;
            $exit_form_status_id = $request->exit_form_status_id;
            $product_id_in_search = $request->product_id_in_search;
        } else {
            $search = session("search_sales_dashboard");
            $order_by = session("order_by_sales_dashboard");
            $waiting_status_id = session("waiting_status_id_sales_dashboard");
            $leading_permission_status_id = session("leading_permission_status_id_dashboard");
            $exit_form_status_id = session("exit_form_status_id_dashboard");
            $product_id_in_search = session("product_id_in_search_dashboard");
        }
        if ($order_by == "") {
            $order_by = "orders.order_datetime__desc";
        }

        $filter_data = [
            "search_sales_dashboard" => $search,
            "order_by_sales_dashboard" => $order_by,
            "waiting_status_id_sales_dashboard" => $waiting_status_id,
            "leading_permission_status_id_dashboard" => $leading_permission_status_id,
            "exit_form_status_id_dashboard" => $exit_form_status_id,
            "product_id_in_search_dashboard" => $product_id_in_search
        ];
        session($filter_data);

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $OrderStatusItem = PostStatus::whereIn("post_id", $post_ids)->pluck("status_id");
        $provinceItem = ProvincePost::whereIn("post_id", $post_ids)->pluck("province_id");
        $channelItem = ChannelTypePost::whereIn("post_id", $post_ids)->pluck("channel_type_id");
        $allowOrderStatus = $OrderStatusItem;

        // آیا انبار جهت خروج کالا، نیاز به ثبت فرم مجوز بارگیری توسط فروش دارد؟
        $does_sales_need_to_register_a_loading_permit_form = Setting::getIntegerValue("does_sales_need_to_register_a_loading_permit_form");

        $will_be_processed_later = [];
        // search
        if ($waiting_status_id != 0) {

            $OrderStatusItem = [];
            $OrderStatusItem[] = $waiting_status_id;


        }

        if (in_array(304080, $allowOrderStatus->toArray())) { // وقتی در حال پردازش است، انهاییکه بعدا پردازش می شوند را هم نمایش دهد.
            $will_be_processed_later = OrderLog::where("event_id", 35100)->pluck("order_id", "order_id")->toArray();
        }

        $product_ids = [-1];

        if ($product_id_in_search) {
            $product_ids = Product::where("code", "like", "%" . $product_id_in_search . "%")->orWhere("caption", "like", "%" . $product_id_in_search . "%")->pluck("id", "id")->toArray();
            $product_ids[] = -1;


        }

        $list = Order::
        join("customers", "customer_id", "customers.id")->
        when($product_id_in_search, function ($query) {
            return $query->join("order_list", "orders.id", "order_list.order_id")->
            where("order_list.customer_id",">",0);
        })->
        when($product_id_in_search, function ($query) use ($product_ids) {
            return $query->whereIn("order_list.product_id", $product_ids);
        })->
        when(count($OrderStatusItem) > 0, function ($query) use ($OrderStatusItem, $will_be_processed_later) {
            if (count($will_be_processed_later) > 0) {
                return $query->where(function ($query) use ($OrderStatusItem, $will_be_processed_later) {
                    return $query->whereIn("orders.status_id", $OrderStatusItem)->
                    orWhereIn("orders.id", $will_be_processed_later);
                });

            } else {
                return $query->whereIn("orders.status_id", $OrderStatusItem);
            }

        })->
        when($leading_permission_status_id != 0, function ($query) use ($leading_permission_status_id) {
            return $query->where("orders.loading_status_id", $leading_permission_status_id);
        })->

        whereIn("customers.province_id", $provinceItem)->
        whereIn("customers.channel_id", $channelItem)->
        when($search != "", function ($query) use ($search) {
            if (strpos($search, '/') !== false) {// ایا متن شامل ممیز است اگر بود به این صورت اگر نبود سرچ عادی که از قبل داشتیم
                list($series, $code) = explode('/', $search, 2);// مقدار را به دو بخش تبدیل می کند.
                return $query->where("orders.series", "like", "%" . $series . "%")
                    ->where("orders.code", "like", "%" . $code . "%");
            } else {
                return $query->where(function ($query) use ($search) {
                    return $query->where("orders.code", "like", "%" . $search . "%")
                        ->orWhere("orders.series", "like", "%" . $search . "%")
                        ->orWhere("customers.caption", "like", "%" . $search . "%")
                        ->orWhere("customers.code", "like", "%" . $search . "%");
                });
            }
        })->
        when($exit_form_status_id != 0, function ($query) use ($exit_form_status_id) {
            $query->
            join("product_request_forms", "product_request_forms.order_id", "orders.id")->
            join("product_request_form_form", "product_request_forms.id", "product_request_form_form.product_request_form_id")->
            join("forms", "forms.id", "product_request_form_form.form_id")->
            where("product_request_forms.applicant_type_id", 30)->
            where("forms.status_id", $exit_form_status_id);
        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        distinct("orders.id")->
        select([
            "orders.id as id",
            "orders.code",
            "orders.priority_id",
            "orders.status_id",
            "address_id"
            ,
            "orders.customer_id",
            "series",
            "orders.order_datetime",
            "register_xml_status_id",
            "register_xml_code"
        ])->
        paginate(50);


        $order_by_Option = Option::OrderBy("orders", $order_by);
        $waiting_status_option = Option::get("order_waiting_status", $waiting_status_id, $allowOrderStatus->toArray());
        $leading_permission_status_option = Option::get("status", $leading_permission_status_id, 4600);

        $user = \Auth::user();
        $post_user = \Auth::user()->posts->first();

        $token_api = $user->createToken('web-token')->plainTextToken;

        $exit_form_status_option = Option::get("status_in_ids", $exit_form_status_id, 0, Form::ExitFormStatus());

        session(["back_url" => route("sales.dashboard.index")]);

        return view("sales.dashboard.index", compact("waiting_status_option", "token_api",
            "leading_permission_status_option", "product_id_in_search", "post_user", "order_by_Option", "list",
            "search", "exit_form_status_option", "does_sales_need_to_register_a_loading_permit_form"));

    }

    public function view_order(Order $order)
    {

        $post_user = \Auth::user()->posts->first();

        $result = $this->checkPermission($order);
        if ($result != null) {
            return $result;
        }
        $is_customer = Customer::where("user_id", \Auth::id())->exists();
        $order = $order->calculate();
        $account_balances = AccountBalance::where("customer_id", $order->customer_id)->first();
        $back_url = session("back_url");
        $reject_product_form_list = RejectProductForm::where("order_id", $order->id)->get();

        $product_request_forms = ProductRequestForm::select("id", "code", "status_id")->where([
            "order_id" => $order->id,
            "applicant_type_id" => 30
        ])->with("status")->get()->keyBy("id");

        // در صورتی که انتخاب کرده است، بعدا پردازش کند، باید بتواند، بعد از آماده سازی یا برگ خروج پردازش را انجام دهد.


        return view("sales.dashboard.view_order", compact("back_url", "account_balances", "order", "product_request_forms", "post_user", "reject_product_form_list", "is_customer"));
    }

    public function DCOV_SortLink($order_id)
    {
        $order = Order::find($order_id);
        if (!$order) {
            return back()->withErrors("سفارش مورد نظر یافت نشد.");
        }

        return $this->view_order($order);
    }

    public function view_form(Order $order, Form $form)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales._exist_form_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        $allow_create_reject_form = \Auth::user()->posts->first()->checkButtonPermission("sales.reject_product.index");

        return view("sales.dashboard.view_form", compact("form", "order", "allow_create_reject_form"));
    }

    public function view_product_request_form(Order $order, ProductRequestForm $product_request_form, $back_type = "", $q1 = 0, $q2 = 0)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales._exist_form_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        return view("sales.dashboard.view_product_request_form", compact("product_request_form", "order", "back_type", "q1", "q2"));
    }

    public function view_leads_in_warehouse(Order $order)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.view_leads_in_warehouse")) {
            return back()->withErrors("شما اجازه دسترسی به صفحه مورد نظر را ندارید");
        }

        $product_request_form = ProductRequestForm::
        where("applicant_type_id", 30)->
        where("applicant_id", $order->customer_id)->
        where("order_id", $order->id)->first();

        if (!$product_request_form) {
            return back()->withErrors("هنوز درخواست کالا از انبار برای سفارش ایجاد نشده است.");
        }
        foreach ($product_request_form->items as $item) {
            $selected_amount[$item->id]["CurrentDelivery"] = \App\Http\Controllers\Warehouse\Out\DashboardController::getCurrentDelivery($item);

            $selected_amount[$item->id]["CurrentSelectedToExist"] = \App\Http\Controllers\Warehouse\Out\DashboardController::getCurrentSelectedToExist($item);

            $selected_amount[$item->id]["WarehouseInventory"] = \App\Http\Controllers\Warehouse\Out\DashboardController::getWarehosueInventory($item);

        }
        $selected_amount["ShowWarehouseInventory"] = "show";

        $product_request_form_items = ProductRequestFormItem::
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*")->
        where("product_request_form_item.product_request_form_id", $product_request_form->id)->

        with("product")->get();
        return view("sales.dashboard.view_leads_in_warehouse", compact("product_request_form", "order", "selected_amount", "product_request_form_items"));

    }

    public function view_reject_product_form(Order $order, RejectProductForm $reject_product_form)
    {

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.show_reject_product_form_list")) {
            return back()->withErrors("شما اجازه دسترسی به صفحه مورد نظر را ندارید");
        }


        $reject_product_in_send_product = Setting::getStringValue("reject_product_in_send_product");

        return view("sales.dashboard.view_reject_product_form", compact("reject_product_in_send_product", "reject_product_form", "order"));

    }

    public function confirm(Request $request, Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != null) {
            return $result;
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales." . $order->status_id)) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        if ($order->status_id == 304080) {
            // چک کردن اینکه در هر لحظه فقط یک درخواست در حال پردازش دستی باشد.
            $first_order_list = OrderList::join("orders", "orders.id", "order_id")->
            where("orders.status_id", "304080")-> // در انتظار تایید پردازش
            where("order_list.status_id", 301)->
            first();

            if ($first_order_list && $first_order_list->order_id != $order->id) {
                return back()->withErrors("با توجه به اینکه سفارش " . $first_order_list->order->code() . " در حال پردازش می باشد، امکان پردازش این سفارش وجود ندارد، لطفا پس از پردازش سفارش " . $first_order_list->order->code() . "، نسبت به پردازش این سفارش اقدام نمایید.");
            }
        }

        $order->nextOrderPermission($request->comment, $request->customer_comment);

        return redirect()->route("sales.dashboard.index")->with(["success" => "درخواست با موفقیت تایید شد"]);


    }

    public function reject(Request $request, Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != null) {
            return $result;
        }


        switch ($request->reject_type) {
            case 1:
                // کنسل کردن درخواست
                if (!\Auth::user()->posts->first()->checkButtonPermission("sales.35060")) {
                    return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
                }
                foreach ($order->orderList as $item) {
                    $item->erp_status_id = 320;// خاتمه یافته
                    $item->status_id = 302;// پردازش نشده
                    $item->save();
                    $item->log();
                }
                $order->status_id = 35065;
                $order->save();
                event(new OrderLogEvent($order, 35065, $request->message, $request->customer_message));


                $order->sendSms("ordersms35060"); // پیامک خاتمه یافته شدن
                break;

            case 2:
                // ارجاع به مرحله قبل
                if (!\Auth::user()->posts->first()->checkButtonPermission("sales." . $order->status_id)) {
                    return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
                }
                $order->previousOrderPermission($request->message, $request->customer_message);
                event(new OrderLogEvent($order, 304993, $request->message, $request->customer_message));
                break;

            case 3:
                // ارجاع به کارتابل مشتری
                if (!\Auth::user()->posts->first()->checkButtonPermission("sales." . $order->status_id)) {
                    return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
                }
                $order->status_id = 304010;
                $order->save();
                event(new OrderLogEvent($order, 304994, $request->message, $request->customer_message));

                break;

            case 5:
                // ارجاع به کارتابل کارشناس فروش
                if (!\Auth::user()->posts->first()->checkButtonPermission("sales." . $order->status_id)) {
                    return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
                }
                $order->status_id = 304020;
                $order->save();
                event(new OrderLogEvent($order, 304995, $request->message, $request->customer_message));
                break;
        }

        return redirect()->route("sales.dashboard.index")->with(["success" => "درخواست با موفقیت به کارتابل مربوطه ارجاع شد"]);

    }

    public function terminate_order(Request $request, Order $order)
    {
        // خاتمه یافت کردن سفارش
        $result = $this->checkPermission($order);
        if ($result != null) {
            return $result;
        }

        if (!in_array($order->status_id, [35030, 35040])) {
            return back()->withErrors("وضعیت سفارش جهت خاتمه یافته کردن سفارش معتبر نمی باشد." . "<br/>" . "فقط سفارش های در وضعیت آماده سازی و ارسال نافص را می توان خاتمه یافته کرد");
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.terminate_order")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        // بررسی درخواست کالا از انبار
        $product_request_form = ProductRequestForm::where("order_id", $order->id)->first();


        // چک کردن اینکه همه برگ خروج ها در وضعیت مناسب باشند.
        $forms = ProductRequestFormForm::
        join("forms", "forms.id", "=", "form_id")->
        where("product_request_form_id", $product_request_form->id ?? -1)->
        where("forms.status_id", "!=", 500000100)-> // عدم تایید
        get();

        foreach ($forms as $form) {
            if ($form->status_id != 500000200) { /// تایید شده
                return back()->withErrors("با توجه به اینکه برگ خروج " . $form->code . " مربوط به این سفارش در وضعیت " . $form->status->capton . " می باشد، امکان کنسل کردن درخواست وجود ندارد.");
            }
        }

        // چک کردن اینکه درخواست خروج از انبار در وضعیت مناسب باشد.
        if ($product_request_form && in_array($product_request_form->status_id, [7005004, 7005005, 7005007, 7005201])) {
            return back()->withErrors("با توجه به اینکه امکان خاتمه یافته کردن درخواست کالا از انبار با شماره " .
                $product_request_form->code .
                " مربوط به سفارش " .
                $order->code() .
                " وجود ندارد،<br/> امکان خاتمه یافته کردن سفارش وجود ندارد");
        }

        if ($product_request_form) {
            $product_request_form->status_id = count($forms) == 0 ? 7005006 : 7005009;
            $product_request_form->save();

            event(new ProductRequestFormLogEvent($product_request_form, $request->message, null, 7005016));
        }

        // ردیف های سفارش هم خانمه یافته می شود.
//        foreach ($order->orderList as $item) {
//            $item->erp_status_id = 320;// خاتمه یافته
//            $item->status_id = 302;// پردازش نشده
//            $item->save();
//            $item->log();
//        }


        $order->status_id = count($forms) == 0 ? 35075 : 35078;
        $order->save();
        event(new OrderLogEvent($order, 35070, $request->message, $request->customer_message));


        // $order->sendSms("ordersms35060"); // پیامک خاتمه یافته شدن


        return redirect()->route("sales.dashboard.index")->with(["success" => "سفارش " . $order->code() . " با موفقیت خاتمه یافته گردید."]);

    }

    public function special_off(Request $request, Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != null) {
            return $result;
        }

        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.special_off")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }
        $order->special_off_price = $request->special_off_price;
        $order->save();
        event(new OrderLogEvent($order, 304992, $order->special_off_price));
        $order->updateSpecialOff();
        $order->updateRoundOff();
        $order->updatePrice();

        $order = Order::find($order->id);

        return redirect()->route("sales.dashboard.view_order", $order)->with(["success" => "تخفیف خاص با موفقیت بر روی فاکتور اعمال گردید"]);
    }

    public function loading_permission(Request $request, Order $order)
    {


        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.exit_permission")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        if ($order->loading_status_id == 460000200) {
            return back()->withErrors("مجوز بارگیری قبلا ثبت شده است");
        }
        $order->loading_status_id = 460000200;// مجوز خروج صادر شد
        $order->exit_datetime = Carbon::now();
        $order->save();
        event(new OrderLogEvent($order, 35080));

        return redirect()->route("sales.dashboard.view_order", $order)->with(["success" => "مجوز خروج با موفقیت ثبت گردید"]);

    }

    public function register_xml(Order $order)
    {
        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.register_xml")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        return view("sales.dashboard.register_xml", compact("order"));
    }

    public function register_xml_submit(Request $request, Order $order)
    {
        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.register_xml")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        $order->register_xml_code = $request->register_xml_code;
        $order->register_xml_status_id = 523000200;
        $order->save();

        return redirect()->route("sales.dashboard.index")->with(["success" => "کد با موفقیت ثبت گردید."]);
    }

    public function log(Order $order)
    {
        $user_id = \Auth::user()->id;
        $is_customer = Customer::where("user_id", $user_id)->exists();

        return view("sales.dashboard.log", compact("order", "is_customer"));
    }

    public function print_product_request_form(Order $order, ProductRequestForm $product_request_form, PackingTypeLabelPrintingType $packing_type_label, $type, $dashboard_type = "")
    {
        $result = \App\Http\Controllers\Warehouse\Out\DashboardController::create_pdf_file($product_request_form, $packing_type_label, $type, $dashboard_type);
        if ($type == "print") {
            Pdf::labelPrinter($result["html"],
                $packing_type_label->orientation,
                $product_request_form->id, [
                    $packing_type_label->width,
                    $packing_type_label->long
                ],
                $result["print_file"]);


            return back()->with(["success" => "دستور پرینت با موفقیت ثبت گردید."]);
        } else {
            Pdf::labelPrinter($result["html"],
                $packing_type_label->orientation,
                $product_request_form->code, $packing_type_label->size,
            );

        }

    }

    public function receipt_from_customer(Order $order)
    {
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if ($order->status_id != 304030 || !$post_user->checkButtonPermission("sales.304030")) {
            return back()->withErrors("دسترسی عملیات تایید از جانت مشتری برای شما تعریف نشده است.");
        }

        return view("sales.dashboard.confirm_receipt_from_customer", compact("order"));
    }

    public function confirm_receipt_from_customer(Request $request, Order $order)
    {
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if ($order->status_id != 304030 || !$post_user->checkButtonPermission("sales.304030")) {
            return back()->withErrors("دسترسی عملیات تایید از جانت مشتری برای شما تعریف نشده است.");
        }

        if (!isset($request->image_file)) {
            return back()->withErrors("بارگذاری تصویر تاییدیه از طرف مشتری الزامی است.");

        }
        $file = File::uploadFile($request->file('image_file'), $order->id . "_" . rand(1000, 9000) . ".png", 110, "upload/sales/", true);

        $message = "<a target='_block' href='" . asset("upload/sales/" . ($file->filename ?? '')) . "' > تصویر تاییدیه از طرف مشتری</a>";

        $order->nextOrderPermission($message);

        return redirect()->route("sales.dashboard.view_order", $order)->with(["success" => "تایید با موفقیت ثبت گردید."]);

    }

    public function will_be_processed_later(Order $order)
    {
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if ($order->status_id != 304080 || !$post_user->checkButtonPermission("sales.will_be_processed_later")) {
            return back()->withErrors("دسترسی عملیات برای شما تعریف نشده است.");
        }

        event(new OrderLogEvent($order, 35100, "", ""));
        $order->nextOrderPermission("");

        return redirect()->route("sales.dashboard.index", $order)->with(["success" => "تایید پردازش با موفقیت ثبت گردید."]);

    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     * فعال / غیر فعال کردن پردازش توسط دستیار دیجیتال دیاکو
     */
    public function change_processed_by_script(Order $order)
    {
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if (!$post_user->checkButtonPermission("sales." . $order->status_id)) {
            return back()->withErrors("دسترسی عملیات برای شما تعریف نشده است.");
        }

        $order_log = OrderLog::where([
            "order_id" => $order->id,

        ])->
        whereIn("event_id", [35103, 35104,35105])->
        orderBy("id", "desc")->
        first();

        if (!$order_log || ($order_log &&  $order_log->event_id == 35105)) {
            event(new OrderLogEvent($order, 35103, "", ""));
        } else {
            event(new OrderLogEvent($order, 35105, "", ""));
        }


        return back()->with(["success" => "تغییرات نوع پردازش توسط کارشناس دیجیتال با موفقیت ثبت گردید."]);

    }

    public function register_processed_later(Order $order)
    {
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if (!$post_user->checkButtonPermission("sales.304080")) {
            return back()->withErrors("دسترسی عملیات برای شما تعریف نشده است.");
        }
        $will_be_processed_later = $order->will_be_processed_later();
        if (!$will_be_processed_later) {
            return back()->withErrors("این سفارش قبلا پردازش شده است و یا نیاز به پردازش  ندارد.");
        }
        $will_be_processed_later->event_id = 35101;
        $will_be_processed_later->save();
        event(new OrderLogEvent($order, 35102, "", ""));


        return redirect()->route("sales.dashboard.index", $order)->with(["success" => "تایید پردازش با موفقیت ثبت گردید."]);

    }

    private function checkPermission($order)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");

        $statusCheck = PostStatus::whereIn("post_id", $post_ids)->where(["status_id" => $order->status_id])->exists();
        if (in_array($order->status_id, [35030, 35040, 35090]) && !$statusCheck) { // وقتی در حال پردازش است، انهاییکه بعدا پردازش می شوند را هم نمایش دهد.
            $statusCheck = OrderLog::where("event_id", 35100)->where("order_id", $order->id)->exists();
        }
        $provinceCheck = ProvincePost::whereIn("post_id", $post_ids)->where(["province_id" => $order->customer->province_id])->exists();
        $channelCheck = ChannelTypePost::whereIn("post_id", $post_ids)->where(["channel_type_id" => $order->customer->channel_id])->exists();
        $worker = Worker::find(\Auth::user()->id);
        $post_user = \Auth::user()->posts->first();

        // 615: "sales.dashboard.index";
        if ($post_user->getMenuPermission($worker, 615) && $statusCheck && $provinceCheck && $channelCheck) {
            return null;
        }

        return back()->withErrors("شما اجازه دسترسی به سفارش مورد نظر را ندارید(511)");
    }

}
