<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use Illuminate\Http\Request;
use App\Models\Order\OrderList;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Support\Facades\Auth;

class ProductDashboardController extends Controller
{
    //
    public function list(Request $request)
    {

        $search = "";

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            // if (isset($request->page))
            $search = session("search_product_dashboard");
            $order_by = session("order_by_product_dashboard");
        }
        session(["search_product_dashboard" => $search, "order_by_product_dashboard" => $order_by]);

        $list = Order::search($search, $order_by);
        if (!(isset($search) && $search != "")) {
            $list = $list->whereIn("orders.status_id", [35030, 35040]);
        }

        $list = $list->paginate();

        $orber_by_Option = Option::OrderBy("orders", $order_by);

        return view("warehouse.product_dashboard.list", compact("orber_by_Option", "list", "search"));

    }

    public function view_order(Order $order)
    {

        $order = $order->calculate();

        $forms = Form::where(
            [
                "order_id" => $order->id,
                "order_list_id" => 0,
                "production_card_id" => 0,
                "user_id" => Auth::user()->id,
                "status_id" => 500000100
            ]
        )->select("id")->get()->toArray();

        FormItem::whereIn("form_id", $forms)->delete();

        Form::whereIn("id", $forms)->delete();

        return view("warehouse.product_dashboard.view_order_details", compact("order"));

    }

    public function delivery_form_request(Order $order)
    {
        $delivery = 1;
        $order = $order->calculate();
        return view("warehouse.product_dashboard.view_order_details", compact("order", "delivery"));

    }

    public function confirm_form_request(Request $request, Order $order)
    {

        if ($order->exit_status() != 460000200) {
            return back()->withErrors("مجوز خروج صادر نشده است");
        }
        $count = 0;
        $error_text = "";
        foreach ($request->data as $order_id => $value) {
            if ($value) {
                $count++;

                $OL_item = OrderList::find($order_id);
                $error_text .= $value < 0 ? " مقدار تحولی " . $OL_item->product->caption . " نمی تواند منفی باشد" . "<br/>" : "";

                if ($value > $OL_item->amount_remaining) {
                    $error_text .= "مقدار تحویلی " . $OL_item->product->caption . " از مقدار باقی مانده بزرگتر است." . "<br/>";
                }

            }
        }


        $error_text .= $count == 0 ? "حذاقل یکی از محصولات  باید مقدار داشته باشد" . "<br/>" : "";

        if ($error_text != "") {
            return back()->withErrors($error_text);
        }

        $form = Form::CreateFrom(
            [
                "order_id" => $order->id,
                "order_list_id" => 0,
                "production_card_id" => 0,
                "user_id" => Auth::user()->id,
            ]
        );

        foreach ($request->data as $order_list_id => $value) {
            if ($value) {
                $OL_item = OrderList::find($order_list_id);
                FormItem::create([
                    "form_id" => $form->id,
                    "order_list_id" => $order_list_id,
                    "product_id" => $OL_item->product->id,
                    "amount" => $value
                ]);

            }

        }

        return view("warehouse.product_dashboard.confirm_form_request", compact("order", "form"));

    }

    public function store_form_request(Request $request, Order $order, Form $form)
    {
        $form->refresh();
        if ($form->status_id == 500000200) {
            return redirect()->route("wh.product.view_order", $order)->withErrors("فرم قبلا ثبت شده است");
        }
        $form->status_id = 500000200;
        $form->save();

        foreach ($form->item as $item) {

            $OL_item = OrderList::find($item->order_list_id);
            $OL_item->amount_sent += $item->amount;
            $OL_item->amount_remaining -= $item->amount;

            $OL_item->erp_status_id = $OL_item->amount_remaining != 0 ? 340 : 350;
            $OL_item->save();
            $OL_item->log();

            $WP = WarehouseProduct::create([
                "warehouse_id" => $item->product->warehouse_id,
                "product_id" => $item->product->id,
                "form_id" => $form->id,
                "output" => $item->amount,
                "ic" => $order->customer->code ?? "00",
                "opp_kind" => 1,
                "trans_kind" => $OL_item->order_type_id == 100 ? 10 : 6
            ]);


            $WP->update_remaining();
        }



        $order_count = OrderList::
        where("order_id", $order->id)->
        whereIn("erp_status_id", [340, 305, 360])->
        whereNull("from_order_id")->
        count();

        $order_status_id = $order_count == 0 ? 35050 : 35040;

        $order->status_id = $order_status_id;
        $order->save();
        $order->log();
        return redirect()->route("wh.product.view_order", $order)->with(["success" => "فرم با موفقیت ثبت شد"]);

    }

    public function show_exit_form(Order $order, Form $form)
    {
        return view("warehouse/product_dashboard/exit_form", compact("order", "form"));
    }

}
