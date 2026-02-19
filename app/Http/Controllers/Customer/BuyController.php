<?php

namespace App\Http\Controllers\Customer;

use App\Events\Order\OrderLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerAddress;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindDisplayProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderList;
use App\Models\Order\OrderListPackingType;
use App\Models\Order\OrderPaymentMethod;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function Termwind\renderUsing;

class BuyController extends Controller
{
    //customer/group/buy

    var $route_path = "customer_group.buy.";
    var $view_path = "customer.group.buy.";

    public function new_order()
    {

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();

        if (!isset($customer)) {
            return back()->withErrors("اطلاعات مشتری برای شما در سامانه وجود ندارد ");

        }

        if (!$customer->code || $customer->code == "") {
            return back()->withErrors("کد مرکز و کد تفضیلی برای مشتری ثبت نشده است، لطفا قبل از ثبت سفارش فرایند همکاری با ما را تکمیل نمایید.");
        }

        $customer_controller = new CustomerController();

        return $customer_controller->new_order_for_customer($customer);
    }

    public function complete_order(Customer $customer)
    {
        if (!$customer->code || $customer->code == "") {
            return back()->withErrors("کد مرکز و کد تفضیلی برای مشتری ثبت نشده است، لطفا قبل از ثبت سفارش فرایند همکاری با ما را تکمیل نمایید.");
        }
        $user_id = \Auth::user()->id;

        $list = Order::where([
            "register_user_id" => $user_id,
            "customer_id" => $customer->id,
            "status_id" => 304010,//  در انتظار تایید پیش نویس سفارش
        ])->get();

        return view($this->view_path . "complete_order", compact("list", "customer"));
    }

    public function edit_order(Order $order)
    {


        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        event(new OrderLogEvent($order, 304991));

        return redirect()->route($this->route_path . "index", $order);
    }

    public function index(Request $request, Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();
        $post_user = \Auth::user()->posts->first();
        $worker = Worker::find($user_id);
        // ثبت سفارش برای مشتری
        if ($post_user->getMenuPermission($worker, 1025)) {
            $customer = $order->customer;
        } else {
            if (!$customer) {
                return back()->withErrors("نوع کاربری شما از گروه مشتریان نمی باشد.");
            }
            if ($order->customer_id != $customer->id) {
                return back()->withErrors("سفارش نامعتبر می باشد");
            }

        }

        $result_tariff = $customer->valid_tariff();
        if (!$result_tariff["result"]) {
            return back()->withErrors($result_tariff["error"]);
        }

        $goods_kind_list = ProductTariff::
        join("products", "products.id", "product_id")->
        join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
        where([
            "products.active_status_id" => 1200,
            "possibility_of_sale" => 1,
            "tariff_id" => $customer->tariff_id,
        ])->
        groupBy("goods_kind_id")->
        select("goods_kinds.id", "goods_kinds.caption")->
        get();

        if (count($goods_kind_list) == 0) {
            return back()->withErrors("هیچ کالایی برای سفارش وجود ندارد.");
        }
        if (count($goods_kind_list) == 1) {
            return redirect()->route($this->route_path . "index_property", [$order, $goods_kind_list[0]->id]);
        }

        return view($this->view_path . "index", compact("goods_kind_list", "customer", "order"));

    }

    public function index_property(Request $request, Order $order, $goods_kind_id)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $goods_kind = GoodsKind::find($goods_kind_id);
        if (!$goods_kind) {
            return redirect()->route("dashboard");
        }

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();
        $post_user = \Auth::user()->posts->first();
        $worker = Worker::find($user_id);
        if ($post_user->getMenuPermission($worker, 1025)) {
            $customer = $order->customer;
        }

        $result_tariff = $customer->valid_tariff();
        if (!$result_tariff["result"]) {
            return back()->withErrors($result_tariff["error"]);
        }

        if ($request->isMethod('post')) {
            $search = $request->search;

        } else {
            $search = session("search_product_property");

        }
        session([
            "search_product_property" => $search,
        ]);

        //####################################################
        $goods_kind_display_property = GoodsKindDisplayProperty::where("goods_kind_id", $goods_kind->id)->first();
//        if (!$goods_kind_display_property) {
//            return back()->withErrors("نوع مشخصه رسته کالایی جهت دسته بندی مشخص نشده است، لطفا با پشتیبانی تماس بگیرید");
//        }

        $property_list = GoodsKindPropertyValue::
        join("product_tariff", "product_tariff.product_id", "goods_kind_property_values.product_id")->
        where([
            "tariff_id" => $customer->tariff_id,

        ])->
        where("goods_kind_property_id", $goods_kind_display_property->goods_kind_property_id ?? -1)->
        when($search != "", function ($query) use ($search) {

            return $query->where("value", "like", "%" . $search . "%");

        })->
        select("goods_kind_property_values.id", "value")->
        groupBy("value")->get()->toArray();

        // اگر نوع مشخصه از نوع چند گزینه ای باشد، می بایست مقدار مشخه ها را نمایش دهد نه مقدار id را
        $property_list_values_option = [];
        if ($goods_kind_display_property && $goods_kind_display_property->goods_kind_property && $goods_kind_display_property->goods_kind_property->field_type_id == 3) {
            $property_list_values = array_column($property_list, 'value');
            $property_list_values[] = -1;
            $property_list_values_option = GoodsKindPropertyOption::whereIn("id", $property_list_values)->pluck("caption", "id");
        }

        if ($search == "") {
            if (count($property_list) == 0) {
                //return back()->withErrors("هیچ گروه کالایی برای سفارش وجود ندارد.");
                return redirect()->route($this->route_path . "index_product", [$order, 0]);
            }
            if (count($property_list) == 1) {
                return redirect()->route($this->route_path . "index_product", [$order, $property_list[0]["id"]]);
            }
        }

