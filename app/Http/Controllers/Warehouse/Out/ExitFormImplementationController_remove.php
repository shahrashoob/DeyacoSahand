<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProductBlock;
use Illuminate\Http\Request;

class ExitFormImplementationController extends Controller
{
    //
    public $route_path = "wh.out.exit_form_implementation.";
    public $view_path = "warehouse.out.exit_form_implementation.";

    public function index($search_exist_form_id=null)
    {

        $search_exist_form_model=Form::
        where("id",$search_exist_form_id)->
        where("form_type_id",0)->
        first();

        $opp_kind_option = Option::get("opp_kind", 1);
        $trans_kind_option = Option::get("trans_kind", -1, 2);
        $cost_center_option = Option::get("cost_center", 0);

        $packing_form_list_ids = [];
        $packing_form_list = [];
        $description = "";
        session([
            "one_time_token" => rand(0, 1000000)
        ]);

        return view($this->view_path . "index",
            compact("opp_kind_option", "description",
                "trans_kind_option", "cost_center_option", "packing_form_list", "packing_form_list_ids","search_exist_form_model"));


    }

    public function submit(Request $request)
    {


        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $description = $request->description;

        $packing_form_list_ids = json_decode($request->packing_form_list_ids);

        if (count($packing_form_list_ids) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی انتخاب نمایید.");
        }
        $warehouse_id = null;
        if (count($packing_form_list_ids) > 0) {
            $packing_form = PackingForm::where("id", $packing_form_list_ids[0])->first();
            $warehouse_id = $packing_form->warehouse_id ?? -1;
        }
        $packing_form_list = PackingForm::whereIn("id", $packing_form_list_ids)->get();

        $error = "";
        foreach ($packing_form_list as $item) {
            $error .= $this->get_error($item->code, $warehouse_id, $packing_form_list_ids, false, $item);
        }

        if ($error != "") {
            return back()->withErrors($error);
        }
        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            return back()->withErrors("انبار برای ثبت فرم خروج از انبار معتبر نمی باشد.");
        }

        session([
            "packing_form_list_ids" => $packing_form_list_ids,
            "opp_kind" => $opp_kind,
            "trans_kind" => $trans_kind,
            "cost_center" => $cost_center,
            "description" => $description
        ]);

