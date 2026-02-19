<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReceiptOfReceivablesController extends Controller
{

    public function list(Request $request){


        $search= $request->search;
        $order_by=$request->order_by;

        $list =Order::search($search,$order_by)
            ->whereIn("orders.status_id",[35030])
            ->whereNull("exit_datetime")
            ->paginate();

        $order_by_Option=Option::OrderBy("orders",$order_by);

        return view("sales.receipt_of_receivables.list",compact("order_by_Option","list","search"));

    }
    public function finished_order(Order $order){
1/0;
//        $order= $order->calculate();
        return view("sales.receipt_of_receivables.finished_order",compact("order"));
    }
    public function exit_permission(Order $order){
1/0;
        if($order->exit_status_id==460000200){
            return back()->withErrors("مجوز خروج قبلا ثبت شده است");
        }
        $order->exit_status_id=460000200;// مجوز خروج صادر شد
        $order->exit_datetime=Carbon::now();
        $order->save();
        $order->log();
        return redirect()->route("sales.receipt_of_receivables.list")->with(["success"=>"برگ خروج با موفقیت ثبت شد"]);

    }


}
