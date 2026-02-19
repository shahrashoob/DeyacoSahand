<?php

namespace App\Http\Controllers\Contractor\Admin;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionLog;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\Address\Address;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContractorAllocationController extends Controller
{
    public static $info = [
        "route" => "contractor.admin.contractor_allocation.",
        "view" => "contractor.admin.contractor_allocation.",
        "enable_status" => ["001", "002", "004", "006"],
        "button" => ["caption" => "تخصیص پیمانکار", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.admin.dashboard.";

    public function __construct()
    {
        $this->route_path = ContractorAllocationController::$info["route"];
        $this->view_path = ContractorAllocationController::$info["view"];
    }

    public function index(Production $production, $contractor = null, $production_channel_type = null)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $is_quick_allocation = $production_channel_type != null;
        if ($production->packing_types()->count() == 0) {
            return back()->withErrors("بسته بندی مجاز فروش برای پیمان مشخص نشده است، لطفا با پشتیبانی تماس بگیرد.");
        }

        $list = $production->product->line_product_station()->
        orderBy("id")->
        whereNotNull("contractor_operation_id")->
        groupBy("product_route_id")->
        get();
        $contractors = [];
        $contractor_id = null;
        $production_channel_type_id = null;
        $production_channel_type =
        $contractor_option = [];
        $it_is_coordination_for_sending = true;
        foreach ($list as $item) {
            $contractor_option[] = [
                "text" => $item->contractor->caption . " - " . $item->contractor_operation->caption,
                "value" => $item->id
            ];
            $contractors[$item->contractor_id] = $item->contractor_id;
            $contractor_id = $item->contractor_id;
            $production_channel_type_id = $item->production_channel_type_id;
            $production_channel_type = $item->production_channel_type;
            $it_is_coordination_for_sending = $item->contractor->it_is_coordination_for_sending;
        }

        $allocation_amount = $production->number - $production->get_allocation_amount(false, 3);

        if ($allocation_amount <= 0) {
            //  در انتظار تخصیص مجدد
            if ($production->waiting_status_id == 7008006) {
                $production->waiting_status_id = 7008002; // تولید توسط پیمانکار
                $production->save();
            }
            return back()->withErrors("کل مقدار کارت پیمان ".$production->serial."  به پیمانکار تخصیص داده شده است، و امکان تخصیص جدید وجود ندارد.");
        }
        if (count($contractors) != 1) {
            return back()->withErrors("با توجه به اینکه کالا بیش از یک مسیر محصول دارد، امکان تشخیص پیمانکار برای کارت پیمان ".$production->serial." وجود ندارد.");
        }

           $result = self::GetTogether($production, $production_channel_type_id, $contractor_id, $is_quick_allocation);
        $production_allocation_togethers = $result["production_allocation_togethers"];
        $product_bom_inventory = $result["product_bom_inventory"];
        $production_packing_forms_list = $result["production_packing_forms_list"];
        $production_selected_by_packing_forms_list = $result["production_selected_by_packing_forms_list"];


        return view($this->view_path . "index", compact("production_selected_by_packing_forms_list", "contractor_id", "production_packing_forms_list",
            "it_is_coordination_for_sending", "product_bom_inventory", "production_channel_type",
            "is_quick_allocation",
            "production_allocation_togethers", "production", "contractor_option", "allocation_amount"
        ));
    }

    public function submit(Request $request, Production $production)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $line_product_station = LineProductStation::find($request->line_product_station_id);
        $result = self::GetTogether($production, $line_product_station->production_channel_type_id, $line_product_station->contractor_id);
        $production_allocation_togethers = $result["production_allocation_togethers"];


        foreach ($production_allocation_togethers as $production_id => $item) {
            if (!isset($request->together_checkbox[$production_id])) {
                unset($production_allocation_togethers[$production_id]);
            } else {
                $production_allocation_togethers[$production_id]["allocation_amount"] = $request->together_allocation_amount[$production_id];

            }
        }


        $result_allocation = self::PostSubmit($production, $request->line_product_station_id, $request->production_allocation_amount, $request->message, null, $production_allocation_togethers);
        if ($result_allocation["result"]) {
            return redirect()->route($this->dashboard_route . "view_card", $production)->with(["success" => $result_allocation["message"]]);

        } else {
            $production->waiting_status_id = 7008006; //  در انتظار تخصیص مجدد
            $production->save();
            event(new ProductionCardLogEvent($production, $result_allocation["error"], null, 7008002));

            return back()->withErrors($result_allocation["error"]);
        }


    }

