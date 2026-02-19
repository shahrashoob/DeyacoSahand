<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer\ChannelTypePost;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Order\Order;
use App\Models\Post\PostUser;
use App\Models\Utility\Address\ProvincePost;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    //
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


        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $provinceItem = ProvincePost::whereIn("post_id", $post_ids)->pluck("province_id");
        $channelItem = ChannelTypePost::whereIn("post_id", $post_ids)->pluck("channel_type_id");

        $list = Customer::
        whereIn("customers.province_id", $provinceItem)->
        whereIn("customers.channel_id", $channelItem)->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("customers.code", "like", "%" . $search . "%")
                    ->orWhere("customers.caption", "like", "%" . $search . "%");
            });


        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");
            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        paginate(50);

        $order_by_Option = Option::OrderBy("customer", $order_by);


        session(["back_url" => route("sales.customer.index")]);
        return view("sales.customer.index", compact("list", "search", "order_by_Option"));

    }

    public function orders(Request $request, Customer $customer)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $provinceItem = ProvincePost::whereIn("post_id", $post_ids)->pluck("province_id");
        $channelItem = ChannelTypePost::whereIn("post_id", $post_ids)->pluck("channel_type_id");

        $list = Customer::
        where("id", $customer->id)->
        whereIn("customers.province_id", $provinceItem)->
        whereIn("customers.channel_id", $channelItem)->get();

        if (count($list) == 0) {
            return back()->withErrors(" شما به سفارش های مشتری دسترسی ندارید");
        }

        $list = Order::where("customer_id", $customer->id)->orderByDesc("id")->paginate(50);

        $back_url = session("back_url");
        return view("sales.customer.orders", compact("list", "customer", "back_url"));


    }

    public function new_order_for_customer(Customer $customer)
    {


        $user_id = \Auth::user()->id;
        $result_order = self::GetOrderForCustomer($customer, $user_id);

        if (!$result_order["result"]) {
            return back()->withErrors($result_order["error"]);
        }

        // تکمیل سفارش
        if (isset($result_order["complete_order"])) {
            return redirect()->route("customer_group.buy.complete_order", $customer);
        }

        // ادامه فرایند ثبت سفارش
        return redirect()->route("customer_group.buy.index", $result_order["order"]);
    }

    public static function GetOrderForCustomer(Customer $customer, $user_id)
    {
        $old_order = Order::where([
            "register_user_id" => $user_id,
            "customer_id" => $customer->id,
            "status_id" => 304010,//  در انتظار تایید پیش نویس سفارش
        ])->first();
        if ($old_order) {
            return [
                "result" => true,
                "order" => $old_order,
                "complete_order" => true
            ];
        }

        // بررسی انیکه آیا فرد هیچ برگ خروج از انبار تایید نشده دارد که بیش از زمان مجاز تایید از ثبت آن گذشته باشد.
        $max_confirm_datetime = Carbon::now()->addDay(-$customer->the_max_day_allowed_to_conform_exit_form_to);
        $form_list = Form::join("product_request_form_form", "forms.id", 'form_id')->
        join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
        where("product_request_forms.applicant_type_id", 30)->
        where("product_request_forms.applicant_id", $customer->id)->
        where("forms.status_id", 500000500)-> // در انتظار تایید درخواست کننده
        where("forms.updated_at", "<", $max_confirm_datetime)->
        select("forms.*")->
        addSelect("product_request_forms.order_id")->
        get();
        if (count($form_list) > 0) {
            $message = "با توجه به اینکه برگ خروج های زیر تایید نشده است، لطفا قبل ثبت سفارش جدید آنها را تایید بفرمایید. ";

            foreach ($form_list as $item) {
                $order_item = Order::find($item->order_id);
                $message .= "<br/>" . $item->code . " مربوط به سفارش شماره " . $order_item->code;
            }

            return [
                "result" => false,
                "error" => $message
            ];
        }
        $loading_status_id=Setting::getIntegerValue("order_loading_status_id");


        $order = Order::create([
            "register_user_id" => $user_id,
            "customer_id" => $customer->id,
            "status_id" => 304010, //  در انتظار تایید پیش نویس سفارش
            "order_datetime" => Carbon::now(),
            "code" => "",
            "priority_id" => 3,
            "loading_status_id"=>$loading_status_id,
            "exit_datetime"=>$loading_status_id == 460000200?now():null,

        ]);
        $order->code();

        return [
            "result" => true,
            "order" => $order
        ];

    }
}