        return redirect()->route($this->route_path . "show_list");
    }

    public function show_list()
    {

        $packing_form_list_ids = session("packing_form_list_ids");
        $opp_kind = session("opp_kind");
        $trans_kind = session("trans_kind");
        $cost_center = session("cost_center");
        $description = session("description");

        $packing_form_list = PackingForm::whereIn("id", $packing_form_list_ids)->get();

        return view($this->view_path . "submit", compact("packing_form_list",
            "packing_form_list_ids", "opp_kind", "trans_kind", "cost_center", "description"
        ));
    }

    public function confirm(Request $request)
    {
        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $description = $request->description;

        $packing_form_list_ids = json_decode($request->packing_form_list_ids);

        if (count($packing_form_list_ids) == 0) {
            return redirect()->route($this->route_path)->withErrors("لطفا حداقل یک بسته بندی انتخاب نمایید.");
        }
        $warehouse_id = null;
        if (count($packing_form_list_ids) > 0) {
            $packing_form = PackingForm::where("id", $packing_form_list_ids[0])->first();
            $warehouse_id = $packing_form->warehouse_id ?? -1;
        }
        $packing_form_list = PackingForm::whereIn("id", $packing_form_list_ids)->get();

        $error = "";
        foreach ($packing_form_list as $item) {
            $error .= $this->get_error($item->code, $warehouse_id, $packing_form_list_ids, false, $item);
        }

        if ($error != "") {
            return back()->withErrors($error);
        }

        $warehouse = Warehouse::find($warehouse_id);
        if (!$warehouse) {
            return redirect()->route($this->route_path)->withErrors("انبار برای ثبت فرم خروج از انبار معتبر نمی باشد.");
        }


        $form = Form::CreateFrom([
            "user_id" => \Auth::user()->id,
            "form_type_id" => 0,
            "status_id" => 500000410, // در انتظار تایید انبار
            "trans_kind" => $trans_kind->id,
            "warehouse_id" => $warehouse->id,
            "ic" => $cost_center->code
        ]);
        $form->getCode("DCEF");
        event(new FormLogEvent($form));

        $packing_form_list_lowest_level_ids = PackingForm::LowestLevelOfPackingFormIds($packing_form_list);

        $packing_form_items_list_lowest_level = PackingFormItem::whereIn("packing_form_id", $packing_form_list_lowest_level_ids)->get();

        foreach ($packing_form_items_list_lowest_level as $packing_form_item) {

            $form_item = FormItem::create([
                "form_id" => $form->id,
                "product_id" => $packing_form_item->product_id,
                "amount" => $packing_form_item->final_amount,
                "sub_amount" => $packing_form_item->sub_amount,
                "carrier_id" => $packing_form_item->packing_form->carrier_id,
                "degree_id" => $packing_form_item->degree_id,
                "lot_number_id" => $packing_form_item->lot_number_id,
                "packing_type_id" => $packing_form_item->packing_form->packing_type_id,
                "packing_form_item_id" => $packing_form_item->id,
                "io_line_code" => 0,
                "product_request_form_item_id" => null,
                "description" => $description . " " . $form->code
            ]);


        }

//        $packing_form_list_lowest_level=PackingForm::whereIn("id",$packing_form_list_lowest_level_ids)->get();
//        foreach ($packing_form_list_lowest_level as $packing_form){
//            $packing_form->status_id = 7007012; // خارج شده از انبار
//            $packing_form->save();
//            // ثبت تراکنش خروج  از انبار
//            event( new PackingLogEvent( $packing_form, 7007010, null, "", $form->id ) );
//
//        }


        event(new PutInWarehouseEvent($form));
        $form->status_id = 500000200;
        $form->save();
        event(new FormLogEvent($form));

        ProductRequestForm::updateMasterFormInConformExitForm($packing_form_list, $form->id, true);

        return redirect()->route($this->route_path . "index",$form)->with(["success" => "اطلاعات بسته بندی ها با فرم خروج " . $form->getCode() . "  با موفقیت ثبت گردید."]);
    }

    public function app_packing_api(Request $request)
    {

        $opp_kind_option = Option::get("opp_kind", $request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $trans_kind_option = Option::get("trans_kind", $trans_kind->id ?? "", 2);

        $cost_center_option = Option::get("cost_center", $request->cost_center_id);

        $description = $request->description;

        $packing_form_list = [];
        $packing_form_list_ids = $request->packing_form_list_ids ?? [];

        $warehouse_id = null;
        if (count($packing_form_list_ids) > 0) {
            $packing_form = PackingForm::where("id", $packing_form_list_ids[0])->first();
            $warehouse_id = $packing_form->warehouse_id ?? -1;
        }

        $error = "";
//        $request->packing_form_code = 7850;
        //اضافه کردن یک بسته بندی
        if ($request->packing_form_code) {
            $packing_form = PackingForm::where("code", "DCPK/" . $request->packing_form_code)->first();
            $error .= $this->get_error($request->packing_form_code, $warehouse_id, $packing_form_list_ids);

            if ($error == "") {
                $packing_form_list[] = $packing_form;
                $packing_form_list_ids[] = $packing_form->id;
            }
        }

        // افزودن چند بسته بندی
        if ($request->from_packing_form_code && $request->to_packing_form_code) {

            for ($packing_form_code = $request->from_packing_form_code; $packing_form_code <= $request->to_packing_form_code; $packing_form_code++) {
                $error .= $this->get_error($packing_form_code, $warehouse_id, $packing_form_list_ids);

                if ($error == "" && $warehouse_id == null) {
                    $packing_form = PackingForm::where("code", "DCPK/" . $packing_form_code)->first();
                    $warehouse_id = $packing_form->warehouse_id ?? -1;
                }
            }

            if ($error == "") {

                for ($k = $request->from_packing_form_code; $k <= $request->to_packing_form_code; $k++) {

                    $packing_form = PackingForm::where("code", "DCPK/" . $k)->first();
                    $packing_form_list[] = $packing_form;
                    $packing_form_list_ids[] = $packing_form->id;
                }
            }

        }

        $packing_form_list_ids[] = -1;
        $packing_form_list = PackingForm::whereIn("id", $packing_form_list_ids)->get();
        $packing_form_list_ids = PackingForm::whereIn("id", $packing_form_list_ids)->pluck("id")->toArray();

        //$error .= $warehouse_id;

        return view($this->view_path . "_packing_form_list",
            compact("opp_kind_option", "packing_form_list_ids", "packing_form_list", "description",
                "trans_kind_option", "cost_center_option", "error"));


    }

    public function get_error($packing_form_code, $warehouse_id, $packing_form_list_ids, $add_packing_no_cheek = true, $packing_form = null)
    {
        $error = "";
        if (!$packing_form) {
            $packing_form = PackingForm::where("code", "DCPK/" . $packing_form_code)->first();
        }

        if (!$packing_form) {
            $error .= "بسته بندی " . "DCPK/" . $packing_form_code . " یافت نشد." . "<br/>";
        }

        if ($packing_form && ($packing_form->status_id != 7007003 || $packing_form->warehouse_status_id != 4201)) {
            $error .= "وضعیت  بندی " . "DCPK/" . $packing_form_code . " موجود در انبار نمی باشد." . "<br/>";
        }

        if ($warehouse_id && $packing_form && $packing_form->warehouse_id && $warehouse_id != $packing_form->warehouse_id) {
            $error .= "انباری که بسته بندی " . " DCPK/" . $packing_form_code . " در آن قرار دارد، با بسته بندی های قبلی برابر نیست." . "<br/>";
        }

        $warehouse = $packing_form->warehouse ?? null;
        if ($packing_form && !$warehouse) {
            $error .= " انبار نامعتبر است، لطفا مجدد تلاش کنید." . "<br/>";
        }
//        if($warehouse && $warehouse->warehouse_type_id !=1){
//            $error.="امکان خروج از به غیر از انبار اصلی وجود ندارد.";
//        }

        if ($packing_form && $add_packing_no_cheek && in_array($packing_form->id, $packing_form_list_ids)) {
            $error .= "بسته بندی  " . " DCPK/" . $packing_form_code . " قبلا انتخاب شده است." . "<br/>";

        }

        if ($packing_form) {
            foreach ($packing_form->items as $packing_form_item) {
                if (!$packing_form_item->product->goods_kind->record_out_of_warehouse_manually) {
                    $error .= "امکان ثبت خروج از انبار به صورت دستی برای بسته بندی که کالای " . $packing_form_item->product->caption . " در آن قرار دارد، وجود ندارد. " . "<br/>";
                }
            }
        }

        if ($packing_form && $packing_form->packing_form_master) {
            $error .= "امکان خروج از انبار برای بسته بندی های فرعی وجود ندارد." . "<br/>";
        }
        if ($packing_form) {
            // بررسی اینکه کالا در انبارگردانی نباشد
            $warehouse_ids = [];
            $warehouse_ids[] = $packing_form->warehouse_id;

            $product_ids_for_check = $packing_form->items()->pluck("product_id")->toArray();
            $result_warehouse = WarehouseProductBlock::CheckProduct($warehouse_ids, $product_ids_for_check);

            if (!$result_warehouse["result"]) {
                $error .= $result_warehouse["error"];

            }
        }


        return $error;
    }


}