        return view($this->view_path . "index_property", compact("search", "customer", "order", "customer", "property_list", "goods_kind_display_property", "property_list_values_option"));

    }

    public function index_product(Request $request, Order $order, $goods_kind_property_value_id)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $goods_kind_property_value = 0;
        $goods_kind_property_option_value = null;
        if ($goods_kind_property_value_id != 0) {
            $goods_kind_property_value = GoodsKindPropertyValue::find($goods_kind_property_value_id);
            if (isset($goods_kind_property_value->property) && $goods_kind_property_value->property->field_type_id == 3) {
                $goods_kind_property_option_value = GoodsKindPropertyOption::where("id", $goods_kind_property_value->value ?? 0)->first();
                $goods_kind_property_option_value = $goods_kind_property_option_value->caption ?? null;
            }
        }

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();
        $post_user = \Auth::user()->posts->first();
        $worker = Worker::find($user_id);
        // ثبت سفارش برای مشتری
        if ($post_user->getMenuPermission($worker, 1025)) {
            $customer = $order->customer;
        }

        $is_valid = $customer->valid_tariff();
        if ($is_valid != true) {
            return back()->withErrors($is_valid);
        }


        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $factory_id = $request->factory_id;
            $product_type_id = $request->product_type_id;
            $goods_kind_id = $request->goods_kind_id;
        } else {
            $search = session("search_group_customer");
            $factory_id = session("factory_id_group_customer");
            $order_by = session("order_by_group_customer");
            $product_type_id = session("product_type_id_group_customer");
            $goods_kind_id = session("goods_kind_id_group_customer");
        }
        session([
            "search_group_customer" => $search,
            "order_by_group_customer" => $order_by,
            "factory_id_group_customer" => $factory_id,
            "product_type_id_group_customer" => $product_type_id,
            "goods_kind_id_group_customer" => $goods_kind_id
        ]);

        $query = ProductTariff::
        join("products", "products.id", "product_tariff.product_id")->
        where([
            "active_status_id" => 1200,
            "possibility_of_sale" => 1,
            "tariff_id" => $customer->tariff_id,
        ])->
        when($goods_kind_property_value, function ($query) use ($goods_kind_property_value) {
            return $query->
            join("goods_kind_property_values", "products.id", "goods_kind_property_values.product_id")->
            where([
                "value" => $goods_kind_property_value->value,
                "products.goods_kind_id" => $goods_kind_property_value->property->goods_kind_id,
            ]);
        })->
        when($factory_id != 0, function ($query) use ($factory_id) {
            $query->where("factory_id", $factory_id);
        })->
//        when( $product_type_id != 0, function ( $query ) use ( $product_type_id ) {
//            $query->where( "product_type_id", $product_type_id );
//        } )->
//        when($goods_kind_id != 0, function ($query) use ($goods_kind_id) {
//            $query->where("goods_kind_id", $goods_kind_id);
//        })->
        when($search != "", function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where("caption", "like", "%" . $search . "%");
                $query->Orwhere("code", "like", "%" . $search . "%");
            });

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        });

        // چک کردن اینکه همه کالاها واحد اصلی آنها معتبر است.
        $product_ids = $query->pluck("products.id", "products.id")->toArray();
        $product_not_unit = Product::
        whereIn("id", $product_ids)->
        where("unit_id", 0)->first();
        if ($product_not_unit) {
            return back()->withErrors("اطلاعات اولیه " . $product_not_unit->caption . " کامل نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.");
        }

        $list = $query->
        select("product_tariff.id", "product_tariff.product_id", "tariff_id", "product_tariff.service_id")->
        groupBy("products.id")->
        paginate(50);
        $order_by_Option = Option::OrderBy("customer_buy", $order_by);
//return $list[0]->getProductTariffWithMasterDegree();
        ###########################################################
        // $product_type_option = Option::get( "product_type_tariff", $product_type_id, $customer->tariff_id );
        $goods_kind_option = Option::get("goods_kind_tariff", $goods_kind_id, $customer->tariff_id);

        return view($this->view_path . "index_product", compact("order_by_Option", "goods_kind_property_value", "customer", "order", "customer", "list", "goods_kind_option", "order_by", "search", "goods_kind_property_option_value"));

    }

    public function show_shopping_product(Order $order, Customer $customer, Product $product)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }


        $tariff_product_list_query = ProductTariff::where([
            "tariff_id" => $customer->tariff_id,
            "product_id" => $product->id
        ]);
        foreach (ProductTariff::GroupItemList() as $item) {
            $tariff_product_list_query = $tariff_product_list_query->groupBy($item);
        }
        $tariff_product_list = $tariff_product_list_query->get();
        if (count($tariff_product_list) == 0) {
            return back()->withErrors("کالا در تعرفه تعریف شده برای شما وجود ندارد.");
        }
        //  return $tariff_product_list[0];
        ########################################################### OrderList

        $orderListPluck = OrderList::join("order_list_packing_type", "order_list.id", "order_list_id")->
        where([
            "order_list.order_id" => $order->id,
            "order_list.order_kind_id" => 1,
            "order_list.product_id" => $product->id
        ])->
        addSelect(DB::raw("concat(degree_id,'_',product_id,'_',packing_type_id) as degree_packing_type, carton"))->
        pluck("carton", "degree_packing_type");

        $goods_kind_display_property = GoodsKindDisplayProperty::where("goods_kind_id", $product->goods_kind_id)->first();

        $goods_kind_property_value = GoodsKindPropertyValue::where([
            "product_id" => $product->id,
            "goods_kind_property_id" => $goods_kind_display_property->goods_kind_property_id ?? 0
        ])->first();

        if ($goods_kind_property_value) {
            $goods_kind_property_value = $goods_kind_property_value->id;
        } else {
            $goods_kind_property_value = 0;
        }

        // مقدار کالا به تفکیک بسته بندی
        $result = PackingForm::GetInventoryBuyPackingType($product);
        $packing_type_caption = $result["packing_type_caption"];

        //جمع مجوز های باقی مانده
        $product_request_form_remaining = Product\ProductRequest\ProductRequestForm::ProductRequestFormAmount($product);
        return view($this->view_path . "show_shopping_product", compact("product", "product_request_form_remaining", "packing_type_caption", "customer", "order", "tariff_product_list", "orderListPluck", "goods_kind_property_value"));

    }

    public function add_to_shopping_cart(Request $request, Order $order, Customer $customer, Product $product)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result_add_product = self::PostAddToShoppingCard($order, $product, $customer, $request->data);
        if (!$result_add_product["result"]) {
            return back()->withErrors($result_add_product["error"]);
        }


        $goods_kind_display_property = GoodsKindDisplayProperty::where("goods_kind_id", $product->goods_kind_id)->first();


        $goods_kind_property_value = GoodsKindPropertyValue::where([
            "product_id" => $product->id,
            "goods_kind_property_id" => $goods_kind_display_property->goods_kind_property_id ?? 0
        ])->first();

        if ($goods_kind_property_value) {
            $goods_kind_property_value = $goods_kind_property_value->id;
        } else {
            $goods_kind_property_value = 0;
        }


        return redirect()->route($this->route_path . "index_product", [
            $order,
            $goods_kind_property_value
        ])->with(["success" => "کالا ها با موفقیت به سبد خرید اضافه شدند."]);

    }

    public function remove_from_shopping_cart(Request $request, Order $order, Customer $customer, Product $product)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result_add_product = self::PostAddToShoppingCard($order, $product, $customer, null, true);
        if (!$result_add_product["result"]) {
            return back()->withErrors($result_add_product["error"]);
        }

