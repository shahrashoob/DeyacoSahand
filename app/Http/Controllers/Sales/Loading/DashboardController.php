<?php

namespace App\Http\Controllers\Sales\Loading;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductRequestPermission\ProductRequestPermission;
use App\Models\Order\Loading\LoadingProcesses;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index_request_permission()
    {
      $list=  ProductRequestPermission::orderBy("id", "desc")->paginate(30);

      return view('sales.loading.dashboard.index', compact('list'));
    }
    public function create_order_to_collection(Order $order)
    {
        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.order_to_collection")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

        $post_list = Option::get("posts",410); // تیم جمع آوری بار
        $car_types_option = Option::get("car_types");
        $doors_option = Option::get("doors");
        return view("sales.loading.dashboard.create", compact("order", "post_list", "car_types_option","doors_option"));
    }

    public function order_to_collection_submit(Request $request, Order $order)
    {

        $exists_item = LoadingProcesses::where(["order_id" => $order->id, "status_id" => 35030020])->exists();
        if ($exists_item) {
            return back()->withErrors("یک بارگیری در انتظار جمع آوری بار وجود دارد");
        }

        $loading_process = LoadingProcesses::create([
            "order_id" => $order->id,
            "status_id" => 35030020,
            "supervisor_collect_post_id" => $request->supervisor_collect_post_id,
            "supervisor_loading_post_id" => $request->supervisor_loading_post_id,
            "car_type_id" => $request->car_type_id
        ]);
        $loading_process->log("", 35030010);

        return redirect()->route("sales.dashboard.view_order", $order);
    }

}
