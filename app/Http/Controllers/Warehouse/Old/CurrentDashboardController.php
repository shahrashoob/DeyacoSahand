<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Order\TransKind;
use App\Models\Utility\Message;
use App\Models\Warehouse\WarehousePost;
use Illuminate\Http\Request;
use App\Models\Production\Production;
use App\Models\Production\ExtraProduction;

use App\Models\Utility\Option;
use Illuminate\Support\Str;
//use App\Models\Order\RequestFromWarehouse;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Support\Facades\Auth;

class CurrentDashboardController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_current_dashboard");
            $order_by = session("order_by_current_dashboard");
        }
        session(["search_current_dashboard" => $search, "order_by_current_dashboard" => $order_by]);

        $list = Production::search($search, $order_by)
            ->paginate(50);

        $orber_by_Option = Option::OrderBy("production", $order_by);
        // }
        //         $list=Production::where("status_id",500)-> paginate();
        //       //  return $productions->product;
        return view("warehouse.current_dashboard.list", compact("orber_by_Option", "list", "search"));
    }

    public function view_material(Production $production)
    {

        $forms = Form::where(
            [
                "order_id" => $production->order_id,
                "order_list_id" => $production->order_list_id,
                "production_card_id" => $production->id,
                "user_id" => Auth::user()->id,
                "status_id" => 500000100
            ]
        )->select("id")->get()->toArray();

        FormItem::whereIn("form_id", $forms)->delete();

        Form::whereIn("id", $forms)->delete();

        return view("warehouse.current_dashboard.view_material", compact("production"));

    }

    public function confirm_form_request(Request $request, Production $production)
    {
        $count = 0;
        $error_text = "";
        foreach ($request->data as $rfw_id => $value) {
            if ($value) {
                $count++;

                $RFW_item = RequestFromWarehouse::find($rfw_id);
                $error_text .= $value < 0 ? " مقدار تحویلی " . $RFW_item->material->caption . " نمی تواند منفی باشد" . "<br/>" : "";

                if ($value > $RFW_item->amount_remaining) {
                    // $error_text.="مقدار تحویلی ".$RFW_item->material->caption." از مقدار باقی مانده بزرگتر است."."<br/>";
                }

            }
        }


        $error_text .= $count == 0 ? "حذاقل یکی از ماده های اولیه باید مقدار داشته باشد" . "<br/>" : "";

        if ($error_text != "") {
            return back()->withErrors($error_text);
        }

        $form = Form::CreateFrom(
            [
                "order_id" => $production->order_id,
                "order_list_id" => $production->order_list_id,
                "production_card_id" => $production->id,
                "user_id" => Auth::user()->id,
                "status_id" => 500000100
            ]
        );

        foreach ($request->data as $rfw_id => $value) {

            if ($value) {
                $RFW_item = RequestFromWarehouse::find($rfw_id);
                FormItem::create([
                    "form_id" => $form->id,
                    "rfw_id" => $rfw_id,
                    "product_id" => $RFW_item->material->id,
                    "amount" => $value,
                ]);
            }
        }


        return view("warehouse.current_dashboard.confirm_form_request", compact("production", "form"));
    }

    public function store_form_request(Request $request, Production $production, Form $form)
    {

        if ($form->status_id == 500000200) {
            return redirect()->route("wh.cd.view_material", $production)->withErrors("این فرم قبلا ثبت شده و قابلیت ثبت مجدد ندارد");
        }

        $form->status_id = 500000200;
        $form->save();

        foreach ($form->item as $item) {


            $WP = WarehouseProduct::create([
                "warehouse_id" => $item->product->warehouse_id,
                "product_id" => $item->product->id,
                "form_id" => $form->id,
                "output" => $item->amount,
                "ic" => $production->product->ic,
                "opp_kind" => 1,
                "trans_kind" => 8
            ]);

            $WP->update_remaining();

            $RFW_item = RequestFromWarehouse::find($item->rfw_id);
            $RFW_item->amount_sent += $item->amount;
            $RFW_item->amount_remaining -= $item->amount;

            $RFW_item->amount_remaining = $RFW_item->amount_remaining < 0 ? 0 : $RFW_item->amount_remaining;
            $RFW_item->status_id = $RFW_item->amount_remaining == 0 ? 386 : 384;

            $RFW_item->save();

        }





        return redirect()->route("wh.cd.view_material", $production)->with(["success" => "فرم با موفقیت ثبت شد"]);
    }

    public function delivery_form_request(Production $production)
    {
        $delivery = 1;
        return view("warehouse.current_dashboard.view_material", compact("production", "delivery"));

    }

    public function show_exit_form(Production $production, Form $form)
    {
        return view("warehouse/current_dashboard/exit_form", compact("production", "form"));
    }


    public function entry_form_to_warehouse()
    {

        $factory_option = Option::get("factory", 1);
        $warehouse_option = Option::get("warehouse");
        $opp_kind_option = Option::get("opp_kind", 1);
        $trans_kind_option = Option::get("trans_kind", -1, 1);
        $product_option = Option::get("product_active");

        return view("warehouse.current_dashboard.entry_form_to_warehouse",
            compact("factory_option", "opp_kind_option", "warehouse_option",
                "trans_kind_option", "product_option"));


    }

    public function entry_form_to_warehouse_submit(Request $request)
    {
        $request->all();

        session(["entry_form" => $request->all()]);

        return view("warehouse.current_dashboard.entry_form_to_warehouse_submit", compact("request"));
    }

    public function entry_form_to_warehouse_confirm()
    {

        $request = session("entry_form");

        $trans_kind = TransKind::find($request["trans_kind_id"]==100?0:$request["trans_kind_id"]);

        $form = Form::CreateFrom([
            "user_id" => \Auth::user()->id,
            "form_type_id"=>100,
            "status_id" => 500000200,
            "trans_kind" => $trans_kind->id,
            "warehouse_id"=> $request["warehouse_id"]
        ]);

        if($request["description"]) {
            $msg = Message::create(["text" => $request["description"], "message_type_id" => 130]);
            $form->message_id=$msg->id;
            $form->save();
        }


        for ($i = 1; $i < 11; $i++) {
            $prow = "product_" . $i;
            if (isset($request[$prow]) && $request[$prow] != 0) {
                $wp = WarehouseProduct::create([
                    "warehouse_id" => $request["warehouse_id"],
                    "product_id" => $request[$prow],
                    "form_id" => $form->id,
                    "factory_id" => $request["factory_id"],
                    "input" => $trans_kind->entry_type_id == 1 ? $request["data"][$i]["carton"] : 0,
                    "output" => $trans_kind->entry_type_id == 2 ? $request["data"][$i]["carton"] : 0,
                    "ic" => $request["ic"],
                    "trans_kind" => $trans_kind->id,
                    "opp_kind" => $request["opp_kind_id"]
                ]);
                $wp->update_remaining();
            }
        }

        return redirect()->route("wh.entry_form_show",$form);

    }

    public function entry_form_show(Form $form){

        return view("warehouse.current_dashboard.entry_form_show",compact("form"));
    }

    public function entry_form_list(){

        $post_ids = \Auth::user()->posts->pluck("post_id");
        $warehousePermission=WarehousePost::
        whereIn("post_id",$post_ids)->
        groupBy("warehouse_id")->
        pluck("warehouse_id");

        $list=Form::whereIn("warehouse_id",$warehousePermission)->orderByDesc("created_at")->paginate(30);

        return view("warehouse.current_dashboard.entry_form_list",compact("list"));

    }


}