//
//        $goods_kind_display_property = GoodsKindDisplayProperty::where("goods_kind_id", $product->goods_kind_id)->first();
//
//
//        $goods_kind_property_value = GoodsKindPropertyValue::where([
//            "product_id" => $product->id,
//            "goods_kind_property_id" => $goods_kind_display_property->goods_kind_property_id
//        ])->first();
//
//        if ($goods_kind_property_value) {
//            $goods_kind_property_value = $goods_kind_property_value->id;
//        } else {
//            $goods_kind_property_value = 0;
//        }


        return back()->with(["success" => "کالا ها با موفقیت از سبد خرید حذف شد."]);

    }

    public static function PostAddToShoppingCard(Order $order, Product $product, Customer $customer, $data, $delete_product = false)
    {

//        "data": {
//          "63198": "150",
//          "63285": "202",
//          "packing_type": {
//              "63198": [
//                  "50"
//              ],
//              "63285": [
//                "50"
//              ]
//    }
        $error_message = "";
        $k = 0;
        if ($data && count($data) > 0) {

// حذف کل رکورد های مربوط به کالا
            OrderList::where([
                "order_id" => $order->id,
                "product_id" => $product->id,
            ])->delete();

            $productTariff_list = ProductTariff::where(
                [
                    "product_id" => $product->id,
                    "tariff_id" => $customer->parent_id ? $customer->parent->tariff_id : $customer->tariff_id
                ])->get();
            $error_message = "";

            $k = 0; // تعداد سطر هایی که اضافه شده است.
            foreach ($productTariff_list as $item) {

                if (!isset($data[$item->id]) || $data[$item->id] == 0) {
                    continue;
                }
                //چک کردن  نوع فروش کالا در تعرفه و مشخصات کالا
                $type_of_sale_product_product = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where([
                    "product_id" => $product->id,
                    "type_of_sale_of_product_id" => $item->type_of_sale_of_product_id
                ])->first();
                if (!$type_of_sale_product_product) {
                    $error_message .= "نوع " . $item->type_of_sale_of_product->caption . " برای کالا تعریف نشده است. ";
                    continue;
                }
                $carton = $data[$item->id];
                // چک کردن مقدار
                if ($item->degree->degree_type_id == 1 &&
                    ($item->min_buy > $carton || $item->max_buy < $carton)) {

                    $error_message .= "حداقل و حداکثر خرید برای   " . $item->degree->caption . " - " . $item->packing_type->caption . "  رعایت نشده است" . "<br/>";
                    continue;
                }


                if ($item->degree->degree_type_id == 2) {
                    // دریافت موجودی قابل فروش محصول-درجه
                    // آیا موجودی انبار در حداکثر سفارش مشتری تاثیر گذار باشد؟
                    $effective_inventory_in_order = Setting::find(11)->integer_value;
                    $inventory =
                        $product->getInventory($item->degree->id) -
                        $product->getReserveAmount($item->degree->id);
                    if ($effective_inventory_in_order && $inventory < $carton && $item->degree->degree_type_id == 2) {
                        $error_message .= "مقدار مورد درخواست برای این درجه از کالا (" . $item->degree->caption . ") بیش از مقدار قابل سفارش است. " . "<br/>";
                        continue;
                    }
                    if ($item->min_buy > $carton || $item->max_buy < $carton) {

                        $error_message .= "حداقل و حداکثر خرید  برای این درجه از کالا (" . $item->degree->caption . " - " . $item->packing_type->caption . ") رعایت نشده است" . "<br/>";
                        continue;
                    }
                }

                if (!isset($data["packing_type"][$item->id]) || count($data["packing_type"][$item->id]) == 0) {
                    $error_message .= "لطفا برای هر ردیف حداقل یک نوع بسته بندی انتخاب نمایید.";
                }

                // بررسی خدمت و کالا
                if ($type_of_sale_product_product->type_of_sale_of_product_id == 2) {
                    if (!$item->service_id) {
                        $error_message .= "با توجه به اینکه فروش  " . $product->fullCaption() . " به صورت کارمزدی می باشد، کد خدمت  برای کالا نامعتبر است.";
                    }
                    if (!$item->service_id || $type_of_sale_product_product->service_id != $item->service_id) {
                        $error_message .= "با توجه به اینکه فروش  " . $product->fullCaption() . " به صورت کارمزدی می باشد،  خدمت " . $item->service->fullCaption() . " برای کالا نامعتبر است.";
                    }
                }

                $orderList = OrderList::Create([
                    "order_id" => $order->id,
                    "product_id" => $product->id,
                    "service_id" => $item->service_id,
                    "degree_id" => $item->degree_id,
                    "order_kind_id" => 1, // بدون تخفیف
                    "type_of_sale_of_product_id" => $item->type_of_sale_of_product_id // نوع فروش کالا(عادی، کارمزدی)
                ]);


                foreach ($data["packing_type"][$item->id] as $packing_type_id) {
                    OrderListPackingType::Create([
                        "order_id" => $order->id,
                        "order_list_id" => $orderList->id,
                        "packing_type_id" => $packing_type_id
                    ]);
                }


                $orderList->customer_id = $customer->id;
                $orderList->wherehouse_id = $item->wherehouse_id;
                $orderList->priority_id = $customer->priority_id;
                $orderList->erp_status_id = 304;
                $orderList->status_id = 304;
                $orderList->order_type_id = $customer->order_type_id;


                $orderList->carton = $carton;
                $orderList->amount_remaining = $carton;
                $orderList->number_in_carton = $item->product->number_in_carton;
                $orderList->amount = $item->product->number_in_carton * $orderList->carton;

                // کد رهگیری اگر ست شده بود
                if (isset($data["tracking_code1"][$item->id])) {
                    $orderList->tracking_code1 = $data["tracking_code1"][$item->id];
                }
                if (isset($data["tracking_code2"][$item->id])) {
                    $orderList->tracking_code2 = $data["tracking_code2"][$item->id];
                }


                $orderList->save();
                $k++;
            }

        } elseif ($delete_product) {
            OrderList::where([
                "order_id" => $order->id,
                "product_id" => $product->id,
            ])->delete();
        } else {
            $error_message = "کد کالا در تعرفه تعریف نشده است.";
        }

        if ($error_message != "") {
            return [
                "result" => false,
                "error" => $error_message
            ];

        }
        return [
            "result" => true,
            "order_added" => $k
        ];
    }

    public function shopping_cart(Order $order, $selling_type_id = null)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        // مشخص کردن نوع فاکتور
        $order = Order::SellingTypeRefresh($order);

        $result_refresh = $order->factorRefresh();
        if (!$result_refresh["result"]) {
            return back()->withErrors($result_refresh["error"]);
        }

        $order->updatePercentOff();

        $order = Order::find($order->id);

        $order->updatePrice();

        $order = Order::find($order->id);


        $province_option = Option::get("province", $order->address->province_id ?? 0);
        $country_option = Option::get("country", $order->address->country_id ?? 112);
        $change_selling_type = true;


        $result_consumed_product = self::GetConsumeProductFinal($order);
        // در صورتی مشتری که نوع فروش کارمزدی را انتخاب کرده باشد، لیست کالاهایی که باید برای کارفرما ارسال کند را با توجه به تنظیمات هر کالا از مشتری دریافت می کند.
        $consumed_product_list = $result_consumed_product["consumed_product_list"];
        $consumed_product_list_final = $result_consumed_product["consumed_product_list_final"]; // ممکن است، ماده اولیه ای که به صورت امانی دریافت می شود، خودش را مستقیما دریافت نکنیم و مواد اولیه آن را دریافت کنیم ( مثل نخ و کلاف در رزنگ)
        $has_order_consumed_product = $result_consumed_product["has_order_consumed_product"];
        $contractor_supply_type_option = $result_consumed_product["contractor_supply_type_option"];


        return view($this->view_path . "shopping_card", compact("change_selling_type",
            "order", "country_option", "province_option", "has_order_consumed_product",
            "consumed_product_list", "consumed_product_list_final", "contractor_supply_type_option",
        ));

    }


    public function shopping_cart_submit(Request $request, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        if ($order->orderList()->count() == 0) {
            return back()->withErrors("لطفا حداقل یک محصول در سبد خرید انتخاب کنید.");
        }

        $consumed_product_list_final=[];
        if ($request->has_order_consumed_product) { // اگر مشخصات نوع تامین دارد.
            // ثبت مشخصات نوع تامین
            $result_consumed_product = self::GetConsumeProductFinal($order,$request , "submit");

            if (!$result_consumed_product["result"]) {
                return back()->withErrors(["error"]);
            }
            $consumed_product_list_final=$result_consumed_product["consumed_product_list_final"];
        }
        // بررسی اینکه اگر نوع فروش کارمزدی باشد، آیا نوع تامین برای همه موارد ثبت شده است یا خیر
        foreach ($order->orderList as $order_list) {
            if ($order_list->type_of_sale_of_product_id == 2) { // فروش کارمزدی
if(!isset($consumed_product_list_final[$order_list->id])){
    return back()->withErrors("با توجه به اینکه کالاهای موجود در سبد خرید، داری کالای امانی است، تب کالای مصرفی به درستی تکمیل نشده است، لطفا از صحت تعریف کالا اطمینان حاصل کنید،");
}
                foreach ($consumed_product_list_final[$order_list->id] as $item) {
                    $order_consumed_product_exists = OrderConsumedProduct::where([
                        "order_id" => $order->id,
                        "order_list_id" => $order_list->id,
                        "material_id" => $item->material_id,
                    ])->exists();
                    if (!$order_consumed_product_exists) {
                        return back()->withErrors("لطفا مشخصات نوع تامین ثبت کنید.");
                    }
                }
            }
        }


        $allSellingAmount = $order->customer->getAllSellingAmount();
        $informal_percent = $allSellingAmount["selling_type"][2]["percent"];

        //
        if ($request->selling_type_id == 2 && $informal_percent > $order->customer->percent_max_informal_purchase) {
            return back()->withErrors("مشتری گرامی سقف خرید غیر رسمی شما تکمیل می باشد، امکان ثبت این پیش فاکتور به صورت غیر رسمی امکان پذیر نیست.");
        }

        $order->customer->getAllSellingAmount();

        // بروز رسانی سری سفارش
        //
        $sale_series = Setting::getIntegerValue("sale_series_type_" . $order->selling_type_id);
        if (!$sale_series || $sale_series < 1) {
            return back()->withErrors(" سری سفارش به درستی در تنظیمات فروش ثبت نشده است.");
        }
        Order::UpdateCode($order, $sale_series);


        $order->updatePercentCash();

        $order->updateRoundOff();

        $order->updatePrice();

        $order = Order::find($order->id);


        if ($request->has_order_consumed_product) { // اگر مشخصات نوع تامین دارد.

            $orderConsumedProduct = OrderConsumedProduct::where("order_id", $order->id)->orderBy("id")->first();
            if ($orderConsumedProduct) {
                return redirect()->route($this->route_path . "order_packing_form.index", [$order, $orderConsumedProduct]);
            }
        }
        return redirect()->route($this->route_path . "address", $order);

    }