    /**
     * نمایش لیست بسته بندی های مجاز و ...
     **/
    public function show_product_inventory(Production $production, $other_production_id, Product $product, Contractor $contractor, ProductionChannelType $production_channel_type)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $other_production = Production::find($other_production_id);
        $material_ids = ConsumedProduct::whereIn("product_id", [$product->id])->pluck("material_id", "product_id")->toArray();
        //به دست آوردن بسته بندی های تحویل شده
        $list = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        where(
            "packing_forms.status_id", 7007003 //  تحویل شده به انبار
        )->
        whereIn("product_id", $material_ids)->
        groupBy("packing_forms.id")->
        selectRaw("packing_forms.code,packing_forms.status_id as status_id, packing_forms.id as id ,packing_type_id, product_id, sum(final_amount) as final_amount")->
        with("status", "packing_type")->
        where("packing_forms.status_id", 7007003)->
        paginate(30);
        $packing_form_ids = [];

        foreach ($list as $item) {
            $packing_form_ids[] = $item->id;
        }
        $packing_form_ids[] = -1;
        $packing_for_production1 = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
        with("production_form_item.production.parent_production")->get();

        $packing_for_production = [];
        foreach ($packing_for_production1 as $packing_for_production_item) {
            //  $packing_for_production_item->production_form_item->production->parent_production_id;
            if (($packing_for_production_item->production_form_item->production->parent_production_id ?? 0) == $other_production_id) {
                $packing_for_production[$packing_for_production_item->packing_form_id] = 1;
            }
        }

        // لیست بسته بندی هایی که قبلا انتخاب شده است.
        $selected_packing_form_ids = self::GetSelectedPackingList($production_channel_type);
        return view($this->view_path . "show_packing_form_inventory", compact(
            "selected_packing_form_ids", "contractor",
            "other_production", "packing_for_production","production_channel_type"
            , "production", 'other_production_id', "product", "list"));

    }

    public static function GetSelectedPackingList($production_channel_type)
    {
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ])->first();
        if (!$jsn_data_list) {
            return [];
        }
        $data = json_decode($jsn_data_list->data, true);
        $selected_packing_form_ids = isset($data["packing_form_ids"]) ? $data["packing_form_ids"] : [];

        return $selected_packing_form_ids;
    }

    /**
     * @param Request $request
     * @param Production $production
     * @param $other_production_id
     * @param Product $product
     * @param Contractor $contractor
     * @return \Illuminate\Http\RedirectResponse|string
     * انتخاب بسته بندی ها
     */
    public function submit_show_product_inventory(Request $request, Production $production, ProductionChannelType $production_channel_type)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        if (!$request->packing_form_ids) {
            return back()->withErrors("لطفا حداقل یک بسته بندی انتخاب نمایید.");
        }
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ],
            [
                "data" => "[]"
            ]);

        $data = json_decode($jsn_data_list->data, true);
        if (!isset($data["packing_form_ids"])) {
            $data["packing_form_ids"] = [];
        }
        $data["packing_form_ids"] = array_merge($data["packing_form_ids"], array_keys($request->packing_form_ids));
        $data["packing_form_ids"] = PackingForm::whereIn("id", $data["packing_form_ids"])->pluck("id")->toArray();

        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();

        return back()->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);
    }

