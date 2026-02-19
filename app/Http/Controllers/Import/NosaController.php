<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Imports\NewOrderListImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Order\NewOrderList;
use App\Models\Order\OrderList;
use App\Models\Utility\Option;
use App\Models\Utility\Call;

class NosaController extends Controller
{
    //

    public function index()
    {

        $step = 2;
        $stepInfo = Option::stepInfo("call_steps", $step);
        $model = ["name" => "nosa", "route" => "import.nosa.upload", "caption" => "فایل درخواست های جدید از نوسا "];
        return view("import/index", compact("model", "stepInfo", "step"));

    }

    public function upload()
    {

        Excel::import(new NewOrderListImport, request()->file('file_uploaded'));
        return redirect()->route("import.nosa.show");
    }

    public function show()
    {
        $stepInfo = Option::stepInfo("call_steps", 2);
        $list = NewOrderList::orderBy("error", "desc")->paginate(200);

        $error_count = NewOrderList::where("error", "!=", "")->count();

        $count = NewOrderList::count();

        return view("import/nosa_list", compact("stepInfo", "list", "error_count", "count"));

    }

    public function update()
    {

        $list = NewOrderList::orderBy('id', 'DESC')->get();

        $call = Call::where(["status_id"=> 3300])->first();
        if(!$call){
            return back()->withErrors("لطفا ابتدا فراخوانی را ایجاد کنید.");
        }
        OrderList::where(["status_id" => 300, "call_id" => $call->id])->delete();


        foreach ($list as $new_orderlist) {

            $order = Order::firstOrCreate(["code" => $new_orderlist->order_code,
                "series" => $new_orderlist->series, "customer_id" => $new_orderlist->customer_id]);
            $order->order_datetime = $new_orderlist->order_datetime;
            $order->priority_id = $new_orderlist->priority_id;


            // چک کردن وضعیت سفارش با توجه به وضعیت
            //orderlist ها
            // اگر همه خاتمه یافته بودند وضعیت سفارش خاتمه یافته
            // در غیراین صورت وضعیت در حال آماده سازی می شود.
            $order->status_id = ($order->status_id == -100) ? ($new_orderlist->order_status_id == 100 ? 35030 : 35010) : $order->status_id;
            $order->status_id = ($new_orderlist->order_status_id == 110 && $order->status_id == -35010) ? 35010 : 35030;
            $order->description_request_id = $new_orderlist->description_request_id;
            $order->description_sheet_id = $new_orderlist->description_sheet_id;
            $order->save();

            $new_orderlist->order_id = $order->id;
            $new_orderlist->status_id=300;
                OrderList::create($new_orderlist->toArray());
        }


        NewOrderList::where("id", ">", 0)->delete();

        $call->nosa_import_status_id = 3330;
        $call->save();


        return redirect()->route("import.product.inventory.index")->with(["success" => "آپلود با موفقیت انجام شده"]);


    }

    public function with_out_orderlist(){

        $call = Call::where(["status_id"=> 3300])->first();
        if(!$call){
            return back()->withErrors("لطفا ابتدا فراخوانی را ایجاد کنید.");
        }
        $call->nosa_import_status_id = 3330;
        $call->save();
        return redirect()->route("import.product.inventory.index");

    }
}
