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

class OrderListController extends Controller
{
    //customer/group/buy

    var $route_path = "customer_group.retail_customer.order_list.";
    var $view_path = "customer.retail_customer.order_list.";

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

        $list=Order::where("customer_id", $customer->id)->paginate(10);


        return view($this->view_path . "index", compact('customer', "list"));
    }

    public function view(Order $order)
    {
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        return view($this->view_path . "view", compact('order'));
    }


    public function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();


        if ($order->customer_id != ($customer->id ?? 0)) {

            return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
        }


    }

}