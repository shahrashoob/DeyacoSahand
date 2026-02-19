<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Warehouse\Shelving\Shelving;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Mpdf\Tag\Del;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class   DashboardController extends Controller
{
    var $view_path = "warehouse.out.dashboard.";
    var $route_path = "wh.out.dashboard.";
    static $session_name = "selected_packing_id";

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $search_order_code = $request->search_order_code;
            $status_id = $request->status_id;
            $warehouse_id = $request->warehouse_id;
            $order_by = $request->order_by;
            $loading_status_id = $request->loading_status_id;
            $search_exist_form = $request->search_exist_form;
            $exit_form_status_id = $request->exit_form_status_id;
            $prf_type_id = $request->prf_type_id;
        } else {
            $search = session("search_product_request");
            $search_order_code = session("product_request_search_order_code");
            $status_id = session("product_request_status_id");
            $warehouse_id = session("product_request_warehouse_id");
            $order_by = session("product_request_order_by") ?? "product_request_forms.created_at__desc";
            $loading_status_id = session("product_request_loading_status_id");
            $search_exist_form = session("product_request_search_exist_form");
            $exit_form_status_id = session("product_request_search_exist_exit_form_status_id");
            $prf_type_id = session("product_request_search_exist_exit_prf_type_id");


        }

        $loading_status_id = $loading_status_id == 0 ? "" : $loading_status_id;
        session([
            "search_product_request" => $search,
            "product_request_status_id" => $status_id,
            "product_request_warehouse_id" => $warehouse_id,
            "product_request_search_order_code" => $search_order_code,
            "product_request_order_by" => $order_by,
            "product_request_loading_status_id" => $loading_status_id,
            "product_request_search_exist_form" => $search_exist_form,
            "product_request_search_exist_exit_form_status_id" => $exit_form_status_id,
            "product_request_search_exist_exit_prf_type_id" => $prf_type_id,
            DashboardController::$session_name => null,
        ]);

        // بعد از 24 ساعت توکن را منقضی می کند.
        session([
            "bearer_token" => Auth::user()->createToken("API TOKEN", ['*'], Carbon::now()->addMinute(24 * 60))->plainTextToken
        ]);

        $order_by = $order_by == "" ? "product_request_forms.created_at__desc" : $order_by;
        $allowed_status_ids = Post::GetAllStatusPermission();
        $all_allowed_status_ids = $allowed_status_ids;
        // search
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

        // جستجوی عبارت در عنوان درخواست دهنده:
        $customer_list_ids = [];
        $contractor_list_ids = [];
        $machine_list_ids = [];
        if ($search != "") {
            $customer_list_ids = Customer::where("caption", "like", "%" . $search . "%")->pluck("id");
            $contractor_list_ids = Contractor::where("caption", "like", "%" . $search . "%")->pluck("id");
            $machine_list_ids = Machine::where("caption", "like", "%" . $search . "%")->pluck("id");
        }

        $list = ProductRequestForm::
        when($prf_type_id, function ($query) use ($prf_type_id) {
            return $query->where("product_request_form_type_id", $prf_type_id);
        })->
        whereIn("product_request_forms.warehouse_id", $allowed_warehouse_ids)->
        whereIn("product_request_forms.status_id", $allowed_status_ids)->
        whereIn("product_request_forms.active_status_id", $all_allowed_status_ids)->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        when($search_exist_form != "" || $exit_form_status_id, function ($query) use ($search_exist_form, $exit_form_status_id) {

            return $query->join("product_request_form_form", "product_request_form_id", "product_request_forms.id")->
            join("forms", "forms.id", "product_request_form_form.form_id")->

            // جستجوی شماره برگ خروج
            when($search_exist_form != "", function ($query) use ($search_exist_form) {
                return $query->where("forms.code", "like", "%" . $search_exist_form . "%");
            })->
            // وضعیت برگ خروج
            when($exit_form_status_id != 0, function ($query) use ($exit_form_status_id) {
                $query->where("forms.status_id", $exit_form_status_id);
            })->
            groupBy("product_request_form_id");

        })->
        when($search != "" && ($status_id == 0 && count($customer_list_ids) == 0 && count($contractor_list_ids) == 0 && count($machine_list_ids) == 0), function ($query) use ($search) {
            return $query->where("product_request_forms.code", "like", "%" . $search . "%");
        })->
        when($search_order_code != "" || $loading_status_id != "", function ($query) {
            return $query->join("orders", "orders.id", "product_request_forms.order_id")->
            where("product_request_forms.applicant_type_id", 30);
        })->
        when($search_order_code != "", function ($query) use ($search_order_code) {
            if (strpos($search_order_code, '/') !== false) {// ایا متن شامل ممیز است اگر بود به این صورت اگر نبود سرچ عادی که از قبل داشتیم
                list($series, $code) = explode('/', $search_order_code, 2);// مقدار را به دو بخش تبدیل می کند.
                return $query->where("orders.series", "like", "%" . $series . "%")
                    ->where("orders.code", "like", "%" . $code . "%");
            } else {
                return $query->where(function ($query) use ($search_order_code) {
                    return $query->where("orders.code", "like", "%" . $search_order_code . "%");
                });
            }

        })->
        when($loading_status_id != "", function ($query) use ($loading_status_id) {
            return $query->where("orders.loading_status_id", $loading_status_id);
        })->
        // بررسی درخواست کننده ها
        where(function ($query) use ($customer_list_ids, $contractor_list_ids, $machine_list_ids) {
            return $query->when(count($customer_list_ids) > 0, function ($query) use ($customer_list_ids) {
                return $query->orWhereIn("product_request_forms.applicant_id", $customer_list_ids);
            })->
            when(count($contractor_list_ids) > 0, function ($query) use ($contractor_list_ids) {
                return $query->orWhereIn("product_request_forms.applicant_id", $contractor_list_ids);
            })->
            when(count($machine_list_ids) > 0, function ($query) use ($machine_list_ids) {
                return $query->orWhereIn("product_request_forms.applicant_id", $machine_list_ids);
            });
        })->
        selectRaw("product_request_forms.id,product_request_forms.status_id,product_request_forms.user_id,product_request_forms.warehouse_id,product_request_forms.applicant_id,product_request_forms.applicant_type_id,product_request_forms.created_at")->
        with("warehouse", "worker")->
        paginate(15);

        $order_by_Option = Option::OrderBy("warehouse_output", $order_by);
        $loading_status_option = Option::get("status", $loading_status_id, 4600);
        $prf_type_option = Option::get("product_request_form_type", $prf_type_id);

        // حذف کردن فیلد های فعال / غیر فعال از لیست آپشن ها
        if (($key = array_search(7005101, $allowed_status_ids)) !== false) {
            unset($allowed_status_ids[$key]);
        }
        if (($key = array_search(7005102, $allowed_status_ids)) !== false) {
            unset($allowed_status_ids[$key]);
        }

        //نمایش فرم هایی که به صورت دستی یا با دستیار دیجیتال خارج شده اند.
        $search_exist_form_model = null;
        if (count($list) == 0 && $search_exist_form != "") {
            $search_exist_form_model = Form::where("code", "DCEF/" . $search_exist_form)->where("form_type_id", 0)->first();

        }

        $status_option = Option::get("status_permission", $status_id, 7005, $all_allowed_status_ids);
        $exit_form_status_option = Option::get("status_in_ids", $exit_form_status_id, 0, Form::ExitFormStatus());
        $bearer_token = User::GetBearerToken();
        return view($this->view_path . "index", compact(
            "list", "loading_status_option",
            "search_exist_form", "search", "search_order_code",
            "status_option", "warehouse_option", "order_by_Option",
            "exit_form_status_option", "bearer_token", "search_exist_form_model", "prf_type_option"
        ));
    }

    public function view(ProductRequestForm $product_request_form, $page = 1)
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        if ( $product_request_form->status_id == 7005003 ) {
//           return $warps_is_in_warehouse = Warps::warpsExistInWarehouse(null,  $productRequestForm );
            // اگر در انتظار تکیمل موجودی بود، یکبار دیگر چک می کند که چله وارد انبار شده است یا خیر
            // ورود چله به انبار
            foreach ($product_request_form->items as $item) {
                event( new WarpsAvailableEvent( $item->product,Auth::id() ) );
            }
            $product_request_form=ProductRequestForm::find($product_request_form->id);
        }
        // فرم های در انتظار تایید درخواست کننده
        $waiting_for_confirm_form = ProductRequestForm::
        where(["status_id" => 7005004, "applicant_type_id" => $product_request_form])->
        first();

        if (isset($waiting_for_confirm_form) && $product_request_form->status_id == 7005001) {
            return back()->withErrors("   فرم  " . $waiting_for_confirm_form->code . " <b>'در انتظار تایید درخواست کننده'</b> می باشد، تا زمانی که این فرم تایید نشود، امکان تحویل کالا وجود ندارد.");
        }


