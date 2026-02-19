<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormPackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Order\Order;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Mpdf\Tag\Del;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class   CustomerController extends Controller
{
    var $view_path = "warehouse.out.customer.";
    var $route_path = "wh.out.customer.";
    static $session_name = "customer_selected_packing_id";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_sales_customer");
            $order_by = session("order_by_sales_customer");
        }
        session(["search_sales_customer" => $search, "order_by_sales_customer" => $order_by]);

        $applicant_ids = ProductRequestFormItem::
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        select("applicant_id")->
        whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
        where("amount_remaining", ">", 0)->
        where("applicant_type_id", 30)->
        pluck("applicant_id","applicant_id")->toArray();
        $applicant_ids[] = -1;

        $customers = Order::
        when($search != "", function ($query) use ($search) {
            return $query->join("customers", "customers.id", "orders.customer_id")->
            where("customers.code", "like", "%" . $search . "%")
                ->orWhere("customers.caption", "like", "%" . $search . "%");

        })->
        whereIn("customer_id", $applicant_ids)->
        whereIn("orders.status_id", [35030, 35040, 35050])->pluck("customer_id")->
        toArray();

        $list = Customer::whereIn("id", $customers)->paginate(20);

        return view($this->view_path . "index", compact("list", "request", "search", "order_by"));
    }

    public function view(Request $request, Customer $customer)
    {


        $allow_select_product = 1; // ممکن است جایی فقط خواسته باشیم، طرف اطلاعات درخواست را مشاهده کند، بنابراین مقدار این متعیر را صفر می کنیم.

        $product_request_form_item_query = ProductRequestFormItem::
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*", "product_request_forms.code as product_request_form_code")->
        whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
        where("amount_remaining", ">", 0)->
        where("applicant_type_id", 30)->
        where("applicant_id", $customer->id)->

        with("product");

        $product_request_form_items = $product_request_form_item_query->get();

        if (count($product_request_form_items) == 0) {
            return back()->withErrors("مشتری هیچ درخواستی برای تحویل ندارد.");
        }

        $product_request_form_items_by_product_details = [];
        $product_request_form_ids = [];
        foreach ($product_request_form_items as $item) {
            if (!isset($product_request_form_items_by_product_details[$item->product_id])) {
                $product_request_form_items_by_product_details[$item->product_id] = [];
            }
            $product_request_form_items_by_product_details[$item->product_id][] = $item;
            $product_request_form_ids[$item->product_request_form_id] = $item->product_request_form_id;

        }
        $product_request_form = $product_request_form_items[0]->product_request_form;

        // لیست بسته بندی های انتخاب شده
        $current_selected_to_exit = self::getCurrentSelectedToExit($product_request_form, $product_request_form_ids);
        $all_selected_to_exit = [
            "amount" => 0,
            "number_packing_forms" => 0
        ];
        foreach ($current_selected_to_exit as $item) {

            $all_selected_to_exit["amount"] += $item["amounts"];
            $all_selected_to_exit["number_packing_forms"] += count($item["number_packing_forms"]);
        }

        // لیست بسته بندی های در حال تحویل
        $current_delivery = self::getCurrentDelivery($product_request_form, $product_request_form_ids);
        // لیست انواع بسته بندی های و درجه های مجاز
        $packing_types = self::getProductRequestForm($product_request_form_ids);

        // انتخاب یک درخواست که کالایی برای آن انتخاب شده است.
        if (count($current_selected_to_exit) > 0) {
            foreach ($product_request_form_items as $item) {
                if (isset($current_selected_to_exit[$item->product_id])) {
                    $product_request_form = $item->product_request_form;
                }
            }
        }
        $product_request_form_item_by_product = ProductRequestFormItem::
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        join("products", "products.id", "product_request_form_item.product_id")->
        orderBy("products.id")->
        orderBy("products.property1_caption")->
        orderBy("products.property2_caption")->
        select("product_request_form_item.*", "product_request_forms.code as product_request_form_code")->
        selectRaw("sum(amount_request) sum_amount_request , sum(amount_sent) as sum_amount_sent, sum(amount_remaining) as sum_amount_remaining")->
        whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
        whereIn("product_request_forms.id", $product_request_form_ids)->
        where("applicant_type_id", 30)->
        where("applicant_id", $customer->id)->
        where("amount_remaining", ">", 0)->

        with("product")->groupBy("product_id")->paginate(20);


        return view($this->view_path . "view", compact("all_selected_to_exit", "product_request_form", "packing_types", "current_delivery", "current_selected_to_exit", "product_request_form_items_by_product_details", "product_request_form_item_by_product", "product_request_form_items", "customer"));
    }


    /***
     * لیست بسته بندی های انتخاب شده جهت خروج
     */
    public static function getCurrentSelectedToExit(ProductRequestForm $product_request_form, $product_request_form_ids)
    {

        // محاسبه مقدار انتخاب شده جهت خروج
        $session_data = ProductRequestFormSessionData::
        getData($product_request_form, true, true);

        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }
        $count_select = isset($session_data["count_select"]) ? $session_data["count_select"] : [];
        $exit_amount_of_packing_form = isset($session_data["exit_amount_of_packing_form"]) ? $session_data["exit_amount_of_packing_form"] : [];


        $value = [];
        // لیست بسته بندی های انتخاب به تفکیک کالا-بسته بندی
        $selected_packing_forms = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("product_id", "packing_form_id")->
        addSelect(DB::raw("packing_forms.sub_packing_form_number,product_id,packing_form_id,sum(final_amount) as final_amount"))->
        get();

        $selected_list_by_product = [
        ];


        $amount_sum = 0;
        foreach ($selected_packing_forms as $s_item) {

            if (!isset($selected_list_by_product[$s_item->product_id])) {
                $selected_list_by_product[$s_item->product_id] = [
                    "number_packing_forms" => [],
                    "amounts" => 0,
                ];
            }
            $selected_list_by_product[$s_item->product_id]["number_packing_forms"][$s_item->packing_form_id] = $s_item->final_amount;
            $selected_list_by_product[$s_item->product_id]["amounts"] = array_sum(
                $selected_list_by_product[$s_item->product_id]["number_packing_forms"]
            );


        }
        return $selected_list_by_product;

    }

    /**
     * مقدار در حال تحویل به تفکیک کد کالا
     */
    public static function getCurrentDelivery(ProductRequestForm $product_request_form, $product_request_form_ids)
    {


// وضعیت هایی که در آن تراکنش انبار ثبت شده است.
        $status_were_transaction_not_ok = $product_request_form->getCurrentExistFromForDashboard();


        $list = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        join("packing_form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        whereIn("product_request_form_id", $product_request_form_ids)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->

        groupBy("form_item.product_id", "packing_form_item_id")->
        select("form_item.form_id", "form_item.product_id", "packing_form_item_id", "packing_form_id")->
        selectRaw("form_item.amount as amount")->
        get();

        $current_delivery_by_item = [];
        $current_delivery_by_product = [];
        foreach ($list as $item) {
            if (!isset($current_delivery_by_item[$item->product_request_form_id])) {
                $current_delivery_by_item[$item->product_request_form_id] = [];
            }
            if (!isset($current_delivery_by_item[$item->product_request_form_id][$item->product_id])) {
                $current_delivery_by_item[$item->product_request_form_id][$item->product_id] = [
                    "number_packing_forms" => [],
                    "amounts" => 0,
                ];
            }

            if (!isset($current_delivery_by_product[$item->product_id])) {
                $current_delivery_by_product[$item->product_id] = [
                    "number_packing_forms" => [],
                    "amounts" => 0,
                ];
            }

            $current_delivery_by_item[$item->product_request_form_id][$item->product_id]["number_packing_forms"][$item->packing_form_id] = $item->amount;
            $current_delivery_by_item[$item->product_request_form_id][$item->product_id]["amounts"] =
            array_sum($current_delivery_by_item[$item->product_request_form_id][$item->product_id]["number_packing_forms"]);

            $current_delivery_by_product[$item->product_id]["number_packing_forms"][$item->packing_form_id] = $item->amount;
            $current_delivery_by_product[$item->product_id]["amounts"] =array_sum($current_delivery_by_product[$item->product_id]["number_packing_forms"]);
        }

        return [
            "items" => $current_delivery_by_item,
            "products" => $current_delivery_by_product,
        ];

    }

    public static function getProductRequestForm($product_request_form_ids)
    {
        $list = ProductRequestFormPackingType::
        whereIn("product_request_form_id", $product_request_form_ids)->
        with("packing_type")->
        with("degree")->
        get();

        $packing_types = [];
        $degrees = [];
        foreach ($list as $item) {
            if (!isset($packing_types[$item->product_request_form_item_id])) {
                $packing_types[$item->product_request_form_item_id] = [];
            }
            if (!isset($degrees[$item->product_request_form_item_id])) {
                $degrees[$item->product_request_form_item_id] = [];
            }
            $packing_types[$item->product_request_form_item_id][] = $item->packing_type->caption;
            $degrees[$item->product_request_form_item_id][$item->degree_id] = $item->degree->caption;
        }

        return [
            "packing_types" => $packing_types,
            "degree" => $degrees,
        ];
    }

    /***
     * @param Customer $customer
     * @param $product_request_form_ids
     * @return void
     * چک کردن اینکه لیست درخواست ها می توانند با هم تحویل شوند یا خیر
     */
    public static function CheckProductRequestForm(Customer $customer, $product_request_form_ids)
    {

        $list = ProductRequestFormItem::
        join("order_factor", "order_factor.order_list_id", "product_request_form_item.order_list_id")->
        whereIn("product_request_form_id", $product_request_form_ids)->
        groupBy("product_id", "order_id")->
        select("order_id", "order_factor.id", "product_request_form_id", "product_request_form_item.product_id", "total_price_with_tax")->
        get();

        $product_order_price = [];
        foreach ($list as $item) {
            if (!isset($product_order_price[$item->product_id])) {
                $product_order_price[$item->product_id] = [
                    "product_request_form" => [],
                    "price" => null
                ];
            }

            if ($product_order_price[$item->product_id]["price"] == null) {
                $product_order_price[$item->product_id]["price"] = $item->total_price_with_tax;
                $product_order_price[$item->product_id]["product_request_form"][] = $item->product_request_form_id;
            }

            if (
                $item->total_price_with_tax != $product_order_price[$item->product_id]["price"]
            ) {
                $product_order_price[$item->product_id]["product_request_form"][] = $item->product_request_form_id;
            }
        }

        $product_request_not_allowed = [];
        foreach ($product_order_price as $item) {
            if (count($item["product_request_form"]) > 1) {
                $product_request_not_allowed[] = $item;
            }
        }
        return $product_request_not_allowed;
    }
}