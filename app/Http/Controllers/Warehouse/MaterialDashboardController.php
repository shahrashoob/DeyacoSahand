<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
//use App\Models\Order\RequestFromWarehouse;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialDashboardController extends Controller
{
    public function productionStatusIds()
    {
        $post_ids = \Auth::user()->posts->pluck("post_id");

        $ids = PostStatus::whereIn("post_id", $post_ids)->pluck("status_id")->toArray();

        $ids[] = -1;

        return $ids;

    }

    public function list(Request $request)
    {
        $productionStatusIds = $this->productionStatusIds();

        if ($request->waiting_status_id != 0 && !in_array($request->waiting_status_id, $productionStatusIds)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده کارت های وضعیت انتخاب شده را ندارید");
        }

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $waiting_status_id = $request->waiting_status_id;
        } else {
            $search = session("search_material");
            $order_by = session("order_by_material");
            $waiting_status_id = session("waiting_status_id_material");
        }

        session([
            "search_material" => $search,
            "order_by_material" => $order_by,
            "waiting_status_id_material" => $waiting_status_id
        ]);

        // search
        if ($waiting_status_id != 0) {
            $productionStatusIds = [];
            $productionStatusIds[] = $waiting_status_id;
        }

        $list = Production::
        when($search != "", function ($query) use ($search) {
            return $query->where("serial", "like", "%" . $search . "%");

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");
            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        when($productionStatusIds != [] , function ($query) use ($productionStatusIds) {
            return $query->where(function ($query) use ($productionStatusIds) {
                $query->whereIn("production_cards.status_id", $productionStatusIds)->
                orWhereIn("production_cards.waiting_status_id", $productionStatusIds);
            });
        })->
        where("is_master_of_rfw", 1);

        if ($search == "") {
            $list = $list->where("production_cards.status_id", 500);
        }
        $list = $list->paginate(50);

        $order_by_Option = Option::OrderBy("production", $order_by);

        $waiting_status_option = Option::get("production_waiting_status", $waiting_status_id, 50010);

        return view("warehouse.material_dashboard.list", compact("waiting_status_option", "order_by_Option", "list", "search"));

    }

    public function view_materials(Production $production, $delivery = 0)
    {
        //Delete Old Form
        $forms = Form::where(
            [
                "order_id" => $production->order_id,
                "order_list_id" => $production->order_list_id,
                "production_card_id" => $production->id,
                "user_id" => Auth::user()->id,
                "status_id" => 500000100,
                "trans_kind" => 8
            ]
        )->select("id")->get()->toArray();

        FormItem::whereIn("form_id", $forms)->delete();

        Form::whereIn("id", $forms)->delete();
        //?????????????

        if (!(in_array($production->waiting_status_id, $this->productionStatusIds()))) {

            return back()->withErrors("شما اجازه دسترسی به فرم را ندارید");
        }

        $amount = RequestFromWarehouse::where("master_production_id", $production->id)->
        groupBy("material_id")->
        addSelect(DB::raw("sum(amount) as amount, material_id "))->
        pluck("amount", "material_id");

        $amount_sent = RequestFromWarehouse::where("master_production_id", $production->id)->
        groupBy("material_id")->
        addSelect(DB::raw("sum(amount_sent) as amount_sent, material_id "))->
        pluck("amount_sent", "material_id");

        $amount_remaining = RequestFromWarehouse::where("master_production_id", $production->id)->
        groupBy("material_id")->
        addSelect(DB::raw("sum(amount_remaining) as amount_remaining, material_id "))->
        pluck("amount_remaining", "material_id");

        $rfw_list = RequestFromWarehouse::where("master_production_id", $production->id)->
        groupBy("material_id")->get();

        $rfw_production_list = RequestFromWarehouse::where("master_production_id", $production->id)->
        groupBy("production_card_id")->get();

        $allow_register_new_form =
            Form::where(
                [
                    "order_id" => $production->order_id,
                    "order_list_id" => $production->order_list_id,
                    "production_card_id" => $production->id,
                    "status_id" => 500000110,
                    "trans_kind" => 8
                ]
            )->count() > 0 ? False : True;
        return view("warehouse.material_dashboard.view_material", compact("allow_register_new_form", "delivery", "rfw_list", "rfw_production_list", "amount", "amount_remaining", "amount_sent", "production"));

    }

    public function confirm_form_request(Request $request, Production $production)
    {

        if (!(in_array($production->waiting_status_id, $this->productionStatusIds()))) {
            return back()->withErrors("شما اجازه دسترسی به فرم را ندارید");
        }

        $count = 0;
        $error_text = "";

        foreach ($request->data as $rfw_id => $value) {
            if ($value) {
                $count++;

                $RFW_item = RequestFromWarehouse::find($rfw_id);
                $error_text .= $value < 0 ? " مقدار تحویلی " . $RFW_item->material->caption . " نمی تواند منفی باشد" . "<br/>" : "";

//                if ($value > $RFW_item->amount_remaining) {
//                    // $error_text.="مقدار تحویلی ".$RFW_item->material->caption." از مقدار باقی مانده بزرگتر است."."<br/>";
//                }

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
                "status_id" => 500000100,
                "trans_kind" => 8
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
                $form->warehouse_id = $RFW_item->material->warehouse_id;
                $form->save();
            }
        }

        return redirect()->route("wh.material.confirm_form_request_show", [$production, $form]);


    }

    public function confirm_form_request_show(Production $production, Form $form)
    {
        return view("warehouse.material_dashboard.confirm_form_request", compact("production", "form"));
    }

    public function store_form_request(Request $request, Production $production, Form $form)
    {
        if (!(in_array($production->waiting_status_id, $this->productionStatusIds()))) {
            return back()->withErrors("شما اجازه دسترسی به فرم را ندارید");
        }

        $form->refresh();
        if ($form->status_id != 500000100) {
            return redirect()->route("wh.material.view_materials", $production)->withErrors("این فرم قبلا ثبت شده و قابلیت ثبت مجدد ندارد");
        }

        $form->status_id = 500000110;
        $form->save();

        $production->changeProductionStatusGroupInForm(500020, 500030);

        return redirect()->route("wh.material.list", $production)->with(["success" => "انباردار محترم، عملیات با موفقیت انجام شده، قبل از ترک محل تاییدیه تولید را اخذ نمایید."]);


    }


    public function show_exit_form(Production $production, Form $form)
    {
        return view("warehouse.material_dashboard.exit_form", compact("production", "form"));
    }

}
