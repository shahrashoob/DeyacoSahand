<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Order\Order;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    //

    public function factor(Order $order)
    {

        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        return PrintController::getFactor($order);

    }

    public static function getFactor($order)
    {

        $seller = Setting::getStringValue("company_name");
        $national_code = Setting::getStringValue("national_code");
        $economic_number = Setting::getStringValue("economic_number");
        $text_footer_per_factor = Setting::getStringValue("text_footer_per_factor");

        $setting_data = Setting::getIntegerValueList([
            "show_product_caption_in_pre_factor",
            "show_packing_type_caption_in_pre_factor",
            "show_packing_type_code_in_pre_factor",
            "show_property_1_in_pre_factor",
            "show_property_2_in_pre_factor",
            "show_property_3_in_pre_factor",
        ]);

        $col_span_count=array_sum($setting_data);
        if($setting_data["show_product_caption_in_pre_factor"] == 1 && $setting_data["show_packing_type_caption_in_pre_factor"] == 1){
            $col_span_count--; // چون این دو مقدار را در یک ستون نمایش می دهیم.
        }
        // گرفتن مشخصه های اصلی در رسته کالایی اولین آیتم سفارش
        $goods_kind = $order->orderList()->first()->product->goods_kind ?? null;
        $property[1] = $goods_kind->property_1 ?? null;
        $property[2] = $goods_kind->property_2 ?? null;
        $property[3] = $goods_kind->property_3 ?? null;


        $html = view("customer.print.factor", compact("col_span_count","property", "setting_data", "order", "text_footer_per_factor", "seller", "national_code", "economic_number"))->render();


        return Pdf::createAsHtml($html, "L", $order->code(), "A4", " ");
    }


    public function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();
        $post_user = \Auth::user()->posts->first();

        // یا مشتری باشد و سفارش برای خودش باشد و یا به داشبورد فروش دسترسی داشته باشد.
        if ($post_user->getMenuPermission($worker, 615)) { // 615: "sales.dashboard.index";
            return null;

        } else {
            if (!$customer) {
                return back()->withErrors("صفحه مورد نظر یافت نشد");
            }

            if ($order->customer_id != ($customer->id ?? 0)) {

                return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
            }

        }

    }
}
