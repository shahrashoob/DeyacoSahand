<?php

namespace App\Http\Controllers;

use App\Models\Customer\Customer;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Production\Production;
use App\Models\Tmp\TrmOrder;
use App\Models\Tmp\TrmOrderList;
use App\Models\Tmp\TrmProductionCard;
use App\Models\Tmp\TrmProductionUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Call2Controller extends Controller
{
    //

    public function checkLineProduct_OK()
    {

        $production = Production::whereNotNull("line_product_id")->get();
        foreach ($production as $item) {
            $line_product = LineProductStation::where([ "line_id" => $item->line_id, "product_id" => $item->product_id])->first();
            $item->line_product_id = $line_product->id;
            $item->save();
        }
    }

    public function AddNewProductionCard()
    {

        $list = TrmProductionCard:: get();
        foreach ($list as $item) {


            $customer = Customer::where("code", "like", Str::of($item->customer_code)->trim())->first();

            if (!$customer) {
                // echo "error customer_id?".$item->id;
                $item->customer_id = 0;
                $item->save();
            } else {
                $item->customer_id = $customer->id;
                $item->save();
            }

            if ($item->order_code != "0") {
                $order = Order::firstOrCreate(["code" => $item->order_code, "series" => $item->order_series ?? 10]);

                $item->order_id = $order->id;
                $item->save();

            } else {
                $order = Order::firstOrCreate(["code" => 0, "series" => 10]);
                $item->order_id = $order->id;
                $item->save();
            }

            $product = Product::GetIdFromCode(Str::of($item->product_code)->trim());
            if (!isset($product)) {
                echo "error product_id ?" . $item->id;

            } else {
                $item->product_id = $product->id;
                $item->save();
            }

            $line = Line::GetIdFromCode(Str::of($item->line_code)->trim());
            if (!isset($line)) {
                echo "" . $item->line_code . "<br/>";

            } else {
                $item->line_id = $line->id;
                $item->save();
            }
        }
    }

    public function AddFromProductionCardToTable()
    {
        $list = TrmProductionCard:: get();
        foreach ($list as $item) {


            Production::create($item->toArray());
        }
    }

    public function AddNewProductionCardUpdate()
    {

        $list = TrmProductionUpdate:: get();
        foreach ($list as $item) {

            $line = Line::GetIdFromCode(Str::of($item->line_code)->trim());
            if (!isset($line)) {
                echo "" . $item->line_code . "<br/>";

            } else {
                $item->line_id = $line->id;
                $item->save();
            }

            $production = Production::where("serial", $item->serial)->first();
            if ($production) {
                $production->line_id = $item->line_id;
                $production->set_up_time - $item->set_up_time;
                $production->down_time = $item->down_time;
                $production->unemployment_time = $item->unemployment_time;
                $production->line_allocation = $item->line_allocation;
                $production->production_time = $item->production_time;
                $production->number_product = $item->number_product;
                $production->status_id = $item->status_id;
                $production->productivity_index = $item->productivity_index;
                $production->save();
            } else {
                echo "error on serial " . $item->serial . "<br/";
            }
        }

    }

    public function AddNewOrderList($from,$to){

        $list=TrmOrderList::where("id",">=",$from)->where("id","<",$to)-> get();
        foreach ($list as $item){
//            $customer = Customer::where("code", "like", Str::of($item->customer_code)->trim())->first();
//
//            if (!$customer) {
//                // echo "error customer_id?".$item->id;
//                $item->customer_id = 0;
//                $item->save();
//            } else {
//                $item->customer_id = $customer->id;
//                $item->save();
//            }
//
//            if ($item->order_code != "0") {
//                $order = Order::firstOrCreate(["code" => $item->order_code, "series" => $item->order_series ?? 10]);
//
//                $item->order_id = $order->id;
//                $item->save();
//
//            } else {
//                echo $item->id."</br/>";
////                $order = Order::firstOrCreate(["code" => 0, "series" => 10]);
////                $item->order_id = $order->id;
////                $item->save();
//            }
//
//            $product = Product::GetIdFromCode(Str::of($item->product_code)->trim());
//            if (!isset($product)) {
//                echo "error product_id ?" . $item->id;
//
//            } else {
//                $item->product_id = $product->id;
//                $item->save();
//            }
//
//           $production=Production::where("serial",$item->production_card_code)->first();
//            if(isset($production)){
//                $item->production_card_id=$production->id;
//                $item->save();
//            }

            OrderList::create($item->toArray());
        }
    }

    public function AddOrder(){

        $list=TrmOrder::all();

        foreach ($list as $item){
            $customer = Customer::where("code", "like", Str::of($item->customer_code)->trim())->first();

            if (!$customer) {
                 echo "error customer_id?".$item->id;
                $item->customer_id = 0;
                $item->save();
            } else {
                $item->customer_id = $customer->id;
                $item->save();
            }

            $item->series=1;
            $item->code=$item->order_code;
            $item->order_datetime=$item->created_at;
            $order=Order::firstOrCreate(["code"=>$item->code,"series"=>$item->series]);
            $order->update($item->toArray());
        }
    }
}
