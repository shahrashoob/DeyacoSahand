<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report1004_1Export;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Report1004Controller extends Controller
{
    //
    public function index()
    {

        $warehouse_option = Option::get("warehouse");
        $product_option = Option::get("product_all");

        return view("report/1004/index", compact("warehouse_option", "product_option"));

    }

    public function submit_form(Request $request)
    {
        $from_warehouse_id = $request->from_warehouse_id;
        $to_warehouse_id = $request->to_warehouse_id;
        $product_id = $request->product_id;
        $start_date=$request->start_date;
        $end_date=Carbon::parse($request->end_date)->addDay();

        if ( ! isset( $request->start_date ) || ! isset( $request->end_date ) ) {
            return back()->withErrors( "لطفا تاریخ شروع/تاریخ پایان را به درستی انتخاب نمایید." );
        }

        $list = WarehouseProduct::
        when($from_warehouse_id != 0, function ($query) use ($from_warehouse_id) {
            $query->where("warehouse_id", ">=", $from_warehouse_id);
        })->
        when($to_warehouse_id != 0, function ($query) use ($to_warehouse_id) {
            $query->where("warehouse_id", "<=", $to_warehouse_id);
        })->

        when($product_id, function ($query) use ($product_id) {
            $query->where("product_id", $product_id);
        })->
        where("created_at", ">=", $start_date)->
        where("created_at", "<", $end_date)
            ->get();

        $init_inventory = WarehouseProduct::
        when($from_warehouse_id != 0, function ($query) use ($from_warehouse_id) {
            $query->where("warehouse_id", ">=", $from_warehouse_id);
        })->
        when($to_warehouse_id != 0, function ($query) use ($to_warehouse_id) {
            $query->where("warehouse_id", "<=", $to_warehouse_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            $query->where("product_id", $product_id);
        }) ->
        where("created_at", "<", $request->start_date)->
            addSelect(DB::raw(" sum(input) - sum(output) as value"))->first();

        $product = Product::find($product_id);
        $init_inventory=$init_inventory->value??0;
        $export = new Report1004_1Export();
        $export->list = $list;
        $export->init_inventory = $init_inventory;
        $export->product = $product;
        return Excel::download($export, 'product_'.$product->id."_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');


    }

}
