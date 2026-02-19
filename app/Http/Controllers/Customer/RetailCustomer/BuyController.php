<?php

namespace App\Http\Controllers\Customer\RetailCustomer;

use App\Events\Order\OrderLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\BuyController as CustomerBuyController;
use App\Http\Controllers\Sales\CustomerController;
use App\Models\Accounting\Client\ClientPayment;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Customer\Customer;
use App\Models\LineProduct\GoodsKind\GoodsKindDisplayProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderFactor;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BuyController extends Controller
{
    //customer/group/buy

    var $route_path = "customer_group.retail_customer.buy.";
    var $view_path = "customer.retail_customer.buy.";

    public function index()
    {

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();

        if (!isset($customer)) {
            return back()->withErrors("اطلاعات مشتری برای شما در سامانه وجود ندارد ");

        }
        if (!$customer->parent) {
            return back()->withErrors("با توجه به اینکه مشتری سطح 1 برای شما مشخص نشده است، امکان ثبت سفارش وجود ندارد.");
        }

        $query = ProductTariff::
        join("products", "products.id", "product_tariff.product_id")->
        //join("goods_kind_property_values", "products.id", "goods_kind_property_values.product_id")->
        where([
            "active_status_id" => 1200,
            "possibility_of_sale" => 1,
            "tariff_id" => $customer->parent->tariff_id,
//            "value" => $goods_kind_property_value->value
        ]);

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


        return view($this->view_path . "index", compact('customer', "list"));
    }


    public function add_to_shopping_cart(ProductTariff $productTariff)
    {

        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();

        $order_result = CustomerController::GetOrderForCustomer($customer, $user_id);
        if (!$order_result["result"]) {
            return back()->withErrors($order_result["error"]);
        }

        $order = $order_result["order"];

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $data=[
            $productTariff->id=>1,
            "packing_type"=>[$productTariff->id=>[$productTariff->packing_type_id]]
        ];
        $result_add_product = CustomerBuyController::PostAddToShoppingCard($order, $productTariff->product, $customer, $data);
        if (!$result_add_product["result"]) {
            return back()->withErrors($result_add_product["error"]);
        }

        return back()->with(["success" => "کالا با موفقیت به سبد خرید شما اضافه گردید."]);

    }

    public function shopping_cart()
    {
        $user_id = \Auth::user()->id;
        $customer = Customer::where("user_id", $user_id)->first();
        $order_result = CustomerController::GetOrderForCustomer($customer, $user_id);
        if (!$order_result["result"]) {
            return back()->withErrors($order_result["error"]);
        }

        $order = $order_result["order"];
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        // مشخص کردن نوع فاکتور
        $order = Order::SellingTypeRefresh($order);

        $order->factorRefresh();

        $order->updatePercentOff();

        $order = Order::find($order->id);

        $order->updatePrice();

        $order = Order::find($order->id);



        return view($this->view_path . "shopping_card", compact(
            "order",
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

        if ($request->has_order_consumed_product) { // اگر مشخصات نوع تامین دارد.
            // ثبت مشخصات نوع تامین
            $result_consumed_product = self::SubmitConsumedProduct($request, $order);
            if (!$result_consumed_product["result"]) {
                return back()->withErrors(["error"]);
            }
        }
        // بررسی اینکه اگر نوع فروش کارمزدی باشد، آیا نوع تامین برای همه موارد ثبت شده است یا خیر
        foreach ($order->orderList as $order_list) {
            if ($order_list->type_of_sale_of_product_id == 2) { // فروش کارمزدی
                $consumed_product_list[$order_list->id] = Product\ConsumedProduct\ConsumedProduct::where([
                    "product_id" => $order_list->product_id,
                    "in_ordering_customer_can_choose" => 1
                ])->get();
                foreach ($consumed_product_list[$order_list->id] as $item) {
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


        return redirect()->route($this->route_path . "bank", $order);

    }

    public function bank(Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $amount=OrderFactor::where("order_id",$order->id)->sum("total_price_with_tax");
        if($amount <= 0){
            return back()->withErrors("مبلغ فاکتور نامعتبر است، لطفا مجدد تلاش کنید.");
        }
        return view($this->view_path . "bank", compact("order","amount"));
    }

    public function submit_bank(Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }
        $amount=OrderFactor::where("order_id",$order->id)->sum("total_price_with_tax");

        $payment = ClientPayment::create([
            "user_id" => Auth::id(),
            "client_transaction_type_id" => 5, // خرید آنلاین مشتریان
            "amount" => $amount,
            "status_id" => 3600001,//تایید نشده,
            "order_id"=>$order->id
        ]);

        $order->status_id = 35095 ; // در انتظار پرداخت صورت حساب آنلاین
        $order->save();

        event(new OrderLogEvent($order, 304996)); // اتصال به درگاه بانک

        return \App\Http\Controllers\Accounting\Client\BuyController::confirmPayment($payment); // اتصال به درگاه بانک
    }
    public function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();

        if ($order->allowEditPreFactor($order->status_id ?? 0)) {
            return back()->withErrors("امکان تغییر در سفارش وجود ندارد");
        }

        if (!$customer) {
            return back()->withErrors("امکان مشاهده سفارش برای شما امکان پذیر نمی باشد.");
        }
        if ($order->customer_id != ($customer->id ?? 0)) {

            return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
        }


    }

}