//        $post_user = Auth::user()->posts->first();

        $allow_select_product = 1; // ممکن است جایی فقط خواسته باشیم، طرف اطلاعات درخواست را مشاهده کند، بنابراین مقدار این متعیر را صفر می کنیم.

        if ($product_request_form->forms()->count() >= 15) {
            $allow_select_product = 0;
        }
        $bearer_token = User::GetBearerToken();

        $product_request_form_items = ProductRequestFormItem::
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*")->
        where("product_request_form_item.product_request_form_id", $product_request_form->id)->

        with("product")->get();
        return view($this->view_path . "view", compact("product_request_form", "page", "allow_select_product", "bearer_token", 'product_request_form_items'));
    }

    public function view_order(ProductRequestForm $product_request_form)
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }


        if (!$product_request_form->order) {
            return back()->withErrors("برای درخواست سفارشی وجود ندارد.");
        }
        $order = $product_request_form->order;

        return view($this->view_path . "view_order", compact("product_request_form", "order"));

    }

    public function show_form(ProductRequestForm $product_request_form, Form $form, $page = 1)
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $form_item = $form->item()->first();
        if (!$form_item) {
            return back()->withErrors("هیچ ردیفی برای فرم خروج وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $packing_form = $form_item->packing_form_item->packing_form;
        $sum_amount = $form->item()->sum("amount");
        $sum_sub_amount = $form->item()->sum("sub_amount");

//      return  $form->itemOrderByTransportCode("group_by_packing_form");

        return view($this->view_path . "show_form", compact("sum_amount", "sum_sub_amount", "product_request_form", "form", "packing_form", "page"));

    }

    public function show_json_data(ProductRequestForm $product_request_form, ProductRequestFormLog $product_request_form_log, $page = 1)
    {

        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $post_user = Auth::user()->posts->first();

        if (!$post_user->checkButtonPermission("wh.out.dashboard.show_json_data")) {
            return back()->withErrors("شما به صفحه مورد نظر دسترسی ندارید.");
        }

        return view($this->view_path . "show_json_data", compact("product_request_form", "product_request_form_log", "page"));
    }

    public function log(ProductRequestForm $product_request_form, $page = 1)
    {
        $list = $product_request_form->get_log_with_status();
        return view($this->view_path . "log", compact("product_request_form", "page", "list"));
    }


    public function DCRP_QR(ProductRequestForm $product_request_form, $page = 1)
    {
        return $this->view($product_request_form, $page);
    }

    public function print_product_request_form(ProductRequestForm $product_request_form, PackingTypeLabelPrintingType $packing_type_label, $type, $dashboard_type = "")
    {

        $result = self::create_pdf_file($product_request_form, $packing_type_label, $type, $dashboard_type);


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

    public static function create_pdf_file(ProductRequestForm $product_request_form, PackingTypeLabelPrintingType $transport_item_label_type, $type, $dashboard_type)
    {

        $static_ip = Setting::getStringValue("static_ip");
        $local_ip = url("");
        $url = route("DCRP_QR", [$product_request_form]);
        $worker = Auth::user();

        $software_name = Setting::getStringValue("software_name");

        $product_request_form_items = ProductRequestFormItem::
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*")->
        when($dashboard_type == "customer", function ($query) use ($product_request_form) {
            return $query->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            where("amount_remaining", ">", 0)->
            where("applicant_type_id", 30)->
            where("applicant_id", $product_request_form->order->customer_id);
        })->
        when($dashboard_type != "customer", function ($query) use ($product_request_form) {
            return $query->
            where("product_request_forms.id",$product_request_form->id);
        })->


        with("product")->get();
        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ($static_ip != "") {
            $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
        }
        $qr = QrCode::size(100)->generate($url);


        $date_now = jdate(Carbon::now()->timestamp)->format('Y/m/d ');
        $view_path = "warehouse.out.print_product_request_form.";
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_template" . $transport_item_label_type->id, compact('product_request_form_items', "date_now", "product_request_form", "software_name", "qr","dashboard_type"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();


        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $product_request_form->getCode() . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $transport_item_label_type->orientation,
                "printer_id" => $worker->default_printer_id
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

    public static function check_permission(ProductRequestForm $product_request_form)
    {


        $allowed_status_ids = Post::GetAllStatusPermission();
        if (!in_array($product_request_form->status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما به فرم " . $product_request_form->getCode() . " دسترسی ندارید."
            ];
        }

        $allowed_warehouse_ids = Warehouse::getAllowedWarehouse();
        if (!in_array($product_request_form->warehouse_id, $allowed_warehouse_ids)) {
            return [
                "result" => false,
                "message" => "شما به انبار " . $product_request_form->warehouse->code . " دسترسی ندارید."
            ];
        }

        return [
            "result" => true
        ];
    }

    public static function getCurrentSelectedToExist(ProductRequestFormItem $product_request_form_item)
    {

        // محاسبه مقدار انتخاب شده جهت خروج
        $session_data = ProductRequestFormSessionData::
        getData($product_request_form_item->product_request_form, true);

        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }
        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $exit_amount_of_packing_form = isset($session_data["exit_amount_of_packing_form"]) ? $session_data["exit_amount_of_packing_form"] : [];


        // چون برای بسته بندی های دو سطحی، سطح اول آن را هم بررسی می کنیم.
        $packing_type_ids = $product_request_form_item->product_request_form_packing_types()->pluck("packing_type_id")->toArray();
        // اگر بسته بندی سطح اول داشته باشد، سطح اول آن را هم انتخاب می کنیم.
        $first_packing_type_ids = PackingType::
        whereIn("id", $packing_type_ids)->
        whereNotNull("first_packing_type_id")->
        pluck("first_packing_type_id")->toArray();
        // اگر نوع بسته بندی، بوببین باشد، باید بسته بیندی های کارتن را هم انتخاب کنیم.
        $master_packing_type_ids = PackingType::
        whereIn("first_packing_type_id", $packing_type_ids)->
        pluck("id")->toArray();

        $packing_type_ids = array_merge($packing_type_ids, $first_packing_type_ids, $master_packing_type_ids);


        $packing_type_ids[] = -1;

        $value = [];
        $packing_forms_amount = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereIn("packing_type_id", $packing_type_ids)->
        where(["product_id" => $product_request_form_item->product_id])->
        //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("packing_forms.id")->
        addSelect(DB::raw("packing_forms.sub_packing_form_number,packing_forms.id,sum(final_amount) as final_amount"))->
        get();
        $amount_sum = 0;
        foreach ($packing_forms_amount as $packing_form_data) {

            if (isset($exit_amount_of_packing_form[$packing_form_data->id])) // اگر بسته بندی از انبارک خارج می شود، مقداری که انتخاب کرده باید نمایش داده شود.
            {
                $amount_sum += $exit_amount_of_packing_form[$packing_form_data->id];
            } else {
                if (isset($count_select[$packing_form_data->id]) && $count_select[$packing_form_data->id] > 0 && $packing_form_data->sub_packing_form_number > 0) {
                    $amount_sum += $packing_form_data->final_amount * $count_select[$packing_form_data->id] / $packing_form_data->sub_packing_form_number;
                } else {
                    $amount_sum += $packing_form_data->final_amount;
                }
            }
        }

        $value["amount"] = round($amount_sum, 2);
        $value["count"] =
            count(PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
            where(["product_id" => $product_request_form_item->product_id])->
            whereIn("packing_type_id", $packing_type_ids)->
            //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
            whereIn("packing_form_id", $selected_packing_ids)->
            groupBy("packing_form_id")->
            select("packing_form_id")->
            get());


        return $value;

        //addSelect( DB::raw( "count(packing_form_id) as count" ) )->
    }

    public static function getWarehosueInventory(ProductRequestFormItem $product_request_form_item)
    {


        // چون برای بسته بندی های دو سطحی، سطح اول آن را هم بررسی می کنیم.
        $packing_type_ids = $product_request_form_item->product_request_form_packing_types()->pluck("packing_type_id")->toArray();
        // اگر بسته بندی سطح اول داشته باشد، سطح اول آن را هم انتخاب می کنیم.
        $first_packing_type_ids = PackingType::
        whereIn("id", $packing_type_ids)->
        whereNotNull("first_packing_type_id")->
        pluck("first_packing_type_id")->toArray();
        // اگر نوع بسته بندی، بوببین باشد، باید بسته بیندی های کارتن را هم انتخاب کنیم.
        $master_packing_type_ids = PackingType::
        whereIn("first_packing_type_id", $packing_type_ids)->
        pluck("id")->toArray();

        $packing_type_ids = array_merge($packing_type_ids, $first_packing_type_ids, $master_packing_type_ids);


        $packing_type_ids[] = -1;

        $value = [];
        $packing_forms_amount = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereIn("packing_type_id", $packing_type_ids)->
        where(["product_id" => $product_request_form_item->product_id])->
        //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
        where("packing_forms.warehouse_status_id", 4201)->
        where("packing_forms.status_id", 7007003)->
        groupBy("packing_forms.id")->
        addSelect(DB::raw("packing_forms.sub_packing_form_number,packing_forms.id,sum(final_amount) as final_amount"))->
        get();
        $amount_sum = 0;
        foreach ($packing_forms_amount as $packing_form_data) {
            if (isset($count_select[$packing_form_data->id]) && $count_select[$packing_form_data->id] > 0 && $packing_form_data->sub_packing_form_number > 0) {
                $amount_sum += $packing_form_data->final_amount * $count_select[$packing_form_data->id] / $packing_form_data->sub_packing_form_number;
            } else {
                $amount_sum += $packing_form_data->final_amount;
            }
        }

        $value["amount"] = round($amount_sum, 2);
        $value["count"] = count(PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where(["product_id" => $product_request_form_item->product_id])->
        whereIn("packing_type_id", $packing_type_ids)->
        //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
        where("packing_forms.warehouse_status_id", 4201)->
        where("packing_forms.status_id", 7007003)->
        groupBy("packing_form_id")->
        select("packing_form_id")->
        get());

        return $value;

        //addSelect( DB::raw( "count(packing_form_id) as count" ) )->
    }

    public static function getCurrentDelivery(ProductRequestFormItem $product_request_form_item)
    {


// وضعیت هایی که در آن تراکنش انبار ثبت شده است.
        $status_were_transaction_not_ok = $product_request_form_item->product_request_form->getCurrentExistFromForDashboard();


        $sum_amount = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        where("product_request_form_item_id", $product_request_form_item->id)->
        where("product_request_form_id", $product_request_form_item->product_request_form_id)->
        where("product_id", $product_request_form_item->product_id)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->
        sum("amount");


        // لیست بسته بندی هایی که اصلی هستند
        $packing_form_count = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        join("packing_form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        join("packing_forms", "packing_form_id", "packing_forms.id")->
        where("product_request_form_item_id", $product_request_form_item->id)->
        where("form_item.product_id", $product_request_form_item->product_id)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->
        whereNull("packing_form_master_id")->
        selectRaw("count(distinct(packing_form_id)) as count")->first();

        // لیست بسته بندی های اصلی که بسته بندی فرعی دارند.
        $packing_form_count_master = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        join("packing_form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        join("packing_forms", "packing_form_id", "packing_forms.id")->
        where("product_request_form_item_id", $product_request_form_item->id)->
        where("form_item.product_id", $product_request_form_item->product_id)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->
        whereNotNull("packing_form_master_id")->
        selectRaw("count(distinct(packing_form_master_id)) as count")->first();

        return [
            "sum_amount" => $sum_amount,
            "packing_form_count" => ($packing_form_count->count ?? 0) + ($packing_form_count_master->count ?? 0)
        ];
    }
    public static function getCurrentDeliveryRequest(ProductRequestForm $productRequestForm, $product_request_form_ids)
    {


// وضعیت هایی که در آن تراکنش انبار ثبت شده است.
        $status_were_transaction_not_ok = $productRequestForm->getCurrentExistFromForDashboard();

        // به تفکیک درخواست و
        $sum_amount = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        where("product_request_form_id", $productRequestForm->id)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->
        groupBy("product_request_form_item_id")->
        selectRaw("sum(amount) as amount ,product_request_form_item_id ")->
        pluck("amount","product_request_form_item_id")->toArray();
//            get();
        ;
        return $sum_amount;

    }

    public function get_exit_form_list_api(ProductRequestForm $product_request_form, $page)
    {
        return view($this->view_path . "_form_list", compact("product_request_form", "page"));
    }

    public function get_prf_list_api(ProductRequestForm $product_request_form, $page)
    {
        $selected_amount = [];

        $product_ids = [];
        $currentSelectedToExist = [];
        foreach ($product_request_form->items as $item) {
            if (!isset($currentSelectedToExist[$item->product_id])) {
                $currentSelectedToExist[$item->product_id] = 0;
            }
            $selected_amount[$item->id]["CurrentDelivery"] = DashboardController::getCurrentDelivery($item);

            $selected_amount[$item->id]["CurrentSelectedToExist"] = DashboardController::getCurrentSelectedToExist($item);
            $currentSelectedToExist[$item->product_id] += $selected_amount[$item->id]["CurrentSelectedToExist"]["amount"];

            $selected_amount[$item->id]["WarehouseInventory"] = null;
            $product_ids[] = $item->product_id;

        }

        // به دست آوردن جایگاه های هر کالا
        $shelving_packing_list = PackingFormItem::
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("packing_forms.status_id", 7007003)->
        whereNotNull("warehouse_shelving_id")->
        whereIn("product_id", $product_ids)->
        select("product_id", "warehouse_shelving_id")->
        get();

        $product_shelving_list = [];
        $shelving_ids = [];
        foreach ($shelving_packing_list as $item) {
            if (!isset($product_shelving_list[$item->product_id])) {
                $product_shelving_list[$item->product_id] = [];
            }
            $product_shelving_list[$item->product_id][] = $item->warehouse_shelving_id;
            $shelving_ids[] = $item->warehouse_shelving_id;
        }
        $shelving_ids[] = -1;
        $shelving_list = WarehouseShelving::whereIn("id", $shelving_ids)->pluck("id", "fullCode");


        $product_inventory = WarehouseProduct::getProductInventoryList($product_ids);

        $selected_amount["ShowWarehouseInventory"] = null;
        $post_user = Auth::user()->posts->first();
        $allow_select_product = 1; // ممکن است جایی فقط خواسته باشیم، طرف اطلاعات درخواست را مشاهده کند، بنابراین مقدار این متعیر را صفر می کنیم.
        $permission_confirm = $post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout");

        $product_request_form_items = ProductRequestFormItem::
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*")->with("product")->
        where("product_request_form_item.product_request_form_id", $product_request_form->id)->
        get();
        return view($this->view_path . "_prf_list", compact(
            "product_shelving_list", "shelving_list",
            "selected_amount", "product_request_form_items", "product_request_form", "page", "permission_confirm", "allow_select_product", "product_inventory", "currentSelectedToExist"));
    }

    public function get_index_rows_api(Request $request)
    {
        $product_request_form_ids = $request->product_request_form_ids;
        $order_by = $request->order_by;
        $firstItem = $request->firstItem;
        $currentPage = $request->currentPage;
        $product_request_form_ids = json_decode($product_request_form_ids);
        $product_request_form_ids[] = 0;
        $list = ProductRequestForm::
        whereIn("product_request_forms.id", $product_request_form_ids)->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        with("warehouse", "worker")->
        get();

        return view($this->view_path . "_index_rows", compact("list", "firstItem", "currentPage"));
    }
//
//    public function updateStatus() {
//        foreach ( ProductRequestForm::where( "status_id", 7005008 )->get() as $product_request_form ) {
//            //تغییر وضعیت فرم های درخواست
//            $status_id = 7005002; // تحویل (ارسال) شده
//
//            $form_list = $product_request_form->getExistFormForConfirmList();
//            if ( count( $form_list ) > 1 ) { // بعد از اینجا وضعیت فرم تعییر می کند
//                // اگر فرم دیگری برای تایید وجود دارد؟
//                $status_id = 7005004;
//            } else {
//                foreach ( $product_request_form->items as $prf_item ) {
//
//                    $product = $prf_item->product;
//
//                    $min = $prf_item->amount_request * $product->goods_kind->be_lower_in_confirm_exit_form / 100;
//                    if ( $prf_item->amount_remaining > $min ) {
//                        $status_id = 7005008; // در انتظار تحویل (ارسال) بافی مانده کالا
//                    }
//
//                }
//            }
//            $product_request_form->status_id = $status_id;
//            $product_request_form->save();
//        }
//    }


}
