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
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\LineProduct\Product\ProductWarehouseStorageType;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Warehouse\WarehouseStorageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\ExecutionStatus;

class ExitFormImplementation2Controller extends Controller
{
    //
    public $route_path = "wh.out.exit_form_implementation2.";
    public $view_path = "warehouse.out.exit_form_implementation2.";

    public $read_product_info_type4 = 3;

    public function index($search_exist_form_id = null)
    {

        // چگ کردن اینکه هیچ فرم دیگری در انتظار ثبت نداشته باشد.
        $search_exist_form_model = Form::
        where("id", $search_exist_form_id)->
        where("form_type_id", 0)->
        first();

        $opp_kind_option = Option::get("opp_kind", 1);
        $trans_kind_option = Option::get("trans_kind", -1, 2);
        $cost_center_option = Option::get("cost_center", 0);
        $warehouse_option = Option::get("warehouse", 0, 1);
        $warehouse_storage_type_option = Option::get("warehouse_storage_types", 2, 1);

        $packing_form_list_ids = [];
        $packing_form_list = [];
        $description = "";
        session([
            "one_time_token" => rand(0, 1000000)
        ]);
        $packing_form_read_ids = [];
        $data = ProductRequestFormSessionData::getDataByOtherId(Auth::id(),0,231,["packing_form_read_ids"=>[]]);
        if (isset($data["packing_form_read_ids"]) && count($data["packing_form_read_ids"]) > 0) {
            $packing_form_read_ids = $data["packing_form_read_ids"];
        }
        return view($this->view_path . "index",
            compact("opp_kind_option", "description", "packing_form_read_ids", "warehouse_storage_type_option",
                "trans_kind_option", "cost_center_option", "packing_form_list", "packing_form_list_ids", "search_exist_form_model", "warehouse_option"));


    }

    public function submit(Request $request)
    {


        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $warehouse_storage_type = WarehouseStorageType::find($request->warehouse_storage_type_id);
        $warehouse = Warehouse::find($request->warehouse_id);
        $description = $request->description;

        if (!$warehouse) {
            return back()->withErrors("انبار برای ثبت فرم خروج از انبار معتبر نمی باشد.");
        }

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);

        $data["opp_kind_id"] = $opp_kind->id;
        $data["trans_kind_id"] = $trans_kind->id;
        $data["cost_center_id"] = $cost_center->id;
        $data["warehouse_id"] = $warehouse->id;
        $data["warehouse_storage_type_id"] = $warehouse_storage_type->id;
        $data["description"] = $description;
        ProductRequestFormSessionData::setDataByOtherId($user_id,0,231, $data);