//    public function select_packing_forms(Production $production, Contractor $contractor)
//    {
//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }
//
//        $line_product_station = $production->product->line_product_station()->
//        orderBy("id")->
//        whereNotNull("contractor_operation_id")->
//        where("contractor_id", $contractor->id)->
//        groupBy("product_route_id")->
//        first();
//
//
//        $packing_list_data = null;
//        $packing_list_transport_item = [];
//
//        $result = self::GetSelectPackingFormData($contractor, $line_product_station->production_channel_type, $production);
//
//        $packing_list_json_data = $result["packing_list_json_data"];
//        $selected_packing_ids = $result["selected_packing_ids"];
//        $count_select = $result["count_select"];
//        $allow_entry_with_pin = $result["allow_entry_with_pin"];
//
//        return view($this->view_path . "select_packing_forms",
//            compact("packing_list_data", "packing_list_transport_item",
//                "packing_list_json_data", "production",
//                "selected_packing_ids", "count_select", "allow_entry_with_pin",
//            )
//        );
//
//    }

    public function select_packing_form_api(Request $request)
    {
        $production_id = $request->production_id;
        $production_channel_type_id = $request->production_channel_type_id;


        $production_channel_type = Production::find($production_channel_type_id);
        if (!$request->packing_form_id) {
            return ("لطفا حداقل یک بسته بندی انتخاب نمایید.");
        }
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" =>  $production_channel_type->id,
                "message_type_id" =>  602,
            ],
            [
                "data" => "[]"
            ]);

        $data = json_decode($jsn_data_list->data, true);
        if (!isset($data["packing_form_ids"])) {
            $data["packing_form_ids"] = [];
        }
        $data["packing_form_ids"] = array_merge($data["packing_form_ids"], [$request->packing_form_id + 0]);

        $data["packing_form_ids"] = PackingForm::whereIn("id", $data["packing_form_ids"])->pluck("id")->toArray();
        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();

        return "ok";
    }

    /**
     * حذف بسته بنیدی های انتخاب شده
     **/
    public function remove_packing_forms(Production $production, PackingForm $packing_form,ProductionChannelType $production_channel_type)
    {
        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ],
            [
                "data" => "[]"
            ]);

        $data = json_decode($jsn_data_list->data, true);
        if (!isset($data["packing_form_ids"])) {
            $data["packing_form_ids"] = [];
        }
        $data["packing_form_ids"] = array_filter($data["packing_form_ids"], fn($value) => $value !== $packing_form->id);

        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();
        return back()->with(["success" => "بسته بندی با موفقیت حذف گردید."]);
    }

    public static function GetOtherProduction($production_channel_type_id, $contractor_id,$production){

        $product_ids = LineProductStation::
        where("production_channel_type_id", $production_channel_type_id)->
        where("contractor_id", $contractor_id)->
        pluck("product_id")->toArray();

        $production_list = Production::
        whereIn("product_id", $product_ids)->
        whereIn("waiting_status_id", [7008001,7008002,7008006])->
        where("production_type_id", 1)->
        when($production, function ($query) use ($production) {
            $query->where("id", "!=", $production->id);
        })->
        select("*")-> // "product_id", "id", "serial", "number", "status_id"
        get();

        $production_allocation_togethers = [];

        foreach ($production_list as $production_item) {
            $allocation_amount_new = $production_item->number - $production_item->get_allocation_amount();
            $production_allocation_togethers[$production_item->id] = [
                "machine_allocation" => null,
                "production" => $production_item,
                "allocation_amount" => $allocation_amount_new,
            ];
        }

        return [
            "product_ids"=>$product_ids,
            "production_allocation_togethers"=>$production_allocation_togethers
        ];
    }
    public static function GetTogether($production, $production_channel_type_id, $contractor_id, $is_quick_allocation = false)
    {

        $result=self::GetOtherProduction($production_channel_type_id, $contractor_id, $production);
        $product_ids=$result["product_ids"];
        $production_allocation_togethers=$result["production_allocation_togethers"];
        // گرفتن لیست دیگر کارت هایی که با این کالا هم کانال هستند.

        $bom_items = BOMItem::whereIn("product_id", $product_ids)->select("product_id", "material_id", "amount")->get();
        $material_ids = [];
        $product_bom_inventory = []; // موجودی BOM هر کالایی
        $bom_item_amount = [];
        foreach ($bom_items as $bom_item) {
            $bom_item_amount[$bom_item->product_id] = $bom_item->amount;
            $material_ids[] = $bom_item->material_id;
        }
        $material_inventory = WarehouseProduct::getProductInventoryList($material_ids);
        foreach ($bom_items as $bom_item) {
            $product_bom_inventory[$bom_item->product_id] = $material_inventory[$bom_item->material_id];
        }
// به دست آوردن موجودی بسته بندی هایی که در لیست وجود دارد.
        $packing_form_list = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("packing_forms.status_id", 7007003)-> // تحویل شده به انبار
        where("packing_forms.warehouse_status_id", 4201)-> // تحویل شده به انبار
        whereIn("product_id", $material_ids)->
        select("packing_form_item.id", "final_amount", "production_form_item_id")->
        with("production_form_item.production.parent_production")->get();
        $production_packing_forms_list = [];
        foreach ($packing_form_list as $packing_form_item) {
            $parent_production_id = $packing_form_item->production_form_item->production->parent_production_id ?? 0;
            if (!isset($production_packing_forms_list[$parent_production_id])) {
                $production_packing_forms_list[$parent_production_id] = 0;
            }
            if ($parent_production_id > 0) {
                $production_packing_forms_list[$parent_production_id] += $packing_form_item->final_amount;
            }
        }

        foreach ($production_allocation_togethers as $key => $item) {
            if (!isset($production_packing_forms_list[$key])) {
                $production_packing_forms_list[$key] = 0;
            }
        }

        if ($production && !isset($production_packing_forms_list[$production->id])) {
            $production_packing_forms_list[$production->id] = 0;
        }

        // بسته بندی های انتخاب شده
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" =>  $production_channel_type_id ,
                "message_type_id" => 602 ,
            ],
            [
                "data" => "[]"
            ]);

        $data = json_decode($jsn_data_list->data, true);
        if (!isset($data["packing_form_ids"])) {
            $data["packing_form_ids"] = [];
        }
         // بعضی از بسته بندی ها، کارت سطح بالای آنها تغییر کرده و باید به جای کارت پیمان خودشان، کارت دیگری را انتخاب کنیم.
        if (!isset($data["special_licence_packing_forms"])) {
            $data["special_licence_packing_forms"] = [];
        }
        $special_licence_packing_forms=$data["special_licence_packing_forms"];

        $list = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where(
            "packing_forms.status_id", 7007003 //  تحویل شده به انبار
        )->
        whereIn("packing_form_id", $data["packing_form_ids"])->
        groupBy("product_id")->
        groupBy("packing_forms.id")->
        selectRaw("packing_forms.code,packing_forms.status_id as status_id, packing_forms.id as packing_form_id ,packing_type_id, product_id, sum(final_amount) as final_amount,production_form_item_id")->
        where("packing_forms.status_id", 7007003)->
        with("production_form_item.production.parent_production")->
        get();

        $production_selected_by_packing_forms_list = [];
        foreach ($list as $item) {
            $parent_production_id = $item->production_form_item->production->parent_production_id ?? 0;
            $parent_product_id = $item->production_form_item->production->parent_production->product_id ?? 0;

            if(isset($special_licence_packing_forms[$item->packing_form_id])){
                $parent_production_id = $special_licence_packing_forms[$item->packing_form_id]["production_id"];
                $parent_product_id = $special_licence_packing_forms[$item->packing_form_id]["product_id"];

            }
            if (!isset($production_selected_by_packing_forms_list[$parent_production_id])) {
                $production_selected_by_packing_forms_list[$parent_production_id] = 0;
            }
            $production_selected_by_packing_forms_list[$parent_production_id] +=
                round($item->final_amount / (isset($bom_item_amount[$parent_product_id]) ? $bom_item_amount[$parent_product_id] : 2), 2);
        }

        return [
            "production_allocation_togethers" => $production_allocation_togethers,
            "product_ids" => $product_ids,
            "product_bom_inventory" => $product_bom_inventory,
            "production_packing_forms_list" => $production_packing_forms_list,
            "production_selected_by_packing_forms_list" => $production_selected_by_packing_forms_list,
            "bom_item_amount" => $bom_item_amount
        ];
    }

    public static function GetSelectPackingFormData(Contractor $contractor, ProductionChannelType $productionChannelType, $production, $is_quick_allocation = false)
    {
        $result = self::GetTogether($production ?? null, $productionChannelType->id, $contractor->id, $is_quick_allocation);

        $product_ids = $result["product_ids"];
        $material_ids = ConsumedProduct::whereIn("product_id", $product_ids)->pluck("material_id", "product_id")->toArray();
        //به دست آوردن بسته بندی های تحویل شده
        $packing_form_ids = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        where(
            "packing_forms.status_id", 7007003 //  تحویل شده به انبار
        )->
        whereIn("product_id", $material_ids)->
        groupBy("packing_forms.id")->
        selectRaw("packing_forms.id as id ")->
        where("packing_forms.status_id", 7007003)->
        pluck("id", "id")->toArray();

        $packing_form_ids[] = -1;
        if (count($packing_form_ids) > 400) {
            return [
                "result"=>false,
                "error"=>" با توجه به اینکه تعداد بسته بندی موجود در انبار بیش از 400 بسته بندی است، امکان انتخاب بسته بندی ها وجود ندارد. "
            ];

        }
        $packing_for_production1 = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
        with("production_form_item.production.parent_production")->get();

        $production_allocation_togethers = $result["production_allocation_togethers"];
        $production_allocation_togethers_ids = array_keys($production_allocation_togethers);
        $packing_for_production = [];
        foreach ($packing_for_production1 as $packing_for_production_item) {
            $parent_production_id = $packing_for_production_item->production_form_item->production->parent_production_id??0;

            if (in_array($parent_production_id, $production_allocation_togethers_ids)) {
                $packing_for_production[$packing_for_production_item->packing_form_id] = 1;
            }
        }

        // به دست آوردن انبار
        $line_product_station = LineProductStation::
        where("production_channel_type_id", $productionChannelType->id)->
        where("contractor_id", $contractor->id)->
        first();
        $bom_item = BOMItem::where([
            "product_id" => $line_product_station->product_id ?? 0,
        ])->
        first();
        $allow_entry_with_pin = $bom_item->warehouse->allow_entry_with_pin ?? 1;

        $packing_list_json_data = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        whereIn("packing_forms.id", $packing_form_ids)->
        groupBy("packing_forms.id")->
        selectRaw("packing_forms.id,packing_forms.code,packing_type_id, carrier_id,
         sub_packing_form_number,sum(final_amount) as final_amount,pin1")->
        get()->keyBy(
            $allow_entry_with_pin ? "pin1" : "code"
        );


        // لیست بسته بندی هایی که قبلا انتخاب شده است.
        $selected_packing_ids = self::GetSelectedPackingList($productionChannelType);
        $selected_packing_ids = array_values($selected_packing_ids);
        $count_select = count($selected_packing_ids);

        return [
            "result"=>true,
            "count_select" => $count_select,
            "selected_packing_ids" => $selected_packing_ids,
            "packing_list_json_data" => $packing_list_json_data,
            "allow_entry_with_pin" => $allow_entry_with_pin,
        ];

    }

    public static function PostSubmit(Production $production, $line_product_station_id, $allocation_amount, $message, $user_id = null, $production_allocation_togethers = [])
    {
        $message_log = "";
        $allocation_amount_remaining = $production->number - $production->get_allocation_amount(false, 3);
        if ($allocation_amount_remaining <= 0) {

            //  در انتظار تخصیص مجدد
            if ($production->waiting_status_id == 7008006) {
                $production->waiting_status_id = 7008002; // تولید توسط پیمانکار
                $production->save();
            }
            return [
                "result" => false,
                "error" => "کل مقدار کارت ".$production->serial()." به پیمانکار تخصیص داده شده است، و امکان تخصیص جدید وجود ندارد."
            ];

        }
        if ($allocation_amount > $allocation_amount_remaining) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص درخواست شده از  مقدار باقی مانده کارت پیمان ".$production->serial()." بیشتر است."
            ];
        }
        if ($allocation_amount +0 <= 0) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص برای کارت پیمان ".$production->serial()." نامعتبر است ( مقدار تخصیص نمی تواند مقدار کوچکتر مساوی صفر باشد)"
            ];
        }
        foreach ($production_allocation_togethers as $allocation_together) {

            $allocation_amount_remaining =  $allocation_together["production"]->number - $allocation_together["production"]->get_allocation_amount(false, 3);
            if ($allocation_amount_remaining <= 0) {
                return [
                    "result" => false,
                    "error" => "کل مقدار کارت ".$allocation_together["production"]->serial()." به پیمانکار تخصیص داده شده است، و امکان تخصیص جدید وجود ندارد."
                ];
            }

        if ( $allocation_together["allocation_amount"] <=0) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص برای کارت پیمان ".$allocation_together["production"]->serial()." نامعتبر است ( مقدار تخصیص نمی تواند مقدار کوچکتر مساوی صفر باشد)"
            ];
        }
        if ( $allocation_together["allocation_amount"] > $allocation_amount_remaining) {
            return [
                "result" => false,
                "error" => "مقدار تخصیص درخواست شده از  مقدار باقی مانده کارت پیمان ".$allocation_together["production"]->serial()." بیشتر است."
            ];
        }
        }
        $line_product_station = LineProductStation::find($line_product_station_id);
        if (!$line_product_station) {
            return [
                "result" => false,
                "error" => "پیمانکار جهت تخصیص نامعتبر است."
            ];

        }

        if (!$line_product_station->production_channel_type) {
            return [
                "result" => false,
                "error" => "کانال پیمان برای مسیر محصول پیمانکاری مشخص نشده است، لطفا با مسئول اطلاعات پایه تماس بگیرید."
            ];
        }

        $bom = BOM::where("product_route_id", $line_product_station->product_route_id)->first();
        if (!$bom) {
            return [
                "result" => false,
                "error" => "BOM کالا(" . $production->product->caption . ") برای " . $line_product_station->route->caption . " تعریف نشده است."
            ];

        }
        if (count($bom->items) == 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه هیچ ردیف BOMی برای کالا تعریف نشده است، امکان تخصیص وجود ندارد."
            ];

        }


        // چک کردن BOM در دیگر مسیر ها
