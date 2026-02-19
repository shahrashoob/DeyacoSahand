<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

use App\Models\Order\Order;
use Illuminate\Http\Request;


class APIController extends Controller
{


    public function get_desktop_info_api(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return "سفارش مورد نظر یافت نشد.";
        }
        $post_user = \Auth::user()->posts->first();
        $order = $order->calculate();

        return view('sales.api.get_desktop_info_api', compact("order","post_user"));
    }
}