        switch ($warehouse_storage_type->id) {
            case 1:
                return back()->withErrors("این ماژول در دست پیاده سازی است، لطفا با واحد پشتیبانی تماس بگیرید.");
                break;
            case 2:
                return redirect()->route($this->route_path . "read_packing_forms");
                break;
            case 3:
                return back()->withErrors("این ماژول در دست پیاده سازی است، لطفا با واحد پشتیبانی تماس بگیرید.");
                break;
            case 4:
                return redirect()->route($this->route_path . "read_product_info_type4");
                break;
        }

    }

    public function read_packing_forms()
    {
        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);
        $packing_form_read_ids = [];
        if(is_null($data["packing_form_read_ids"])){
            $data["packing_form_read_ids"] = [];
        }
        $packing_form_read_ids_string = $data["packing_form_read_ids"];

        foreach ($packing_form_read_ids_string as $each_number) {
            $packing_form_read_ids[] = (int)$each_number;
        }

        if(!isset($data["opp_kind_id"])){
            return back()->withErrors("اطلاعات فرم قبلی نامعتبر است و امکان تکمیل آن وجود ندارد، لطفا بسته بندی ها را حذف و فرم جدید ایجاد نمایید.");
        }
        $opp_kind = OppKind::find($data["opp_kind_id"]);
        $trans_kind = TransKind::find($data["trans_kind_id"]);
        $cost_center = CostCenter::find($data["cost_center_id"]);
        $warehouse = Warehouse::find($data["warehouse_id"]);
        $description = $data["description"];
        if (!$description) {
            return back()->withErrors("لطفا اطلاعات فرم خروج را به صورت کامل تکمیل نمایید.");
        }

        $all_packing_form_data = PackingForm::where([
            "status_id" => 7007003,
            "warehouse_id" => $warehouse->id,
            "warehouse_status_id" => 4201
        ])->pluck("id", "id")->
        toArray();

        $packing_form_data2 = $all_packing_form_data;
        $packing_form_data2[] = -1;
        $all_transport_packing_form_ids = TransportPackingForm::whereIn("packing_form_id", array_keys($packing_form_data2))->pluck("transport_item_id", "packing_form_id")->toArray();
        $transport_count = TransportPackingForm::whereIn("packing_form_id", $packing_form_read_ids)->pluck("transport_item_id", "transport_item_id")->count();

        return view($this->view_path . "read_packing_forms", compact("opp_kind", "trans_kind", "cost_center", "warehouse", "description",
            "packing_form_read_ids", "all_packing_form_data", "all_transport_packing_form_ids", "user_id", "transport_count"));

    }

    public function remove_packing_data($count)
    {
        ;
        $data = ProductRequestFormSessionData::getDataByOtherId(Auth::id(),0,231,["packing_form_read_ids"=>[]]);
        if (count($data["packing_form_read_ids"]) == $count) {
            ProductRequestFormSessionData::removeDataByUserId(Auth::id());
            return back()->with(["success" => "اطلاعات قبلی با موفقیت حذف گردید، لطفا یک فرم جدید ثبت نمایید."]);
        }
        return back()->withErrors("اطلاعات فرم خروج جهت حذف نادرست است، لطفا یکبار دیگر تلاش کنید.");
    }

    public function show_list()
    {
        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);
        $packing_form_read_ids = [];
        $packing_form_read_ids_string = $data["packing_form_read_ids"];
        foreach ($packing_form_read_ids_string as $each_number) {
            $packing_form_read_ids[] = (int)$each_number;
        }

        $opp_kind = OppKind::find($data["opp_kind_id"]);
        $trans_kind = TransKind::find($data["trans_kind_id"]);
        $cost_center = CostCenter::find($data["cost_center_id"]);
        $warehouse = Warehouse::find($data["warehouse_id"]);
        $description = $data["description"];

        $packing_form_list = PackingForm::whereIn("id", $packing_form_read_ids)->get();

        return view($this->view_path . "submit", compact("packing_form_list",
            "packing_form_read_ids", "opp_kind", "trans_kind", "cost_center", "description"
        ));
    }

    public function confirm(Request $request)
    {
        $opp_kind = OppKind::find($request->opp_kind_id);

        $trans_kind = TransKind::find($request->trans_kind_id);
        $cost_center = CostCenter::find($request->cost_center_id);
        $description = $request->description;

        $packing_form_list_ids = json_decode($request->packing_form_read_ids);

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

        ProductRequestFormSessionData::removeDataByOtherId(Auth::id(),0,231);

        return redirect()->route($this->route_path . "index", $form)->with(["success" => "اطلاعات بسته بندی ها با فرم خروج " . $form->getCode() . "  با موفقیت ثبت گردید."]);
    }

    public function app_packing_api(Request $request)
    {

        $user_id = $request->user_id;
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);

        $data["packing_form_read_ids"] = $request->packing_form_read_ids;
        ProductRequestFormSessionData::setDataByOtherId($user_id,0,231, $data);

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


    /****************************** ثبت خروج دستی برای نوع بدون انبارش و بدون بسته بندی (دپو) **************************************/


    public function read_product_info_type4()
    {
        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);

        if (!isset($data["warehouse_storage_type_id"]) || $data["warehouse_storage_type_id"] != 4) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات نوع انبارش فرم نادرست است، لطفا یکبار دیگر تلاش کنید.");
        }


        $opp_kind = OppKind::find($data["opp_kind_id"]);
        $trans_kind = TransKind::find($data["trans_kind_id"]);
        $cost_center = CostCenter::find($data["cost_center_id"]);
        $warehouse = Warehouse::find($data["warehouse_id"]);
        $product_amount = isset($data["product_amount"]) ? $data["product_amount"] : [];
        $description = $data["description"];
        if (!$description) {
            return back()->withErrors("لطفا اطلاعات فرم خروج را به صورت کامل تکمیل نمایید.");
        }

        $max_row_for_exit_type4 = $this->read_product_info_type4;
        $product_option = Option::get("warehouse_storage_type_product", 0, 4);
        return view($this->view_path . "read_product_info_type4", compact("opp_kind", "max_row_for_exit_type4", "trans_kind", "cost_center", "warehouse", "description", "product_option", "product_amount"
            , "user_id",));

    }

    public function submit_read_product_info_type4(Request $request)
    {


        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,["packing_form_read_ids"=>[]]);

        if (!isset($data["warehouse_storage_type_id"]) || $data["warehouse_storage_type_id"] != 4) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات نوع انبارش فرم نادرست است، لطفا یکبار دیگر تلاش کنید.");
        }
       // return $request->all();
        $warehouse_id=$data["warehouse_id"];
        $max_row_for_exit_type4 = $this->read_product_info_type4;
        $product_ids_amount = [];
        $product_ids_lot_number_degree = [];
        for ($k = 0; $k < $max_row_for_exit_type4; $k++) {
            $product_key = "product_id_$k";
            $final_amount="final_amount_$k";
            $lot_number_key="lot_number_$k";
            if ($request->has($product_key) && $request->$product_key) {

                $product=Product::find($request->$product_key);
                if (!$request->has($final_amount)) {
                    return back()->withErrors("لطفا مقدار نهایی جهت خروج ".$product->fullCaption()."را مشخص نمایید.");
                }

                // بررسی لات کالا
                if (!$request->has($lot_number_key)) {
                    return back()->withErrors("لطفا لات ".$product->fullCaption()." جهت خروج را مشخص نمایید.");
                }
                $lot_number=LotNumber::where("product_id",$request->$product_key)->where("code",$request->$lot_number_key)->first();
                if(!$lot_number){

                    return back()->withErrors("لات ".$request->$lot_number_key." برای ".$product->fullCaption()." تعریف نشده است.");
                }


                if ($request->$final_amount+0 <=0) {

                    return back()->withErrors("مقدار نهایی برای خروج باید عددی بزرگتر از صفر باشد.");
                }

                if (isset($product_ids_amount[$request->$product_key])) {
                    return back()->withErrors("لطفا هر کالا را حداکثر در یک ردیف وارد نمایید.");
                }


                $product_ids_amount[$request->$product_key] = $request->$final_amount;

                $degree=Degree::where("goods_kind_id",$product->goods_kind_id)->where("degree_type_id",1)->first();
                $product_ids_lot_number_degree[$request->$product_key] = [
                    "lot_number_id"=>$lot_number->id,
                    "degree_id"=>$degree->id,
                    "lot_number_code"=>$lot_number->code,
                    "degree_code"=>$degree->caption,
                ]; //*** پیش فرض درجه اصلی

            }


        }

        $result=self::CheckInventoryType4($product_ids_amount,$product_ids_lot_number_degree,$warehouse_id);
        if(!$result["result"]){
            return back()->withErrors($result["error"]);
        }

        $product_list=Product::whereIn("id",array_keys($product_ids_amount))->get();
        $opp_kind = OppKind::find($data["opp_kind_id"]);
        $trans_kind = TransKind::find($data["trans_kind_id"]);
        $cost_center = CostCenter::find($data["cost_center_id"]);
        $warehouse = Warehouse::find($data["warehouse_id"]);
        $product_amount = isset($data["product_amount"]) ? $data["product_amount"] : [];
        $description = $data["description"];

        $data["product_ids_amount"]=$product_ids_amount;
        $data["product_ids_lot_number_degree"]=$product_ids_lot_number_degree;
         ProductRequestFormSessionData::setDataByOtherId($user_id,0,231,$data);
       return  view($this->view_path."submit_read_product_info_type4",compact("product_list","product_ids_lot_number_degree","product_ids_amount","opp_kind", "max_row_for_exit_type4", "trans_kind", "cost_center", "warehouse", "description", "product_amount"
            , "user_id"));

    }


    public function confirm_read_product_info_type4()
    {
        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id,0,231,[]);

        if (!isset($data["warehouse_storage_type_id"]) || $data["warehouse_storage_type_id"] != 4) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات نوع انبارش فرم نادرست است، لطفا یکبار دیگر تلاش کنید.");
        }

        $product_ids_amount=$data["product_ids_amount"];
        $product_ids_lot_number_degree=$data["product_ids_lot_number_degree"];

        $opp_kind = OppKind::find($data["opp_kind_id"]);
        $trans_kind = TransKind::find($data["trans_kind_id"]);
        $cost_center = CostCenter::find($data["cost_center_id"]);
        $warehouse = Warehouse::find($data["warehouse_id"]);
        $product_amount = isset($data["product_amount"]) ? $data["product_amount"] : [];
        $description = $data["description"];
        $result=self::CheckInventoryType4($product_ids_amount,$product_ids_lot_number_degree,$warehouse->id);

        if(!$result["result"]){
            return redirect()->route($this->route_path . "index")->withErrors($result["error"]);
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



        foreach ($product_ids_amount as $product_id => $final_amount) {

            $form_item = FormItem::create([
                "form_id" => $form->id,
                "product_id" => $product_id,
                "amount" => $final_amount,
                "sub_amount" => 0,
                "carrier_id" =>null,
                "degree_id" => $product_ids_lot_number_degree[$product_id]["degree_id"],
                "lot_number_id" =>  $product_ids_lot_number_degree[$product_id]["lot_number_id"],
                "packing_type_id" =>null,
                "packing_form_item_id" => null,
                "io_line_code" => 0,
                "product_request_form_item_id" => null,
                "description" => $description . " " . $form->code
            ]);


        }


        event(new PutInWarehouseEvent($form));
        $form->status_id = 500000200;
        $form->save();
        event(new FormLogEvent($form));


        ProductRequestFormSessionData::removeDataByOtherId(Auth::id(),0,231);

        return redirect()->route($this->route_path . "index", $form)->with(["success" => "اطلاعات کالاها با فرم خروج " . $form->getCode() . "  با موفقیت ثبت گردید."]);



        return $data;
    }

    public static function CheckInventoryType4($product_ids_amount,$product_ids_lot_number_degree,$warehouse_id)
    {



        $product_list=Product::whereIn("id",array_keys($product_ids_amount))->get()->keyBy("id");
        foreach ($product_ids_amount as $product_key => $product_value) {

             $product_inventory=  WarehouseProduct::getProductInventoryList(array_keys($product_ids_amount),
            $product_ids_lot_number_degree[$product_key]["degree_id"],
            $warehouse_id,
            null,
            $product_ids_lot_number_degree[$product_key]["lot_number_id"],
            6,"product_id",null,
            null,null,true);
            if(!isset($product_inventory[$product_key])){
                return [
                    "result"=>false,
                    "error"=>"مقدار موجودی کالای ".$product_list[$product_key]->fullCaption()." در انبار صفر می باشد و امکان ثبت خروج از انبار برای کالا امکان پذیر نمی باشد."
                ];

            }

            if($product_inventory[$product_key] - $product_ids_amount[$product_key] < 0){
                return [
                    "result"=>false,
                    "error"=>"مقدار موجودی کالای ".$product_list[$product_key]->fullCaption()." با توجه به درجه و همبافت انتخاب شده، در انبار به اندازه کافی نمی باشد."
                ];

            }
        }

        return [
            "result"=>true,
        ];
    }

}