//        foreach ($production_allocation_togethers as $item){
//            $bom_together = BOM::where("product_id", $item->production->product_id)->first();
//            if (!$bom_together) {
//                return [
//                    "result" => false,
//                    "error" => "BOM کالا(" . $item->production->product->caption . ") برای " . "مسیر پیمانکاری" . " تعریف نشده است."
//                ];
//
//            }
//            if (count($bom_toghether->items) == 0) {
//                return [
//                    "result" => false,
//                    "error" => "با توجه به اینکه هیچ ردیف BOMی برای کالا تعریف نشده است، امکان تخصیص وجود ندارد."
//                ];
//
//            }
//        }
        $contractor = $line_product_station->contractor;
        // بررسی تاریخ قرارداد
        if (!$contractor->end_date_of_contract || !$contractor->start_date_of_contract) {
            return [
                "result" => false,
                "error" => "تاریخ شروع و پایان قرارداد برای پیمانکار ثبت نشده است، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        }
        if (
            Carbon::now()->greaterThan(Carbon::parse($contractor->end_date_of_contract)) ||
            Carbon::now()->lessThan(Carbon::parse($contractor->start_date_of_contract))
        ) {
            return [
                "result" => false,
                "error" => "تاریخ قرارداد با پیمانکار نامعتبر است، لطفا نسبت به تمدید قرارداد با پیمانکار اقدامات لازم مبذول فرمایید. "
            ];
        }

        // بررسی ارتباط با سامانه پیمانکار
        if ($contractor->software_system_id) {
            $result_api_login = SoftwareSystem::Login($contractor->software_system, $contractor->api_url, $contractor->api_username, $contractor->api_password, $contractor->api_key);
            if (!$result_api_login["result"]) {
                return [
                    "result" => false,
                    "error" => $result_api_login["message"] . "<br/>" . " (" . $contractor->software_system->caption . " در " . $contractor->caption . ")"
                ];
            }

            // نمونه گیری
            if ($production->production_type_id == 2) {
                // ثبت درخواست طراحی کالا
                $result_api_product_creation = SoftwareSystem::CallNewProductCreation(
                    $contractor->software_system,
                    $contractor->api_url,
                    $result_api_login["token"],
                    $production
                );

                if (!$result_api_product_creation["result"]) {
                    SoftwareSystem::Logout(
                        $contractor->software_system,
                        $contractor->api_url,
                        $result_api_login["token"]
                    );
                    return [
                        "result" => false,
                        "error" => $result_api_product_creation["message"]
                    ];
                }
                $message_log = $result_api_product_creation["message"];
            } else {

                // پیدا کردن کد کالا در سامانه مشتری
                $product_code_in_contractor_system = $line_product_station->product_code_in_contractor_system;

                // به دست آوردن آدرس ارسال
                $address = null;
                if ($contractor->sent_address_place_type_of_transport == 1) {
                    $list_setting = Setting::whereIn("id", [83, 122, 123, 124, 125, 126])->get()->keyBy("key");

                    if (
                        $list_setting["company_address"]->string_value == "" ||
                        $list_setting["company_postal_code"]->string_value == "" ||
                        $list_setting["company_phone_number"]->string_value == ""
                    ) {
                        return [
                            "result" => false,
                            "error" => "آدرس شرکت در تنظیمات اولیه نامعتبر است، "
                        ];
                    }
                    $address = new Address([
                        "country_id" => $list_setting["company_country_id"]->integer_value,
                        "province_id" => $list_setting["company_province_id"]->integer_value,
                        "address" => $list_setting["company_address"]->string_value,
                        "postal_code" => $list_setting["company_postal_code"]->string_value,
                        "city_name" => $list_setting["company_city_name"]->string_value,
                        "phone" => $list_setting["company_phone_number"]->string_value,
                    ]);
                } else {
                    $address = $production->order->address ?? null;
                    if (!$address) {
                        return [
                            "result" => false,
                            "error" => "آدرس سفارش مربوط به دستور پیمان " . $production->serial() . " نامعتبر است. "
                        ];
                    }
                }

                // به دست آوردن تاریخ تحویل به مشتری
                if ($contractor->is_order_registration_date_chosen_by_contractor) {
                    // ایا تاریخ ثبت سفارش توسط پیمانکار انتخاب شود
                    // بله
                    $delivery_datetime = Carbon::now();
                } else {
                    // تاریخ تحویل کارت پیمان - مدت زمان پیش فرض تحویل کالا توسط پیمانکار
                    $delivery_datetime = Carbon::parse($production->max_delivery_datetime)->
                    addDay(-$contractor->duration_of_default_of_product);

                }

                // اگر تاریخ تحویل کوچکتر از تاریخ حال بود، تاریخ فردا را ست می کنیم.
                if (Carbon::now()->greaterThan($delivery_datetime)) {
                    $delivery_datetime = Carbon::now()->addDay();
                }

                $result_degree = Degree::getMainDegree($production->product->goods_kind_id);
                if (!$result_degree["result"]) {
                    return $result_degree;
                }
                $degree_ids = [$result_degree["degree"]["code"]];

                // بسته بندی های تولید شده از کارت سطح پایان
                $packing_form_data = [];
                $child_production_ids = Production::where("parent_production_id", $production->id)->pluck("id")->toArray();
                if (count($child_production_ids) > 0) {
                    $child_production_form_item_ids = ProductionFormItem::whereIn("production_id", $child_production_ids)->pluck("id")->toArray();
                    if (count($child_production_form_item_ids) > 0) {
                        // لیست بسته بندی هایی که برای پیمانکار ارسال می شود.
                        $child_packing_form_items = PackingFormItem::
                        whereIn("production_form_item_id", $child_production_form_item_ids)->
                        with("packing_form", "product", "lot_number", "degree")->
                        get();


                        if (count($child_packing_form_items) > 0) {
                            foreach ($child_packing_form_items as $child_packing_form_item) {


                                // ردیف هایی معتبر است که پدرشان نال باشد و در وضعیت های غیر مجاز نباشند
                                if (
                                    !in_array($child_packing_form_item->packing_form->status_id, [
                                        7007006, // معلق
                                        7007007, // تغییر یافته
                                        7007011, // معلق
                                        7007012, // خارج شده
                                        7007019, // مصرف شده
                                        7007021, // ادغام شده
                                    ])

                                ) {

                                    $packing_form_data[$child_packing_form_item->id] = [
                                        "code" => $child_packing_form_item->packing_form->code,
                                        "gross_weight" => $child_packing_form_item->packing_form->gross_weight,
                                        "weight" => $child_packing_form_item->packing_form->weight,
                                        "packing_type_id" => $child_packing_form_item->packing_form->packing_type_id
                                    ];
                                    if (!isset($packing_form_data[$child_packing_form_item->id]["packing_form_item"])) {
                                        $packing_form_data[$child_packing_form_item->id]["packing_form_item"] = [];
                                    }

                                    $packing_form_item_data["amount"] = $child_packing_form_item->final_amount;
                                    $packing_form_item_data["sub_amount"] = $child_packing_form_item->sub_amount;
                                    $packing_form_item_data["product_code"] = $child_packing_form_item->product->code;
                                    $packing_form_item_data["lot_number_code"] = $child_packing_form_item->lot_number->code;
                                    $packing_form_item_data["degree_code"] = $child_packing_form_item->degree->code;
                                    $packing_form_item_data["band_code"] = $child_packing_form_item->band_code;
                                    $packing_form_item_data["goods_kind_id"] = $child_packing_form_item->product->goods_kind_id;
                                    $packing_form_data[$child_packing_form_item->id]["packing_form_item"][] = $packing_form_item_data;

                                }

                            }
                        }
                    }
                }
//return   [
//    [
//        "product_code" => $product_code_in_contractor_system,
//        // لیست بسته بندی های مجاز
//        "packing_type_ids" => $production->packing_types()->pluck("packing_type_id")->toArray(),
//        "degree_codes" => $degree_ids,
//        "production_id_in_source" => $production->id,
//        "product_id_in_source" => $production->product_id,
//        "amount" => $allocation_amount,
//        "message" => $message,
//        "tracking_code1" => ($production->order ? $production->order->code() : null), // کد پیگیری 1 (شماره سفارش)
//        "tracking_code2" => $production->serial ?? null, //  کد پیگیری 2 (دستور پیمان)
//        "packing_form_data" => $packing_form_data
//    ]
//];
                // کال کردن API ثبت سفارش در سامانه پیمانکار
                $result_api_order_registration = SoftwareSystem::CallOrderRegistration(
                    $contractor->software_system,
                    $contractor->api_url,
                    $contractor->api_username,
                    $result_api_login["token"],
                    [
                        [
                            "product_code" => $product_code_in_contractor_system,
                            // لیست بسته بندی های مجاز
                            "packing_type_ids" => $production->packing_types()->pluck("packing_type_id")->toArray(),
                            "degree_codes" => $degree_ids,
                            "production_id_in_source" => $production->id,
                            "product_id_in_source" => $production->product_id,
                            "amount" => $allocation_amount,
                            "message" => $message,
                            "tracking_code1" => ($production->order ? $production->order->code() : null), // کد پیگیری 1 (شماره سفارش)
                            "tracking_code2" => $production->serial ?? null, //  کد پیگیری 2 (دستور پیمان)
                            "packing_form_data" => $packing_form_data
                        ]
                    ],
                    $address,
                    $delivery_datetime
                );

                if (!$result_api_order_registration["result"]) {
                    SoftwareSystem::Logout(
                        $contractor->software_system,
                        $contractor->api_url,
                        $result_api_login["token"]
                    );
                    return [
                        "result" => false,
                        "error" => "خطای " . $contractor->software_system->caption . " در " . $contractor->caption . ":<br/>" . $result_api_order_registration["error"]
                    ];
                }
                $message_log = $result_api_order_registration["message"];
            }

            // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
            SoftwareSystem::Logout(
                $contractor->software_system,
                $contractor->api_url,
                $result_api_login["token"]
            );
        }

        $it_is_coordination_for_sending = $line_product_station->contractor->it_is_coordination_for_sending;
        $allocation_status_id = $it_is_coordination_for_sending ?
            5310107 // در انتظار هماهنگی جهت ارسال
            :
            5310103 // در انتظار تحویل مواد اولیه
        ;
        $production_status_id = $it_is_coordination_for_sending ? 7008004 : 7008002; // در انتظار هماهنگی - تولید توسط ** پیمانکار


        $allocation = Allocation::create([
            "contractor_id" => $line_product_station->contractor_id,
            "status_id" => $allocation_status_id
        ]);

        $contractor_allocation = ContractorAllocation::create([
            "allocation_id" => $allocation->id,
            "production_id" => $production->id,
            "contractor_id" => $line_product_station->contractor_id,
            "product_id" => $production->product_id,
            "status_id" => $allocation_status_id,
            "user_id" => Auth::user()->id ?? $user_id,
            "allocation_amount" => $allocation_amount
        ]);

        if ($contractor_allocation->production->waiting_status_id == 7008001) {
            $contractor_allocation->production->waiting_status_id = $production_status_id;
            $contractor_allocation->production->save();
            event(new ProductionCardLogEvent($contractor_allocation->production, "", $user_id));
        }

        event(new ContractorLogEvent($line_product_station->contractor, 5310107, $production, $contractor_allocation, $message_log, $user_id));


        // در صورتی که چند تخصیص به صورت همزمان تخصیص داده شود، این بخش اجرا می شود.
        foreach ($production_allocation_togethers as $allocation_together) {
            $contractor_allocation_together = ContractorAllocation::create([
                "allocation_id" => $allocation->id,
                "production_id" => $allocation_together["production"]->id,
                "contractor_id" => $line_product_station->contractor_id,
                "product_id" => $allocation_together["production"]->product_id,
                "status_id" => $allocation_status_id,
                "user_id" => Auth::user()->id ?? $user_id,
                "allocation_amount" => $allocation_together["allocation_amount"]
            ]);

            if ($allocation_together["production"]->waiting_status_id == 7008001) {
                $allocation_together["production"]->waiting_status_id = $production_status_id;
                $allocation_together["production"]->save();
                event(new ProductionCardLogEvent($allocation_together["production"], "", $user_id));
            }

            event(new ContractorLogEvent($line_product_station->contractor, 5310107, $allocation_together["production"], $contractor_allocation_together, $message_log, $user_id));


        }

        if (!$it_is_coordination_for_sending) {
            // اگر هماهنگی لازم ندارد، درخواست کالا را ارسال می کند.
            $other["line_product_station"] = $line_product_station;
            $other["user_id"] = Auth::user()->id ?? $user_id;
            $result_product_request = ProductRequestForm::newRequest(
                $contractor_allocation->allocation, $contractor_allocation->contractor->id,
                20, 1, $other,
                Carbon::now(),
                $message
            );

            // درخواست کالا از اانبار موفقیت آمیز بود و در درخواست های همراه پیمانکار بسته بندی انتخاب شده بود، آنها را در درخواست اضافه می کنیم.
            if (isset($result_product_request["product_request_form"])) {

                $product_request_form = $result_product_request["product_request_form"];
                $selected_packing_ids = self::GetSelectedPackingList($line_product_station->production_channel_type);
                if (count($selected_packing_ids) > 0) {
                    // اضافه کردن بسته بندی به درخواست
                    $session_data = Product\ProductRequest\ProductRequestFormSessionData::
                    getData($product_request_form);
                    $session_data["selected_packing_ids"] = $selected_packing_ids;
                    Product\ProductRequest\ProductRequestFormSessionData::
                    setData($product_request_form, $session_data);

                    // حذف اطلاعات بسته بندی مربوط به پیمانکار
                    $jsn_data_list = JsonDataList::where(
                        [
                            "other_id" => $line_product_station->production_channel_type->id,
                            "message_type_id" => 602,
                        ])->delete();
                }

            }

        }

        // ارسال پیامک
        $company_name = Setting::getStringValue("company_name"); //
        $address = $line_product_station->contractor->getDefaultAddress();
        if (!$address) {
            return [
                "result" => true,
                "message" => "تخصیص با موفقیت ثبت گردید، اطلاعات تماس پیمانکار جهت ارسال پیامک معتبر نمی باشد.",
                "production_id" => $production->id,
                "allocation_id" => $allocation->id
            ];

        }
        Notification::send(
            "00" . ($address->mobile_country->area_code ?? "98") . $address->mobile,
            new SMSNotification("contractorallocation",
                $production->product->caption,
                $allocation_amount,
                $contractor_allocation->production->serial(),
                $line_product_station->contractor->fullName(),
                $company_name
            )
        );

        return [
            "result" => true,
            "message" => "تخصیص با موفقیت ثبت گردید.",
            "production_id" => $production->id,
            "allocation_id" => $allocation->id
        ];


    }

    public function checkPermission(Production $production)
    {

        $result = DashboardController::checkPermissionConditions($production, ContractorAllocationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