// ثبت نوع تامین برای مواد اولیه کارمزی
    public static function SubmitConsumedProduct(Request $request, Order $order)
    {

        $order_consumed_product_list = [];
        $first_material_id = null;
        foreach ($order->orderList as $order_list) {

            if ($order_list->type_of_sale_of_product_id == 2) { // فروش کارمزدی

                if (!$first_material_id) {
                    $first_material_id = $order_list->material_id;
                }
                $consumed_product_list = Product\ConsumedProduct\ConsumedProduct::where([
                    "product_id" => $order_list->product_id,
                    "in_ordering_customer_can_choose" => 1
                ])->get();


                foreach ($consumed_product_list as $item) {
                    $key = "consumed_" . $order_list->id . "_" . $item->material->id;
                    if (!$request->$key) {
                        return [
                            "result" => false,
                            "error" => "لطفا برای همه  مواد اولیه ها، مشخصات نوع تامین را تعیین کنید."
                        ];

                    }

                    $order_consumed_product_list[] = [
                        "order_id" => $order->id,
                        "order_list_id" => $order_list->id,
                        "product_id" => $item->product->id,
                        "material_id" => $item->material->id,
                        "contractor_supply_type_id" => $request->$key
                    ];
                }
            }
        }




        return [
            "result" => true,
        ];

    }

    /**
     * @param Order $order
     * @param $request
     * @param $submit_consumed_product // اگر لازم است مقدار ها را ذخیره می کنیم.
     * @return array
     */
    public static function GetConsumeProductFinal(Order $order, $request = null, $call_type = "option")
    {
        /**
         * $call_type == "Option" فقط برای کبوباک
         * $call_type == "submit" برای ثبت
         *
         */
        $consumed_product_list = [];
        $consumed_product_list_final = [];
        $has_order_consumed_product = false;
        $contractor_supply_type_option = [];
        $order_consumed_product_list = [];

        foreach ($order->orderList as $order_list) {
            if ($order_list->type_of_sale_of_product_id == 2) { // فروش کارمزدی
                $consumed_product_list[$order_list->id] = Product\ConsumedProduct\ConsumedProduct::where([
                    "product_id" => $order_list->product_id,
                    "in_ordering_customer_can_choose" => 1 // امکان انتخاب روش ارسال کالای مصرفی به مشتری داده شود؟
                ])->
                with("material")->
                get();
                $has_order_consumed_product = true;

                foreach ($consumed_product_list[$order_list->id] as $item) {

                    if ($item->material->supply_type_id == 4) {

                        $order_consumed_product = OrderConsumedProduct::where([
                            "order_id" => $order->id,
                            "order_list_id" => $order_list->id,
                            "material_id" => $item->material_id,
                        ])->first();

                        if ($call_type == "option") {
                            $contractor_supply_type_option[$order_list->id][$item->material_id] = Option::get("contractor_supply_types", $order_consumed_product->contractor_supply_type_id ?? 0);
                        } elseif ($call_type == "submit") {

                            $key = "consumed_" . $order_list->id . "_" . $item->material->id;
                            if (!$request->$key) {
                                return [
                                    "result" => false,
                                    "error" => "لطفا برای همه  مواد اولیه ها، مشخصات نوع تامین را تعیین کنید."
                                ];

                            }

                            $order_consumed_product_list[] = [
                                "order_id" => $order->id,
                                "order_list_id" => $order_list->id,
                                "product_id" => $item->product->id,
                                "material_id" => $item->material->id,
                                "contractor_supply_type_id" => $request->$key
                            ];


                        }


                        $consumed_product_list_final[$order_list->id][$item->material_id] = $item;


                    } else {
                        // ممکن است ماده اولیه که به صورت امانی دریافت می شود، خودش تولید داخل باشد
                        // در این حالت در BOM کالا می گردیم و کالایی را پیدا می کنیم و که نوع تامین آن تحویل امانی باشد.
                        $list_bom_where_supply_type_id_is_4 = Product\ConsumedProduct\ConsumedProduct::
                        join("products", "products.id", "material_id")->
                        where([
                            "product_id" => $item->material_id,
                            "supply_type_id" => 4,
                            "in_ordering_customer_can_choose" => 1 // امکان انتخاب روش ارسال کالای مصرفی به مشتری داده شود؟
                        ])->
                        with("material")->
                        get();


                        foreach ($list_bom_where_supply_type_id_is_4 as $item_supply_4) {
                            $order_consumed_product = OrderConsumedProduct::where([
                                "order_id" => $order->id,
                                "order_list_id" => $order_list->id,
                                "material_id" => $item_supply_4->material_id,
                            ])->first();

                            if ($call_type == "option") {

                                $contractor_supply_type_option[$order_list->id][$item_supply_4->material_id] = Option::get("contractor_supply_types", $order_consumed_product->contractor_supply_type_id ?? 0);

                            } elseif ($call_type == "submit") {


                                $key = "consumed_" . $order_list->id . "_" . $item_supply_4->material->id;
                                if (!$request->$key) {
                                    return [
                                        "result" => false,
                                        "error" => "لطفا برای همه  مواد اولیه ها، مشخصات نوع تامین را تعیین کنید."
                                    ];

                                }

                                $order_consumed_product_list[] = [
                                    "order_id" => $order->id,
                                    "order_list_id" => $order_list->id,
                                    "product_id" => $item_supply_4->product->id,
                                    "material_id" => $item_supply_4->material->id,
                                    "contractor_supply_type_id" => $request->$key
                                ];


                            }


                            $consumed_product_list_final[$order_list->id][$item_supply_4->material_id] = $item_supply_4;
                        }
                    }
                }
            }
        }

        if($call_type=="submit"){
            // ذخیر نوع تامین برای فروش
            OrderConsumedProduct::where("order_id", $order->id)->whereNull("form_general_item_id")->delete();
            OrderConsumedProduct::insert($order_consumed_product_list);
        }
        return [
            "result" => true,
            "consumed_product_list" => $consumed_product_list,
            "consumed_product_list_final" => $consumed_product_list_final,
            "contractor_supply_type_option" => $contractor_supply_type_option,
            "has_order_consumed_product" => $has_order_consumed_product,
            "order_consumed_product_list" => $order_consumed_product_list
        ];
    }

    public
    function address(Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result_refresh = $order->factorRefresh();
        if (!$result_refresh["result"]) {
            return back()->withErrors($result_refresh["error"]);
        }

        // بررسی اینکه اگر نوع تامین مواد اولیه ارسال پس از درخواست پیمانکار می باشد، حتما یک فرم را تکمیل کرده باشند.
        $order_consumed_list = OrderConsumedProduct::where("order_id", $order->id)->
        where("contractor_supply_type_id", 1)->
        groupBy("material_id")->
        get();

        foreach ($order_consumed_list as $order_consumed_product) {
            $order_consumed_list_item = OrderConsumedProduct::where("order_id", $order->id)->
            where("contractor_supply_type_id", 1)->
            where("material_id", $order_consumed_product->material_id)->
            whereNotNull("form_general_item_id")->
            first();
            if (!$order->customer->get_packing_form_details && $order_consumed_list_item && !$order_consumed_list_item->form_general_item_id) {
                return back()->withErrors(" با توجه به اینکه نوع ارسال مواد اولیه (" . $order_consumed_product->material->caption . ") ارسال پس از درخواست پیمانکار می باشد، لطفا اطلاعات مواد اولیه را تکمیل نمایید. ");


            }

        }

        $order->updatePercentOff();

        $order->updatePrice();

        $order = Order::find($order->id);

        $order->updateFormalOff();

        $order->updateRoundOff();

        $order->updatePrice();
        $order = Order::find($order->id);


        $address_list =
            Order::
            where("customer_id", $order->customer_id)->
            select("address_id", "orders.id")->
            groupBy("address_id")->
            get();


        $province_option = Option::get("province", $order->address->province_id ?? 0);
        $country_option = Option::get("country", $order->address->country_id ?? 112);
        $change_selling_type = true;

        return view("customer.group.buy.address", compact("change_selling_type", "address_list", "order", "country_option", "province_option"));

    }

    public
    function address_submit(Request $request, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        if ($order->orderList()->count() == 0) {
            return back()->withErrors("لطفا حداقل یک محصول در سبد خرید انتخاب کنید.");
        }


        ############# Address
        if ($request->address_id != 0) {
            $order->address_id = $request->address_id;
            $order->save();
        } else {
            if (
                $request->country_id == 0 ||
                $request->province_id == 0 ||
                $request->city_name == null ||
                $request->phone == null ||
                $request->mobile == null ||
                $request->postal_code == null ||
                $request->address == null) {
                return back()->withErrors("لطفا اطلاعات آدرس را به صورت کامل وارد نمایید.");
            }
            $address = Address::create($request->all());
            $order->address_id = $address->id;
        }

        $order->save();


        return redirect()->route("customer_group.buy.payment_method_step1", $order);

    }

    public
    function address_edit(Order $order, Address $address)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $address_exist =
            Order::
            where("customer_id", $order->customer_id)->
            where("address_id", $address->id)->
            first();

        if (!$address_exist) {
            return back()->withErrors("مجوز تغییر در آدرس مورد نظر برای شما وجود ندارد.");
        }
        $province_option = Option::get("province", $address->province_id ?? 0);
        $country_option = Option::get("country", $address->country_id ?? 112);


        return view("customer.group.buy.address_edit", compact("order", "address", "province_option", 'country_option'));

    }

    public
    function address_edit_submit(Request $request, Order $order, Address $address)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $address_exist =
            Order::
            where("customer_id", $order->customer_id)->
            where("address_id", $address->id)->
            first();

        if (!$address_exist) {
            return back()->withErrors("مجوز تغییر در آدرس مورد نظر برای شما وجود ندارد.");
        }
        $address->update($request->all());

        return redirect()->route("customer_group.buy.payment_method_step1", $order);
    }


    public
    function payment_method_step1(Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $order->factorRefresh();

        $order->updatePercentOff();

        $order->updatePrice();

        $order = Order::find($order->id);

        $order->updateFormalOff();

        $order->updateRoundOff();

        $order->updatePrice();

        $order = Order::find($order->id);

        $customer_payment_method = CustomerPaymentMethod::join("payment_method_types", "payment_method_type_id", "payment_method_types.id")->
        where([
            "customer_id" => $order->customer_id
        ])->
        orderBy("payment_method_type_id")->
        get();


        $list_sum = OrderFactor::where("order_id", $order->id)->
        join("products", "products.id", "product_id")->
        selectRaw("sum(total_price_with_tax) as total_price_with_tax , sum(carton* weight) as carton_weight")->
        first();

        $sum_order_factor_with_tax = $list_sum->total_price_with_tax;
        //$sum_carton_weight=$list_sum->carton_weight;
        if ($sum_order_factor_with_tax <= 0) {
            return back()->withErrors("مبلغ نهایی فاکتور صفر شده است، لطفا مبلغ و مقدار خرید را بررسی بفرمایید.");
        }
        $sum_order_factor_with_tax_remaining = $sum_order_factor_with_tax;

        $order_payment_method = OrderPaymentMethod::where("order_id", $order->id)->pluck("amount", "payment_method_type_id");
        $order_payment_method_value = [];
        foreach ($customer_payment_method as $item) {

            if (isset($order_payment_method[$item->payment_method_type_id])) {
                $order_payment_method_value[$item->payment_method_type_id] = $order_payment_method[$item->payment_method_type_id];
            } else {
                $order_payment_method_value[$item->payment_method_type_id] = round(min($sum_order_factor_with_tax_remaining, $sum_order_factor_with_tax * $item->max_percentage / 100));
            }

            $sum_order_factor_with_tax_remaining -=
                $order_payment_method_value[$item->payment_method_type_id];

        }


        return view("customer.group.buy.payment_method_step1", compact("order", "sum_order_factor_with_tax", "customer_payment_method", "order_payment_method_value"));
    }

    public
    function payment_method_step1_submit(Request $request, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $order->delivery_datetime = $request->delivery_datetime;
        $order->save();


        // ذخیره اطلاعات مبلغ پیش پرداخت
        $customer_payment_method = CustomerPaymentMethod::join("payment_method_types", "payment_method_type_id", "payment_method_types.id")->
        where([
            "customer_id" => $order->customer_id
        ])->
        orderBy("payment_method_type_id")->
        get();


        $sum_order_factor_with_tax = OrderFactor::where("order_id", $order->id)->sum("total_price_with_tax");
        $sum_order_factor_with_tax = round($sum_order_factor_with_tax);
        $sum_order_factor_with_tax_remaining = $sum_order_factor_with_tax;

        OrderPaymentMethod::where("order_id", $order->id)->update(["amount" => -1]);
        $error = "";

        foreach ($customer_payment_method as $item) {

            $id = "payment_method_" . $item->payment_method_type_id;
            if ($request->$id) {
                $value = $request->$id;
            } else {
                $value = 0;
            }
            $sum_order_factor_with_tax_remaining -= $value;

            $order_payment_method = OrderPaymentMethod::where([
                "order_id" => $order->id,
                "customer_id" => $order->customer_id,
                "payment_method_type_id" => $item->payment_method_type_id
            ])->first();
            if ($order_payment_method) {

                $order_payment_method->update([
                    "amount" => $value
                ]);
            } else {
                OrderPaymentMethod::create([
                    "order_id" => $order->id,
                    "customer_id" => $order->customer_id,
                    "payment_method_type_id" => $item->payment_method_type_id,
                    "amount" => $value,
                ]);
            }


            if ($value > $sum_order_factor_with_tax * $item->max_percentage / 100 || $value < $sum_order_factor_with_tax * $item->min_percentage / 100) {
                $error .= " حد مجاز برای مبلغ " . $item->caption . " رعایت نشده است." . "<br/>";
            }
        }


        OrderPaymentMethod::where("order_id", $order->id)->where(["amount" => -1])->delete();
        if ($error != "") {
            return back()->withErrors($error);
        }
        ///


        $order = Order::find($order->id);
        $sum_order_factor_with_tax_remaining = round($sum_order_factor_with_tax_remaining);
        if ($sum_order_factor_with_tax_remaining != 0) {
            return back()->withErrors("جمع کل مبلغ در روش پرداخت  باید با جمع کل فاکتور برابر باشد." .
                "<br/>" .
                "جمع کل فاکتور: " . $sum_order_factor_with_tax . " " . $order->customer->tariff->currency->caption . "<br/>" .
                " جمع کل مبلغ پرداخت: " . ($sum_order_factor_with_tax - $sum_order_factor_with_tax_remaining) . " " . $order->customer->tariff->currency->caption . "<br/>" .
                " اختلاف: " . $sum_order_factor_with_tax_remaining . " " . $order->customer->tariff->currency->caption);
        }

        $count = OrderPaymentMethod::
        where("order_id", $order->id)->
        where("amount", ">", 0)->
        whereIn("payment_method_type_id", [50, 60, 25])->
        count();
        if ($count > 0) {
            // ثبت اطلاعات اعتباری
            return redirect()->route("customer_group.buy.payment_method_step2", $order);

        } else {

            return redirect()->route("customer_group.buy.shopping_cart_submit_result", $order);
        }


    }

    public
    function payment_method_step2(Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $max_days = CustomerPaymentMethod::where("customer_id", $order->customer_id)->pluck("max_check_delivery_time_in_days", "payment_method_type_id");

        // چک کردن اینکه نیاز است سر رسید چک را از کاربر دریافت کنید یا خیر
        $check_payment_method = false;
        foreach ($order->order_payment_method as $item) {

            if ($max_days[$item->payment_method_type_id] > 0 && $item->amount > 0) {
                $check_payment_method = true;
                break;
            }
        }

        if ($check_payment_method) {
            return view("customer.group.buy.payment_method_step2", compact("order", "max_days"));
        } else {
            return redirect()->route("customer_group.buy.shopping_cart_submit_result", $order);
        }

    }

    public
    function payment_method_step2_submit(Request $request, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        foreach ($order->order_payment_method as $item) {
            $id = "check_delivery_days_" . $item->id;
            $item->check_delivery_days = $request->$id;
            $item->save();
        }


        //  $order=Order::find($order->id);

        $order->updateFeaIncrease();

        $order = Order::find($order->id);


        $order->updatePrice();

        $order->updatePercentOff();
//
        $order = Order::find($order->id);
//
        $order->updateFormalOff();
//
        $order->updateRoundOff();
        $order = Order::find($order->id);
//
        $order->updatePrice();


        return redirect()->route("customer_group.buy.shopping_cart_submit_result", $order);
    }

    public
    function shopping_cart_submit_result(Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $list_sum = OrderFactor::where("order_id", $order->id)->
        join("products", "products.id", "product_id")->
        selectRaw("sum(total_price) as sum_order_factor , sum(carton* weight) as carton_weight")->
        first();

        $sum_carton_weight = $list_sum->carton_weight;
        $sum_order_factor = $list_sum->sum_order_factor;
        $bar_weight = $sum_carton_weight / 1000;
        $bar_volume = 0;
        $packing_type_volume = [];
        $order_list_packing_type = OrderList::
        join("order_list_packing_type", "order_list_packing_type.order_list_id", "order_list.id")->
        groupBy("packing_type_id")->
        where("order_list.order_id", $order->id)->
        select("packing_type_id", "order_list_id", "carton")->get();
        foreach ($order_list_packing_type as $item) {
            $first_packing_type_id = $item->packing_type_id;
            if (!isset($packing_type_volume[$first_packing_type_id])) {
                $packing_type_volume[$first_packing_type_id] = PackingType::find($first_packing_type_id);;
            }
            if (!$packing_type_volume[$first_packing_type_id]->normal_amount) {
                return back()->withErrors("با توجه به اینکه مقدار نرمال بسته بندی " . $packing_type_volume[$first_packing_type_id]->caption . " مشخص نشده است، امکان محاسبه و حجم ارسال بار امکان پذیر نمی باشد.");
            }
            $volume = PackingType::GetVolume($packing_type_volume[$first_packing_type_id]);
            $bar_volume += $volume * $item->carton / $packing_type_volume[$first_packing_type_id]->normal_amount;

        }

        $setting_data = Setting::getIntegerValueList([
            "default_shipping_method_id",
            "default_delivery_point_type_id",
            "allow_get_shipping_method_in_buy"
        ]);

        $allow_get_shipping_method_in_buy = $setting_data["allow_get_shipping_method_in_buy"];
        $car_type_option = [];
        $shipping_method_option = [];
        $delivery_point_type_option = [];
        if ($allow_get_shipping_method_in_buy) {
            $car_type_option = Option::get("car_type_with_weight_volume", $order->car_type_id ?? 0, 0, ["volume" => $bar_volume, "weight" => $bar_weight]);
            $shipping_method_option = Option::get("shipping_methods", $order->shipping_method_id ?? $setting_data["default_shipping_method_id"], 0);
            $delivery_point_type_option = Option::get("delivery_point_type", $order->delivery_point_type ?? $setting_data["default_delivery_point_type_id"], 0);
        }

        return view("customer.group.buy.shopping_card_confirm", compact("order", "car_type_option", "shipping_method_option", "allow_get_shipping_method_in_buy", "delivery_point_type_option", "sum_order_factor"));
    }

    public
    function shopping_cart_confirm(Request $request, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        // چک کردن لیست کالاها با توجه به تعرفه
        // ممکن است، یک کالا در لیست خرید باشد ولی در تعرفه حذف شده باشد.
        $product_ids = $order->orderList()->pluck("product_id")->toArray();
        if (count($product_ids) == 0) {
            return back()->with("سبد خرید خالی است، لطفا یکبار دیگر تلاش کنید.");
        }
        $setting_data = Setting::getIntegerValueList([
            "allow_get_shipping_method_in_buy"
        ]);

        $allow_get_shipping_method_in_buy = $setting_data["allow_get_shipping_method_in_buy"];
        if ($allow_get_shipping_method_in_buy) {
            $request["insurance_amount"] = str_replace(",", "", $request["insurance_amount"]);
            $request["shipping_cost"] = str_replace(",", "", $request["shipping_cost"]);

        } else {
            $request["shipping_method_id"] = null;
            $request["car_type_id"] = null;
            $request["delivery_point_type_id"] = null;
            $request["insurance_amount"] = null;
            $request["shipping_cost"] = null;
        }
        $order->update($request->all());

        $product_tariff_log_count = ProductTariffLog::whereIn("product_id", $product_ids)->
        where("tariff_log_id", $order->tariff_log_id)->pluck("product_id", "product_id")->count();

        if ($product_tariff_log_count != count($product_ids)) {
//           return $product_tariff_log_count = ProductTariffLog::whereIn("product_id", $product_ids)->
//            where("tariff_log_id", $order->tariff_log_id)->pluck("product_id","product_id")->count();
//            return $product_tariff_log_count."-->".count($product_ids);
            return redirect()->route($this->route_path . "shopping_cart", $order)->withErrors("سبد خرید با توجه به تعرفه نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }

        $order = self::PostShoppingCartConfirm($order, $request->special_off_price);

        if ($order->status_id == 304020) {
            event(new OrderLogEvent($order, 304990));

            $order->sendSms("ordersms304030");

            return redirect()->route("sales.dashboard.view_order", $order)->with(["success" => "سفارش مشتری با موفقیت  ویرایش گردید."]);

        }
        if ($order->status_id == 304030) {
            event(new OrderLogEvent($order, 304990));
            $order->sendSms("ordersms304030");

            $customer = Customer::where("user_id", Auth::id())->first();
            if ($customer && $order->customer_id == $customer->id) {
                return redirect()->route("customer_group.order.show", $order)->with(["success" => "سفارش شما با موفقیت  ویرایش گردید."]);
            } else {
                return redirect()->route("sales.dashboard.view_order", $order)->with(["success" => "سفارش شما با موفقیت  ویرایش گردید."]);
            }
        }

        $order->nextOrderPermission();


        $post_user = \Auth::user()->posts->first();
        if ($post_user->checkButtonPermission("sales.304020")) {
            return redirect()->route("sales.customer.index")->with(["success" => "سفارش برای مشتری با موفقیت در سامانه ثبت گردید."]);
        }

        return redirect()->route("customer_group.order.index")->with(["success" => "سفارش شما با موفقیت در سامانه ثبت گردید."]);

    }


    public
    static function PostShoppingCartConfirm(Order $order, $special_off_price)
    {

        $order->special_off_price = $special_off_price;
        $order->save();

        $order->updatePercentCash();
        $order->updateSpecialOff();
        $order->updateRoundOff();
        $order->updatePrice();

        $order = Order::find($order->id);
        return $order;
    }

    public
    function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();
        $post_user = \Auth::user()->posts->first();

        if ($order->allowEditPreFactor($order->status_id ?? 0)) {
            return back()->withErrors("امکان تغییر در سفارش وجود ندارد");
        }

        // یا مشتری باشد و سفارش برای خودش باشد و یا به داشبورد فروش دسترسی داشته باشد و امکان تغییر در سفارش را هم داشته باشد.
        // 615: "sales.dashboard.index";
        //return $post_user->getMenuPermission($worker, 615)?1:2;

        if ($post_user->checkButtonPermission("sales.edit_order") && $post_user->getMenuPermission($worker, 615)) {
            return null;

        } else {

            if (!$customer) {
                if ($order->status_id == 304010) { // در انتظار تایید پیش نویس سفارش
                    return null; // کسی که به ویرایش دسترسی ندارد، فقط بتوان پیش نویس ها را کامل کند.
                }
                return back()->withErrors("امکان ویرایش سفارش برای شما امکان پذیر نمی باشد.");
            }
            if ($order->customer_id != ($customer->id ?? 0)) {

                return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
            }

        }


    }

    public
    function reserve_amount(Product $product, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("sales.reserve_amount")) {
            return back()->withErrors("  مشاهده صفحه برای شما وجود ندارد");
        }

        $list = OrderList::getProductReserveAmount($product->id, null, "list");

        return view($this->view_path . "reserve_amount", compact("list", "order", "product"));
    }

    public
    function product_request_form_amount(Product $product, Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("sales.warehouse_inventory_column")) {
            return back()->withErrors("  مشاهده صفحه برای شما وجود ندارد");
        }
        $list = Product\ProductRequest\ProductRequestForm::ProductRequestFormAmount($product, "list");
        return view($this->view_path . "product_request_form_amount", compact("list", "order", "product"));
    }

}
