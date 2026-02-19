<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModificationForm;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Status;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingGoodsKindTimeLimit;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LineProduct\Machine\Allocation\Modification;
use Illuminate\Support\Facades\Auth;


class GeneralMaterialReturnToWarehouseController extends Controller
{
    // goods_kind_process/general/machine/material_return_to_warehouse/
// برگشت مواد اولیه
    var $view_path = "goods_kind_process.general.machine.material_return_to_warehouse.";
    var $route_path;
    var $dashboard_route;
    var $route_log_path;

    public function __construct()
    {

    }

    public function index(Machine $machine)
    {
        //چک کردن پرینتر
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $in_action = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id])->
        whereNotIn("status_id", [6021001, 6021003])-> // خاتمه یافته
        first();
        if ($in_action) {
            return redirect()->route($this->route_log_path . "index", $machine)->withErrors("با توجه به اینکه سامانه در حال پردازش درخواست برگشت مواد اولیه شماره " . $in_action->id . " است، امکان ثبت وجود ندارد، لطفا چند دقیقه دیگر اقدام کنید.  ");
        }

        /************************************/
        // گرفتن باسکول
        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object_value = $result_smart_object["smart_object_value"];
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/

        $material_list = self::getMaterialList($machine, $machine->warehouse);

        if (count($material_list) == 0) {
            return redirect()->route($this->route_log_path . "index", $machine)->withErrors("هیچ کالایی برای خروج از انبار وجود ندارد.");
        }


        // چک کردن اینکه آیا نیاز است، کالایی انبار گردانی شود؟
        $result_handling = self::HasAnyWarehouseHandling($machine);
        if ($result_handling["result"]) {
            session(["result_handling" => $result_handling]);
            return redirect()->route($this->route_path . "warehouse_handling", [
                $machine,
                $result_handling["warehouse_id"]
            ]);
        }
        if (isset($result_handling["error"])) {
            return back()->withErrors($result_handling["error"]);
        }

        $consumed_list = session("consumed");
        $gross_weight_list = session("gross_weight");
        $sub_packing_form_number_list = session("sub_packing_form_number");

        // گرفتن درخواست معلق یا ایجاد آن
        $modification_temp = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id, "warehouse_id" => $machine->warehouse_id, "status_id" => 6021001])->
        firstOrCreate([
            "machine_id" => $machine->id,
            "warehouse_id" => $machine->warehouse_id,
            "status_id" => 6021001
        ]);

        $modification_temp->machine_allocation_modification_type_id = 1;
        // اگر انبار گردانی است نیاز نیست موجودی را انبارک را چک کند.
        $modification_temp->check_inventory_for_calculate_actual_consumption = 1;
        $modification_temp->created_at = Carbon::now();
        $modification_temp->save();

        $modification_temp->change_grades()->delete();
        $modification_temp->change_wastes()->delete();


        $goods_kind_ids = [];
        $product_ids = [];


        foreach ($material_list as $item) {


            $product_ids[] = $item->id;

            $goods_kind_ids[$item->id] = $item->goods_kind_id;
            $waste_list = Product\Waste\ProductWaste::where("product_id", $item->id)->get();
            foreach ($waste_list as $product_waste) {

                if (!Unit::HasWeightUnit($product_waste->waste)) {
                    return back()->withErrors("با توجه به اینکه هیچ کدام از واحدهای اصلی و فرعی " . $product_waste->waste->caption . " از نوع وزنی نیستند، امکان انجام عملیات وجود ندارد.");
                }
                $product_ids[] = $product_waste->waste_id;
                $goods_kind_ids[$product_waste->waste_id] = $product_waste->waste->goods_kind_id;
            }
        }

        // ممکن است یک کالا قبلا در برگشت بوده باشد و الان دیگه لازم نیست باشد،پس اگر وارد شده آن را حذف می کنیم.

        $has_sub_packing_type = PackingType::HasSubPackingType($product_ids);


        $product_option = Option::get("product_from_list", 0, 0, $material_list, "");

        // به دست آوردن لیست کالاهایی که در کالای جاری و رزرو مصرف می شود
        $other_product = session("other_reserve_material_list" . $modification_temp->id);
        if (!$other_product) {
            $other_product = [];
        }
        $current_product_ids = $this::getCurrentAndReservedProductId($machine, $other_product);

        // ارسال درخواست برای کدام رسته های کالایی فعال است.
        $goods_kind_ids_allow = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereAllowReturnToWarehouse($machine);
        $goods_kind_ids_allow[] = -1;

        // بسته بندی های که در انبار گردانی وجود دارد ولی نباید برگشت داده شود را از انبار گردانی حذف می کنیم.
        $modification_temp->
        packing_forms()->
        whereIn("product_id", $current_product_ids)->
        delete();

        // لیست بسته بندی هایی که کالای آنها در کارت رزرو و جاری نیست و در انبارک ماشین موجود هستند و باید خارج شوند
        $packing_form_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "product_id")->
        where("warehouse_id", $machine->warehouse->id ?? 0)->
        whereNotIn("product_id", $current_product_ids)->
        whereIn("goods_kind_id", $goods_kind_ids_allow)->
        select("packing_forms.*")->
        get();

        $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);

        session(["master_packing_form_ids" => $master_packing_form_ids]);

        $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get();

        $master_packing_form_group_by_product = PackingFormItem::
        whereIn("packing_form_id", $master_packing_form_ids)->
        groupBy("product_id")->
        selectRaw("product_id,count(distinct(packing_form_id)) as count")->
        pluck("count", "product_id")->
        toArray();

        $modification_packing_form_group_by_product[6021101] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021101)->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();
        $modification_packing_form_group_by_product[6021102] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021102)->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();

        $modification_packing_form_group_by_product[6021103] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021103)->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();

        // به دست آوردن لات های هر کالا
        $packing_form_ids_lot_number = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $machine->warehouse->id ?? 0)->
        pluck("packing_forms.id")->
        toArray();

//        $master_packing_form_ids_lot_number = PackingForm::MasterPackingFormIds( $packing_form_list_lot_number );
        $lot_number_option = $this->getLotNumberOption($packing_form_ids_lot_number);


        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        $route_log_path = $this->route_log_path;
        $warehouse = $machine->warehouse;
        return view($this->view_path . "index", compact(
            "product_option", "modification_temp", "master_packing_form_list",
            "machine", "goods_kind_ids", "material_list",
            "route_path", "dashboard_route", "lot_number_option", "has_sub_packing_type",
            "consumed_list", "gross_weight_list", "sub_packing_form_number_list",
            "warehouse", "smart_object", "url_scale", "route_log_path", "master_packing_form_group_by_product",
            "modification_packing_form_group_by_product"
        ));


    }

    public function remove_product_from_list(Machine $machine, Product $product)
    {
        $modification_temp = Modification\MachineAllocationModification::
        where([
            "machine_id" => $machine->id ?? 0,
            "warehouse_id" => $machine->warehouse_id ?? 0,
            "status_id" => 6021001
        ])->first();
        if (!$modification_temp) {
            return back()->withErrors("شناسه برگشت مواد اولیه نامعتبر است، لطفا مجدد تلاش نمایید.");
        }

        $other_reserve_material_list = session("other_reserve_material_list" . $modification_temp->id);
        if (!$other_reserve_material_list) {
            $other_reserve_material_list = [$product->id];
        } else {
            $other_reserve_material_list[] = $product->id;
        }

        session(["other_reserve_material_list" . $modification_temp->id => $other_reserve_material_list]);

        return back()->with(["success" => "یک کالا از لیست برگشت مواد اولیه حذف گردید."]);
    }

    /**
     * @param Machine $machine
     * @return \Illuminate\Http\RedirectResponse
     * در صورتی که کالایی را از لیست برگشت مواد اولیه حذف کرده بودند، با این تابع می توانند آن را به لیست برگشت بزندد.
     */
    public function reset_removed_product_from_list(Machine $machine)
    {
        $modification_temp = Modification\MachineAllocationModification::
        where([
            "machine_id" => $machine->id ?? 0,
            "warehouse_id" => $machine->warehouse_id ?? 0,
            "status_id" => 6021001
        ])->first();
        if (!$modification_temp) {
            return back()->withErrors("شناسه برگشت مواد اولیه نامعتبر است، لطفا مجدد تلاش نمایید.");
        }

        session(["other_reserve_material_list" . $modification_temp->id => []]);

        return back()->with(["success" => "کلیه کالاهای حذف شده از لیست، بازیابی شدند."]);
    }

    public function add_change_grade_api(Request $request)
    {

        $message = "";

        $machine = Machine::find($request->machine_id);
        if (!$machine) {
            $message = "شناسه ماشین نامعتبر است.";
        }

        // لیست بسته بندی هایی  در انبار موجود هستند و باید خارج شوند
        $packing_form_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $machine->warehouse->id ?? 0)->
        //whereNotIn( "product_id", $current_product_ids )->
        select("packing_forms.*")->
        get();
        $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);

        if (!$request->machine_allocation_modification_id) {
            $modification_temp = Modification\MachineAllocationModification::
            where([
                "machine_id" => $machine->id ?? 0,
                "warehouse_id" => $machine->warehouse_id ?? 0,
                "status_id" => 6021001,
                "id" => $request->machine_allocation_modification_id ?? 0
            ])->first();
        } else {
            $modification_temp = Modification\MachineAllocationModification::
            find($request->machine_allocation_modification_id);
        }
        if (!$modification_temp) {
            $message = "شناسه جدول برگشت مواد اولیه نامعتر است، لطفا مجدد تلاش کنید.";
        }

        if ($message == "" && $request->request_type == "change_grade") {
            $message = $this->add_change_grade($request, $machine, $master_packing_form_ids, $modification_temp);
        } else if ($message == "" && $request->request_type == "waste_product") {
            $message = $this->add_waste_product($request, $machine, $master_packing_form_ids, $modification_temp);
        } else if ($request->request_type == "add_packing_form_product") {
            $result = $this->add_packing_form_from_api($request, $machine, $modification_temp);
            return $result;
        }

// لیست کالاهایی که باید از انبارک خارج شوند و لازم است تا ضایعات برای آنها ثبت گردد
        $material_list = self::getMaterialList($machine, $modification_temp->warehouse);

        $goods_kind_ids = [];

        foreach ($material_list as $item) {

            $product_ids[] = $item->id;

            $goods_kind_ids[$item->id] = $item->goods_kind_id;
            $waste_list = Product\Waste\ProductWaste::where("product_id", $item->id)->get();
            foreach ($waste_list as $product_waste) {
                $product_ids[] = $product_waste->waste_id;
                $goods_kind_ids[$product_waste->waste_id] = $product_waste->waste->goods_kind_id;
            }
        }

        $has_sub_packing_type = PackingType::HasSubPackingType($product_ids);


        $product_option = Option::get("product_from_list", 0, 0, $material_list, "");

        $degree_for_products = $this->getDegreeProduct($machine);
        $route_path = $this->route_path;

        $lot_number_option = $this->getLotNumberOption($master_packing_form_ids);

        $smart_object = SmartObject::find($request->smart_object_id ?? null);

        return view($this->view_path . ($request->request_type == "change_grade" ? "_add_change_grade" : "_add_waste"), compact(
            "modification_temp", "product_option", "route_path", "has_sub_packing_type",
            "machine", "goods_kind_ids", "degree_for_products", "message", "lot_number_option", "smart_object"
        ));


    }


    public function add_change_grade(Request $request, Machine $machine, $master_packing_form_ids, $modification_temp)
    {
        $message = "";

        $product = Product::find($request->product_id);
        if (!$product) {
            $message = "شناسه کالا نامعتبر است.";
        }
        if ($product && !Unit::HasWeightUnit($product)) {
            $message = ("با توجه به اینکه هیچ کدام از واحدهای اصلی و فرعی " . $product->caption . " از نوع وزنی نیستند، امکان انجام عملیات وجود ندارد.");
        }
        $degree = Degree::
        where(["id" => $request->degree_id, "goods_kind_id" => $product->goods_kind_id ?? 0])->
        first();
        if (!$degree) {
            $message = "شناسه درجه نامعتبر است.";
        }

        // نوع بسته بندی.
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            $message = "نوع بسته بندی نامعتبر است.";
        }

        if ($packing_type->first_packing_type) {
            if (!isset($request->sub_packing_form_number) || $request->sub_packing_form_number <= 0) {
                $message = "تعداد بسته بندی های فرعی نا معتبر است.";
            }
        }


        if ($message == "") {
            $get_amount_from_weight_result = PackingType::
            getAmountFromWeight($product, $packing_type, $request->gross_weight, $request->sub_packing_form_number);

            if (!$get_amount_from_weight_result["result"]) {
                $message = $get_amount_from_weight_result["error"];
            } else {
                $gross_weight = $get_amount_from_weight_result["gross_weight"];
                $final_amount = $get_amount_from_weight_result["final_amount"];
                $weight = $get_amount_from_weight_result["weight"];

            }
        }

        // بررسی درست بودن لات
        $key = "lot_number_id_" . $product->id;
        $lot_number = LotNumber::where([
            "id" => $request->$key,
            "product_id" => $product->id
        ])->first();
        if (!$lot_number) {
            $message .= " لات وارد شده معتبر نمی باشد.";
        }

        // آیا لاتی که وارد شده است، جرء بسته بندی هایی که در حال تحویل است، می باشد یا خیر
        $lot_exist_in_packing = PackingFormItem::
        where("lot_number_id", $lot_number->id ?? "")->
        where("product_id", $product->id ?? "")->
        whereIn("packing_form_id", $master_packing_form_ids)->exists();
        if ($lot_number && !$lot_exist_in_packing) {
            $message .= "لات در انبارک ماشین وجود ندارد.";
        }

        if ($message == "") {


            Modification\MachineAllocationModificationChangeGrade::create([
                "machine_allocation_modification_id" => $modification_temp->id,
                "product_id" => $product->id,
                "gross_weight" => $gross_weight,
                "weight" => $weight,
                "amount" => $final_amount,
                "degree_id" => $degree->id,
                "packing_type_id" => $packing_type->id,
                "lot_number_id" => $lot_number->id,
                "sub_packing_form_number" => $request->sub_packing_form_number ?? 0,
            ]);

        }

        return $message;
    }

    public function add_waste_product(Request $request, Machine $machine, $master_packing_form_ids, $modification_temp)
    {
        $message = "";

        $product = Product::find($request->product_id);
        if (!$product) {
            $message = "شناسه کالا نامعتبر است.";
        }

        if ($product && !Unit::HasWeightUnit($product)) {
            $message = ("با توجه به اینکه هیچ کدام از واحدهای اصلی و فرعی " . $product->caption . " از نوع وزنی نیستند، امکان انجام عملیات وجود ندارد.");
        }
        $waste = Product::find($request->waste_id);
        if (!$waste) {
            $message = "شناسه ضایعات نامعتبر است.";
        }

        if (Product\Waste\ProductWaste::where([
                "product_id" => $product->id ?? 0,
                "waste_id" => $waste->id ?? 0
            ])->count() == 0) {
            $message = "ضایعات انتخاب شده برای مواد اولیه وجود ندارد.";
        }

        $degree = Degree::
        where(["id" => $request->degree_id, "goods_kind_id" => $waste->goods_kind_id ?? 0])->
        first();
        if (!$degree) {
            $message = "شناسه درجه نامعتبر است.";
        }

        // نوع بسته بندی.
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            $message = "نوع بسته بندی نامعتبر است.";
        }

        if ($packing_type->first_packing_type) {
            if (!isset($request->sub_packing_form_number) || $request->sub_packing_form_number <= 0) {
                $message = "تعداد بسته بندی های فرعی نا معتبر است.";
            }
        }

        if ($message == "") {
            $get_amount_from_weight_result = PackingType::
            getAmountFromWeight($product, $packing_type, $request->gross_weight, $request->sub_packing_form_number);

            if (!$get_amount_from_weight_result["result"]) {
                $message = $get_amount_from_weight_result["error"];
            } else {
                $gross_weight = $get_amount_from_weight_result["gross_weight"];
                $final_amount = $get_amount_from_weight_result["final_amount"];
                $weight = $get_amount_from_weight_result["weight"];

            }
        }

        // بررسی درست بودن لات
        $key = "lot_number_id_" . $product->id;
        $lot_number = LotNumber::where([
            "id" => $request->$key,
            "product_id" => $product->id
        ])->first();
        if (!$lot_number) {
            $message .= " لات وارد شده معتبر نمی باشد.";
        }

        // آیا لاتی که وارد شده است، جرء بسته بندی هایی که در حال تحویل است، می باشد یا خیر
        $lot_exist_in_packing = PackingFormItem::
        where("lot_number_id", $lot_number->id ?? "")->
        where("product_id", $product->id ?? "")->
        whereIn("packing_form_id", $master_packing_form_ids)->exists();
        if ($lot_number && !$lot_exist_in_packing) {
            $message .= "لات در انبارک ماشین وجود ندارد.";
        }

        if ($message == "") {


            Modification\MachineAllocationModificationChangeGrade::create([
                "machine_allocation_modification_id" => $modification_temp->id,
                "product_id" => $product->id,
                "gross_weight" => $gross_weight,
                "weight" => $weight,
                "amount" => $final_amount,
                "degree_id" => $degree->id,
                "packing_type_id" => $packing_type->id,
                "lot_number_id" => $lot_number->id,
                "sub_packing_form_number" => $request->sub_packing_form_number ?? 0,
                "waste_id" => $waste->id
            ]);

        }

        return $message;
    }

    public function add_packing_form(Modification\MachineAllocationModification $modification_temp, Machine $machine, Product $product, $type)
    {

        $info = self::GetPublicInfo($machine, $modification_temp->warehouse, $product->id, "list_and_ids");
        $material_list = $info["material_list"];

        if (count($material_list) == 0) {
            return back()->withErrors("هیچ بسته بندی جهت خروج از انبار برای " . $product->fullCaption() . " در " . $modification_temp->warehouse->caption . " وجود ندارد.");
        }

        /************************************/
        // گرفتن باسکول
        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/

        $master_packing_form_ids = $info["master_packing_form_ids"];


        // لیست بسته بندی هایی که تاکنون ثبت شده
        $modification_temp_packing_forms = $modification_temp->packing_forms()->
        where([
            "product_id" => $product->id
        ])->
        where("consumed_status_id", "!=", 6021103)->
        paginate();


        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        $route_log_path = $this->route_log_path;
        $warehouse = $machine->warehouse;

        $modification_consumed_status_option = Option::get("modification_consumed_status");
        return view($this->view_path . "add_packing_form", compact(
            "modification_temp",
            "machine", "material_list", "modification_consumed_status_option", "product",
            "route_path", "dashboard_route", "master_packing_form_ids",
            "warehouse", "route_log_path", "modification_temp_packing_forms", "url_scale", "smart_object"
        ));

    }

    public function add_packing_form_from_api(Request $request, Machine $machine, Modification\MachineAllocationModification $modification_temp)
    {
        // api ثبت بسته بندی های مصرف شده و کاملا سالم
        $product = Product::find($request->product_id);

        $info = self::GetPublicInfo($machine, $modification_temp->warehouse, $product->id, "list_and_ids");
        if (!$product) {
            return "شناسه کالا نامعتبر می باشد";
        }
        $material_list = $info["material_list"];

        if (count($material_list) == 0) {
            return "هیچ بسته بندی جهت خروج از انبار برای " . $product->fullCaption() . " در " . $machine->warehouse->caption . " وجود ندارد.";
        }

        // حذف بسته بندی هایی که کاملا مصرف شده، بعد در پایان کار این بسته بندی ها را اضافه می کنیم.
        $modification_temp->packing_forms()->
        where([
            "consumed_status_id" => 6021103,
            "product_id" => $product->id
        ])->
        delete();

        /************************************/
        // گرفتن باسکول
        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine]);
        $smart_object = SmartObject::find($request->smart_object_id);
        /**********************************/

        $master_packing_form_list = $info["master_packing_form_list"];
        $master_packing_form_ids = $info["master_packing_form_ids"];

        $packing_form = PackingForm::where("code", "DCPK/" . $request->packing_form_code)->first();
        if (!$packing_form) {
            return "کد بسته بندی " . "DCPK/" . $request->packing_form_code . " نامعتبر است.";
        }
        if (!isset($master_packing_form_list[$packing_form->id])) {
            return "بسته بندی " . $request->packing_form_code . " جزء بسته بندی های مجاز نمی باشد.";
        }

        if ($packing_form->gross_weight > 0 &&
            ( // اگر مقدار یک بسته بندی از مقدار قبلی آن 50 درصد بیشتر بود به عنوان بسته بندی خارج از عرف شناخته می شود.
                $packing_form->gross_weight * 1.5 < $request->gross_weight
            )
        ) {
            return "مقدار بسته بندی " . $request->packing_form_code . " خارج از عرف می باشد، لطفا مقدار بسته بندی را ویرایش کنید و در صورت اطمینان از مقدار با پشتیبانی تماس بگیرید.";
        }

        $message = "";
        $result_consume_message = self::GetConsumptionMessage(
            $packing_form,
            $modification_temp,
            $request->consumed_status_id,
            $product->id,
            $request->gross_weight,
            $request->sub_packing_form_number,
        );
        if (!$result_consume_message["result"]) {
            $message .= $result_consume_message["message"];
        }

        // لیست بسته بندی هایی که تاکنون ثبت شده
        $modification_temp_packing_forms = $modification_temp->packing_forms()->
        where([
            "product_id" => $product->id
        ])->paginate(99999999);

        $route_path = $request->route_path;
        $dashboard_route = $this->dashboard_route;
        $route_log_path = $this->route_log_path;
        $warehouse = $machine->warehouse;


        $modification_consumed_status_option = Option::get("modification_consumed_status");
        return view($this->view_path . "_add_packing_forms", compact(
            "master_packing_form_list", "modification_temp",
            "machine", "material_list", "modification_consumed_status_option", "product",
            "route_path", "dashboard_route", "master_packing_form_ids",
            "warehouse", "route_log_path", "modification_temp_packing_forms", "url_scale", "smart_object", "message"
        ));


    }

    public function set_remainder_consumed(Modification\MachineAllocationModification $modification_temp, Machine $machine, Product $product)
    {
        // باقی بسته بندی ها را به عنوان مصرف شده ثبت می کند.

        $info = self::GetPublicInfo($machine, $modification_temp->warehouse, $product->id, "list");
        $master_packing_form_list = $info["master_packing_form_list"];
        foreach ($master_packing_form_list as $packing_form) {
            $result_consume_message = self::GetConsumptionMessage(
                $packing_form,
                $modification_temp,
                6021103,
                $product->id,
                0,
                0,
            );
        }

        return redirect()->route($this->route_path . "index", $machine)->with(["تمامی بسته بندی های باقی مانده از  " . $product->caption . "  در انبارک به عنوان مصرف شده ثبت گردید."]);

    }

    public function show_packing_form_by_product(Modification\MachineAllocationModification $modification_temp, Machine $machine, Product $product, $consumed_status_id)
    {
        // باقی بسته بندی ها را به عنوان مصرف شده ثبت می کند.
        $master_packing_form_list = null;
        $modification_packing_forms = null;
        if ($consumed_status_id == 0) {
            $info = self::GetPublicInfo($machine, $modification_temp->warehouse, $product->id, "list", $consumed_status_id);
            $master_packing_form_list = $info["master_packing_form_list"];
        } else {
            $modification_packing_forms = Modification\MachineAllocationModificationPackingForm::
            where([
                "machine_allocation_modification_id" => $modification_temp->id,
                "product_id" => $product->id,
                "consumed_status_id" => $consumed_status_id
            ])->get();
        }
        $route_path = $this->route_path;
        $status = Status::find($consumed_status_id);
        return view($this->view_path . "show_packing_form_by_product", compact(
            "master_packing_form_list", "modification_temp", "modification_packing_forms",
            "machine", "product", "status",
            "route_path",
        ));

    }

    public function delete_one_of_packing_form(Machine $machine, PackingForm $packing_form, $modification_packing_form_id)
    {
        $modification_packing_form = Modification\MachineAllocationModificationPackingForm::find($modification_packing_form_id);
        if (!$modification_packing_form) {
            return back()->withErrors("اطلاعات بسته بندی نامعتبر است.");
        }
        if ($modification_packing_form->machine_allocation_modification->machine_id != $machine->id) {
            return back()->withErrors("اطلاعات ماشین جهت حذف بسته بندی نامعتبر است.");
        }
        if ($modification_packing_form->packing_form_id != $packing_form->id) {
            return back()->withErrors("اطلاعات بسته بندی نامعتبر است.");
        }
        if ($modification_packing_form->machine_allocation_modification->status_id != 6021001) {
            return back()->withErrors("امکان حذف بسته بندی وجود ندارد.");
        }

        $modification_packing_form->delete();

        return back()->with(["success" => "بسته بندی " . $packing_form->code . " با موفقیت از لیست برگشت مواد اولیه حذف گردید."]);
    }

    public static function GetPublicInfo(Machine $machine, Warehouse $warehouse, $product_id, $master_packing_form_type, $consumed_status_id = 0)
    {

        $material_list = self::getMaterialList($machine, $warehouse, $product_id);
        // لیست بسته بندی هایی که کالای آنها در کارت رزرو و جاری نیست و در انبارک ماشین موجود هستند و باید خارج شوند
        $packing_form_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "product_id")->
        where("warehouse_id", $warehouse->id ?? 0)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("products.id", $product_id);
        })->
        when($consumed_status_id > 0, function ($query) use ($consumed_status_id) {
            return $query->where("consumed_status_id", $consumed_status_id);
        })->
        select("packing_forms.*")->
        get();


        $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);

        if ($master_packing_form_type == "list" || $master_packing_form_type == "list_and_ids") {
            $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get()->keyBy("id");
        }
        if ($master_packing_form_type == "ids" || $master_packing_form_type == "list_and_ids") {
            $master_packing_form_ids = PackingForm::whereIn("id", $master_packing_form_ids)->pluck("id", "id")->toArray();

        }

        return [
            "material_list" => $material_list,
            "master_packing_form_list" => $master_packing_form_list ?? null,
            "master_packing_form_ids" => $master_packing_form_ids ?? null
        ];
    }

    public function submit_remaining_packing_form_delete(Request $request, Machine $machine, Warehouse $warehouse)
    {
        1 / 0;
        // گرفتن درخواست معلق
        $modification_temp = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id, "warehouse_id" => $warehouse->id, "status_id" => 6021001])->
        first();

        if (!$modification_temp) {
            return back()->withErrors("درخواست اصلاح تراکنش یافت نشد، لطفا دوباره تلاش کنید.");
        }

        $modification_temp->machine_allocation_modification_type_id = $request->machine_allocation_modification_type_id;
        // اگر انبار گردانی است نیاز نیست موجودی را انبارک را چک کند.
        $modification_temp->check_inventory_for_calculate_actual_consumption = $request->machine_allocation_modification_type_id == 1 ? 1 : 0;
        $modification_temp->save();

        session([
            "consumed" => $request->consumed,
            "gross_weight" => $request->gross_weight,
            "sub_packing_form_number" => $request->sub_packing_form_number,
            "change_of_grade" => $request->change_of_grade,

        ]);

        if ($request->change_of_grade == -1) {
            $modification_temp->change_grades()->delete();
        }
        if ($request->change_of_grade == 1 && $modification_temp->change_grades()->count() == 0) {
            return back()->withErrors("با توجه به اینکه مواد اولیه تغییر درجه داشته اند، لطفا حداقل یک بسته بندی تغییر درجه داده شده را ثبت نمایید. ");
        }
        if ($request->waste == -1) {
            $modification_temp->change_wastes()->delete();
        }
        if ($request->waste == 1 && $modification_temp->change_wastes()->count() == 0) {
            return back()->withErrors("با توجه به اینکه مواد اولیه تغییر درجه داشته اند، لطفا حداقل یک بسته بندی ضایعات شده را ثبت نمایید. ");
        }

        if ($request->gross_weight) {
            $packing_form_ids = array_keys($request->gross_weight);
        } else {
            $packing_form_ids = [];
        }
        $modification_temp->packing_forms()->delete();

        $message = "";
        foreach ($packing_form_ids as $packing_form_id) {
            $packing_form = PackingForm::find($packing_form_id);
            if (!$packing_form) {
                return back()->withErrors("بسته بندی " . $packing_form_id . " یافت نشد.");
            }
            if (!isset($request->consumed[$packing_form_id])) {
                return back()->withErrors("لطفا اطلاعات وضعیت بسته بندی ها را به صورت کامل تکمیل نمایید.");
            }

            if ($request->consumed[$packing_form_id] == 6021102 && // مصرف شده
                (!isset($request->gross_weight[$packing_form_id]) || !isset($request->sub_packing_form_number[$packing_form_id]))
            ) {
                return back()->withErrors(" در صورتی که وضعیت یک بسته بندی مصرف شده باشد، باید مقدار وزن و بسته بندی های فرعی آن را تکمیل نمایید.");
            }

            if ($packing_form->items()->count() != 1) {
                return back()->withErrors("با توجه به اینکه تعداد آیتم های بسته بندی " . $packing_form->id . " بیش از یک مورد است، امکان برگشت بسته بندی وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
            }
            $product_id = $packing_form->items()->first()->product_id;

//return $request->consumed;
            $result_consume_message = self::GetConsumptionMessage(
                $packing_form,
                $modification_temp,
                $request->consumed[$packing_form_id],
                $product_id,
                $request->gross_weight[$packing_form_id],
                $request->sub_packing_form_number[$packing_form_id]
            );
            if (!$result_consume_message["result"]) {
                $message .= $result_consume_message["message"];
            }


        }

        if ($message != "") {
            return back()->withErrors($message);
        }

        return redirect()->route($this->route_path . "confirm", [$machine, $warehouse]);
    }

    public static function GetConsumptionMessage(PackingForm $packing_form, Modification\MachineAllocationModification $modification_temp, $consumed_status_id, $product_id, $gross_weight, $sub_packing_form_number)
    {
        $message = "";
        $before_modification_list = Modification\MachineAllocationModificationPackingForm::where([
            "machine_allocation_modification_id" => $modification_temp->id,
            "packing_form_id" => $packing_form->id,
        ])->get();
        if (count($before_modification_list) == 1) {
            return [
                "result" => false,
                "message" => "این بسته بندی قبلا ثبت شده است."
            ];
        } elseif (count($before_modification_list) > 1) {
            Modification\MachineAllocationModificationPackingForm::where([
                "machine_allocation_modification_id" => $modification_temp->id,
                "packing_form_id" => $packing_form->id,
            ])->delete();
        }
        switch ($consumed_status_id) {
            case 6021101:// مصرف نشده
                // بررسی اینکه تراکنش مصرف برای بسته بندی ثبت شده است یا خیر
                // اگر تزریق مواد انجام شده باشد، پس تراکنش مصرف هم برای آن ثب شده است.
                $current_machine_log = CurrentMachineInputLog::
                where("packing_form_id", $packing_form->id)->
                orWhere("entry_packing_form_id", $packing_form->id)->first();
                if ($current_machine_log) {
                    $message .= ("با توجه به اطلاعات سامانه، وضعیت بسته بندی " . $packing_form->code . " مصرف شده یا کاملا مصرف شده است، لطفا اطلاعات را به دقت تکمیل نمایید.") . "<br/>";
                    return [
                        "result" => false,
                        "message" => $message
                    ];
                } else {
                    Modification\MachineAllocationModificationPackingForm::create([
                        "machine_allocation_modification_id" => $modification_temp->id,
                        "gross_weight" => $packing_form->gross_weight,
                        "weight" => $packing_form->weight,
                        "amount" => $packing_form->getFinalAmount(),
                        "sub_packing_form_number" => $packing_form->sub_packing_form_number,
                        "packing_form_id" => $packing_form->id,
                        "consumed_status_id" => $consumed_status_id,
                        "product_id" => $product_id,
                        "before_gross_weight" => $packing_form->gross_weight,
                        "before_weight" => $packing_form->weight,
                        "before_amount" => $packing_form->getFinalAmount(),
                        "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                    ]);
                }
                break;
            case 6021102: // مصرف شده

                $packing_form_item = PackingFormItem::where("packing_form_id", $packing_form->id)->first();

                if ($packing_form->sub_packing_form_number * 50 < $sub_packing_form_number) {
                    $message .= "تعداد بسته بندی فرعی " . $packing_form->code . " نامعبتر است." . "<br/>" . "این بسته بندی حداکثر شامل " . $packing_form->sub_packing_form_number . " بسته بندی می باشد." . $sub_packing_form_number;
                    return [
                        "result" => false,
                        "message" => $message
                    ];
                }
                if ($sub_packing_form_number <= 0) {
                    return [
                        "result" => false,
                        "message" => "تعداد بسته بندی فرعی نمی تواند کوچکتر مساوی صفر باشد."
                    ];
                }
                $get_amount_from_weight_result = PackingType::
                getAmountFromWeight(
                    $packing_form_item->product,
                    $packing_form->packing_type,
                    $gross_weight,
                    $sub_packing_form_number
                );

                if (!$get_amount_from_weight_result["result"]) {
                    $message .= "در بسته بندی" . $packing_form->code . ": " . $get_amount_from_weight_result["error"] . "<br/>";
                    return [
                        "result" => false,
                        "message" => $message
                    ];
                } else {

                    if($get_amount_from_weight_result["weight"] <=0){
                        return [
                            "result"=>false,
                            "message"=>"مقدار وزن خالص محاسبه شده برای بسته بندی نامعتبر است، لطفا اطلاعات ورودی را چک کنید. "
                        ];
                    }
                    if($get_amount_from_weight_result["final_amount"] <=0){
                        return [
                            "result"=>false,
                            "message"=>"مقدار نهایی محاسبه شده برای بسته بندی نامعتبر است، لطفا اطلاعات ورودی را چک کنید. "
                        ];
                    }
                    Modification\MachineAllocationModificationPackingForm::create([
                        "machine_allocation_modification_id" => $modification_temp->id,
                        "gross_weight" => $get_amount_from_weight_result["gross_weight"],
                        "weight" => $get_amount_from_weight_result["weight"],
                        "amount" => $get_amount_from_weight_result["final_amount"],
                        "sub_packing_form_number" => $sub_packing_form_number,
                        "packing_form_id" => $packing_form->id,
                        "consumed_status_id" => $consumed_status_id,
                        "product_id" => $product_id,
                        "before_gross_weight" => $packing_form->gross_weight,
                        "before_weight" => $packing_form->weight,
                        "before_amount" => $packing_form->getFinalAmount(),
                        "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                    ]);
                }
                break;
            case 6021103: // کاملا مصرف شده
                Modification\MachineAllocationModificationPackingForm::create([
                    "machine_allocation_modification_id" => $modification_temp->id,
                    "gross_weight" => 0,
                    "sub_packing_form_number" => 0,
                    "weight" => 0,
                    "amount" => 0,
                    "packing_form_id" => $packing_form->id,
                    "consumed_status_id" => $consumed_status_id,
                    "product_id" => $product_id,
                    "before_gross_weight" => $packing_form->gross_weight,
                    "before_weight" => $packing_form->weight,
                    "before_amount" => $packing_form->getFinalAmount(),
                    "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                ]);
                break;

        }

        return [
            "result" => true,
            "message" => $message
        ];
    }

    public function confirm(Machine $machine, Warehouse $warehouse)
    {


        // گرفتن درخواست معلق
        $modification_temp = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id, "warehouse_id" => $warehouse->id, "status_id" => 6021001])->
        first();

        if (!$modification_temp) {
            return back()->withErrors("درخواست اصلاح تراکنش یافت نشد، لطفا دوباره تلاش کنید.");
        }
        if ($modification_temp->change_grades()->count() == 0 &&
            $modification_temp->change_wastes()->count() == 0 &&
            $modification_temp->packing_forms()->count() == 0
        ) {
            return back()->withErrors("هیچ آیتمی جهت برگشت به انبار وجود ندارد.");
        }

        // چک کردن اینکه بسته بندی های ثبت شده، و کل بسته بندی ها برابر باشد
        $master_packing_form_ids = session("master_packing_form_ids");
        if (!$master_packing_form_ids) {
            return back()->withErrors("لیست بسته بندی ها مشخص نشده است، لطفا یکبار دیگر تلاش کنید.");
        }


        $packing_form_ids = $modification_temp->packing_forms()->pluck("packing_form_id")->toArray();

        if (count($packing_form_ids) != count($master_packing_form_ids)) {
            return back()->withErrors(
                "لطفا قبل از ثبت نهایی، وضعیت همه بسته بندی های داخل انبارک ماشین را مشخص نمایید." . "<br/>" .
                "تعداد بسته بندی های که باید برگشت داده شود:" . count($master_packing_form_ids) . " عدد" . "<br/>" .
                "تعداد  بسته بندی هایی که وضعیت آنها مشخص شده:" . count($packing_form_ids) . " عدد"
            );
        }

        $current_machine_input_list = CurrentMachineInput::join("allocations", "allocation_id", "allocations.id")->
        where("allocations.status_id", 5310010)-> // تخصیص جاری
        whereIn("packing_form_id", $master_packing_form_ids)->
        select("current_machine_inputs.*")->
        get();
        if (count($current_machine_input_list) != 0) {
            $message = "";
            foreach ($current_machine_input_list as $current_machine_input) {
                $modification_packing_form = $modification_temp->packing_forms()->where("packing_form_id", $current_machine_input->packing_form_id)->first();
                if ($modification_packing_form->consumed_status_id == 6021103) {
                    $message .= "<br/>" . $current_machine_input->packing_form->code;
                }
            }

            if ($message != "") {
                $message =
                    "با توجه به اینکه بسته بندی های زیر در ورودی های جاری ماشین وجود دارند، نمی توان به صورت کاملا مصرف شده ثبت گردد." .
                    "<br/> لطفا یک تزریق مواد اولیه برای ماشین انجام دهید." .
                    $message;
                return back()->withErrors($message);
            }
        }

        $packing_forms = PackingForm::whereIn("id", $packing_form_ids)->get();

        $lowest_packing_form_ids = PackingForm::LowestLevelOfPackingFormIds($packing_forms);

        $packing_form_item_ids = PackingFormItem::whereIn("packing_form_id", $lowest_packing_form_ids)->
        pluck("id")->toArray();

        // چون ممکن است یک بسته بندی چند بار وارد انبار شود، آخرین ورود را در نظر می گریم.
        $input_in_warehouse = WarehouseProduct::
        whereIn("packing_form_item_id", $packing_form_item_ids)->
        where("warehouse_id", $modification_temp->warehouse_id)->
        where("trans_kind", 5)->// ورود متفرقه
        groupBy("packing_form_item_id")->
        selectRaw("max(id) as warehouse_product_id")->
        pluck("warehouse_product_id");

        $input_in_warehouse_amount = WarehouseProduct::whereIn("id", $input_in_warehouse)->sum("input");
        if ($input_in_warehouse_amount < $modification_temp->packing_forms()->sum("amount")) {
            return back()->withErrors("مقدار کل بسته بندی ها نامعتبر است. <br/>مقدار کل بسته بندی ها از مقدار کل وارد شده به انبارک " .
                // ")".$input_in_warehouse_amount." "."(".
                " نمی تواند بیشتر باشد. ");
        }

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        $degree_for_products = $this->getDegreeProduct($machine);

        $modification_temp_packing_forms = $modification_temp->packing_forms()->paginate();

        return view($this->view_path . "confirm", compact("modification_temp", "machine", "route_path", "dashboard_route", "degree_for_products", "warehouse", "modification_temp_packing_forms"));

    }

    public function submit_confirm(Request $request, Machine $machine, Warehouse $warehouse)
    {

        $packing_form_for_print = [];

        // گرفتن درخواست معلق
        $modification_temp = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id, "warehouse_id" => $warehouse->id, "status_id" => 6021001])->
        first();

        if (!$modification_temp) {
            return back()->withErrors("درخواست اصلاح تراکنش یافت نشد، لطفا دوباره تلاش کنید.");
        }

        if ($machine->getCurrentAllocation()) {
            $modification_temp->allocation_id = $machine->getCurrentAllocation()->id;
        } else {
            $allocation_terminate = Allocation::where("machine_id", $machine->id)->where("status_id", 5310020)->orderByDesc("id")->first();
            if (!$allocation_terminate) {
                return back()->withErrors("هیچ تخصیص خاتمه یافته ای برای ماشین یافت نشد.");
            }

            $modification_temp->allocation_id = $allocation_terminate->id;
        }

        $modification_temp->created_at = Carbon::now();

        $modification_temp->save();

        foreach ($modification_temp->packing_forms as $item) {
            if ($item->consumed_status_id == 6021102) {
                $packing_form_for_print[] = $item->packing_form;
            }
        }
        // تغییر بسته بندی
        foreach ($modification_temp->change_grades as $change_grade) {

            $packing_form = PackingForm::create([
                "packing_type_id" => $change_grade->packing_type_id,
                "status_id" => 7007002, // در انتظار تایید انبار
                "weight" => $change_grade->weight,
                "gross_weight" => $change_grade->gross_weight,
                "sub_packing_form_number" => $change_grade->sub_packing_form_number
            ]);

            PackingFormItem::create([
                "packing_form_id" => $packing_form->id,
                "product_id" => $change_grade->product_id,
                "lot_number_id" => $change_grade->lot_number_id,
                "degree_id" => $change_grade->degree_id,
                "amount" => $change_grade->amount,
                "amount_after_control" => $change_grade->amount,
                "final_amount" => $change_grade->amount,
                "sub_amount" => 0,
                "init_sub_amount" => 0,
                "status_id" => 7007002, // در انتظار تایید انبار
                "band_code" => 1,
            ]);


            $packing_form_for_print[] = $packing_form;
            $change_grade->packing_form_id = $packing_form->id;
            $change_grade->save();

        }

        // مواد اولیه ضایعات شده
        foreach ($modification_temp->change_wastes as $change_waste) {

            $lot_number = LotNumber::where("product_id", $change_waste->waste_id)->
            where("code", $change_waste->lot_number->code)->first();

            if (!$lot_number) {
                $lot_number = LotNumber::create([
                    "product_id" => $change_waste->waste_id,
                    "code" => $change_waste->lot_number->code,
                    "user_id" => Auth::id()
                ]);
            }

            $packing_form = PackingForm::create([
                "packing_type_id" => $change_waste->packing_type_id,
                "status_id" => 7007002, // در انتظار تایید انبار
                "weight" => $change_waste->weight,
                "gross_weight" => $change_waste->gross_weight,
                "sub_packing_form_number" => $change_waste->sub_packing_form_number
            ]);

            PackingFormItem::create([
                "packing_form_id" => $packing_form->id,
                "product_id" => $change_waste->waste_id,
                "lot_number_id" => $lot_number->id,
                "degree_id" => $change_waste->degree_id,
                "amount" => $change_waste->amount,
                "amount_after_control" => $change_waste->amount,
                "final_amount" => $change_waste->amount,
                "sub_amount" => 0,
                "init_sub_amount" => 0,
                "status_id" => 7007002, // در انتظار تایید انبار
                "band_code" => 1,
            ]);


            $packing_form_for_print[] = $packing_form;
            $change_waste->packing_form_id = $packing_form->id;
            $change_waste->save();

        }

        $modification_temp->status_id = 6021002; //در انتظار ثبت برگ خروج و فرم ورود
        $modification_temp->user_id = Auth::id();
        $modification_temp->save();

        // لاگ ماشین
        $machine_log = new MachineLog();
        $machine_log->machine_event_type_id = 707;// برگشت مواد اولیه
        event(new MachineLogEvent($machine, $machine_log));


        if (count($packing_form_for_print) > 0 && $modification_temp->machine_allocation_modification_type_id == 1) {
            session([
                "packing_form_ids_print" => $packing_form_for_print,
                "machine_allocation_modification" => $modification_temp
            ]);

            return redirect()->route($this->route_path . "print_new_packing", $machine)->with("عملیات با موفقیت انجام شد.");
        }

        return redirect()->route($this->dashboard_route . "view", $machine)->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public function print_new_packing(Machine $machine, $page = 1)
    {
        $packing_form_list = session("packing_form_ids_print");
        if ($packing_form_list) {
            $route_path = $this->route_path;
            $dashboard_route = $this->dashboard_route;
            $machine_allocation_modification = session("machine_allocation_modification");

            $unconfirmed_data_list = [];

            foreach ($packing_form_list as $packing_form_print) {
                $modification_packing_form = Modification\MachineAllocationModificationPackingForm::where([
                    "packing_form_id" => $packing_form_print->id,
                    "machine_allocation_modification_id" => $machine_allocation_modification->id
                ])->first();
                if ($modification_packing_form) {

                    $unconfirmed_data["weight"] = $modification_packing_form->weight;
                    $unconfirmed_data["gross_weight"] = $modification_packing_form->gross_weight;
                    $unconfirmed_data["final_amount"] = $modification_packing_form->amount;
                    $unconfirmed_data["sub_packing_form_number"] = $modification_packing_form->sub_packing_form_number;

                    $unconfirmed_data_list[$packing_form_print->id] = $unconfirmed_data;
                }
            }

            return view($this->view_path . "print_new_packing", compact("machine", "packing_form_list", "route_path", "dashboard_route", "machine_allocation_modification", "unconfirmed_data_list"));
        } else {
            return redirect()->route($this->dashboard_route . "view", $machine)->withErrors("بسته بندی جهت پرینت وجود ندارد");
        }
    }

    public function submit_print_new_packing(Request $request, Machine $machine, Modification\MachineAllocationModification $machine_allocation_modification)
    {
        $data = $request->data;
        $worker = Worker::find(Auth::id());
        if ($data && count($data) > 0) {

            $packing_forms = PackingForm::whereIn("id", array_keys($data["packing_form"]))->get();
            foreach ($packing_forms as $packing_form_print) {
                $modification_packing_form = Modification\MachineAllocationModificationPackingForm::where([
                    "packing_form_id" => $packing_form_print->id,
                    "machine_allocation_modification_id" => $machine_allocation_modification->id
                ])->first();
                if ($modification_packing_form) {


                    $unconfirmed_data["weight"] = $modification_packing_form->weight;
                    $unconfirmed_data["gross_weight"] = $modification_packing_form->gross_weight;
                    $unconfirmed_data["final_amount"] = $modification_packing_form->amount;
                    $unconfirmed_data["sub_packing_form_number"] = $modification_packing_form->sub_packing_form_number;

                    $packing_form_print->unconfirmed_data = $unconfirmed_data;
                }
                PrintQRController::direct_print($packing_form_print, $worker);
            }
        }
        session([
            "packing_form_ids_print" => null
        ]);

        return redirect()->route($this->dashboard_route . "view", $machine)->with(["success" => "بسته بندی های مورد نظر با موفقیت پرینت شدند"]);

    }

    public static function getCurrentAndReservedProductId(Machine $machine, $other_products = [])
    {
        $current_product_ids = CurrentMachineInput::
        join("allocations", "allocation_id", "allocations.id")->
        where("allocations.machine_id", $machine->id)->
        whereIn("allocations.status_id", [5310010, 5310040])->
        groupBy("material_id")->
        pluck("material_id")->
        toArray();

        $current_product_ids[] = -1;
        foreach ($other_products as $pid) {
            $current_product_ids[] = $pid;
        }
        return $current_product_ids;
    }

    public function getDegreeProduct(Machine $machine)
    {
        // لیست درجه ها کالاهای داخل بسته بندی که قرار است از انبارک خارج شوند در این تابع لیست می شوند.

        // به دست آوردن لیست کالاهایی که در کالای جاری و رزرو مصرف می شود
        // $current_product_ids = $this::getCurrentAndReservedProductId( $machine );

        // لیست بسته بندی هایی که کالای آنها در کارت رزرو و جاری نیست و در انبار موجود هستند و باید خارج شوند
        $packing_form_item_list = PackingFormItem::
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $machine->warehouse->id ?? 0)->
        // whereNotIn( "product_id", $current_product_ids )->
        select("packing_form_item.*")->
        get();

        $list = [];
        foreach ($packing_form_item_list as $item) {
            if (!isset($list[$item->product_id][$item->degree_id])) {
                $list[$item->product_id][$item->degree_id] = $item->degree->caption;
            }
        }

        return $list;
    }

    public static function getMaterialList($machine, $warehouse, $product_id = null)
    {

        // ارسال درخواست برای کدام رسته های کالایی فعال است.
        $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereAllowReturnToWarehouse($machine);
        $goods_kind_ids[] = -1;

        // لیست کالاهایی که باید از انبارک خارج شوند و لازم است تا ضایعات برای آنها ثبت گردد
        return $material_list = Product::
        join("packing_form_item", "products.id", "product_id")->
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $warehouse->id ?? 0)->
        whereIn("goods_kind_id", $goods_kind_ids)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("products.id", $product_id);
        })->
        distinct("product_id")->
        select("products.*")->
        get()->
        keyBy("id");
    }

    public function getLotNumberOption($master_packing_form_ids)
    {
        $lot_number_list = PackingFormItem::join("lot_numbers", "lot_numbers.id", "lot_number_id")->
        whereIn("packing_form_id", $master_packing_form_ids)->
        groupBy("lot_number_id")->
        select("lot_numbers.*")->
        get();
        $lot_number_option = [];
        foreach ($lot_number_list as $lot_number) {
            $lot_number_option[$lot_number->product_id][] = [
                "text" => $lot_number->code,
                "value" => $lot_number->id
            ];
        }

        return $lot_number_option;
    }

    public static function AddNewModificationForPacking(Machine $machine, $packing_form_ids, $data, $allocation)
    {
        if (!$allocation) {
            return [
                "result" => false,
                "error" => "تخصیص مورد نظر در تابع عمومی برگشت مواد اولیه مقدار دهی نشده است"
            ];
        }
        // ایجاد یک درخواست جدید برای برگشت به انبار
        $modification_temp = Modification\MachineAllocationModification::
        create(["machine_id" => $machine->id, "warehouse_id" => $machine->warehouse_id, "status_id" => -1]);

        $message = "";
        foreach ($packing_form_ids as $packing_form_id) {
            // باید بسته بندی وجود داشته باشد، تا بتواند برگشت بزند، این خطا برای اولین بار پیش می آید.
            if ($packing_form_id == -1) {
                continue;
            }
            $packing_form = PackingForm::find($packing_form_id);


            if ($packing_form->items()->count() != 1) {
                $message = "با توجه به اینکه تعداد آیتم های بسته بندی " . $packing_form->id . " بیش از یک مورد است، امکان برگشت بسته بندی وجود ندارد، لطفا با پشتیبانی تماس بگیرید.";

                return [
                    "result" => false,
                    "error" => $message
                ];
            }
            $product_id = $packing_form->items()->first()->product_id;

            if(!isset($data[$packing_form_id]["consumed_status_id"])){
                $packing_form=PackingForm::find($packing_form_id);
                return [
                    "result" => false,
                    "error" => "وضعیت مصرف بسته بندی ".$packing_form->code." مشخص نشده است، لطفا با واحد پشتیبانی تماس بگیرید."
                ];
            }

            switch ($data[$packing_form_id]["consumed_status_id"]) {
                case 6021101:// مصرف نشده
                    // بررسی اینکه تراکنش مصرف برای بسته بندی ثبت شده است یا خیر
                    // اگر تزریق مواد انجام شده باشد، پس تراکنش مصرف هم برای آن ثب شده است.
                    $current_machine_log = CurrentMachineInputLog::
                    where("packing_form_id", $packing_form_id)->
                    orWhere("entry_packing_form_id", $packing_form_id)->first();
                    if ($current_machine_log) {
                        $message .= ("با توجه به اطلاعات سامانه، وضعیت بسته بندی " . $packing_form->code . " مصرف شده یا کاملا مصرف شده است، لطفا اطلاعات را به دقت تکمیل نمایید.") . "<br/>";

                        return [
                            "result" => false,
                            "error" => $message
                        ];
                    } else {
                        Modification\MachineAllocationModificationPackingForm::create([
                            "machine_allocation_modification_id" => $modification_temp->id,
                            "gross_weight" => $packing_form->gross_weight,
                            "weight" => $packing_form->weight,
                            "amount" => $packing_form->getFinalAmount(),
                            "sub_packing_form_number" => $packing_form->sub_packing_form_number,
                            "packing_form_id" => $packing_form_id,
                            "consumed_status_id" => $data[$packing_form_id]["consumed_status_id"],
                            "product_id" => $product_id,
                            "before_gross_weight" => $packing_form->gross_weight,
                            "before_weight" => $packing_form->weight,
                            "before_amount" => $packing_form->getFinalAmount(),
                            "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                        ]);
                    }
                    break;
                case 6021102: // مصرف شده

                    $packing_form_item = PackingFormItem::where("packing_form_id", $packing_form->id)->first();

                    if ($packing_form->sub_packing_form_number < $data[$packing_form_id]["sub_packing_form_number"]) {
                        $message .= "تعداد بسته بندی فرعی " . $packing_form->code . "نا معبتر است.";

                        return [
                            "result" => false,
                            "error" => $message
                        ];
                    }
                    $get_amount_from_weight_result = PackingType::
                    getAmountFromWeight(
                        $packing_form_item->product,
                        $packing_form->packing_type,
                        $data[$packing_form_id]["gross_weight"],
                        $data[$packing_form_id]["sub_packing_form_number"],
                        isset($data[$packing_form_id]["carrier"]) ? $data[$packing_form_id]["carrier"] : null,
                        isset($data[$packing_form_id]["amount"]) ? $data[$packing_form_id]["amount"] : null,
                    );

                    if (!$get_amount_from_weight_result["result"]) {
                        $message .= "در بسته بندی" . $packing_form->code . ": " . $get_amount_from_weight_result["error"] . "<br/>";

                        return [
                            "result" => false,
                            "error" => $message
                        ];
                    } else {
                        Modification\MachineAllocationModificationPackingForm::create([
                            "machine_allocation_modification_id" => $modification_temp->id,
                            "gross_weight" => $get_amount_from_weight_result["gross_weight"],
                            "weight" => $get_amount_from_weight_result["weight"],
                            "amount" => $get_amount_from_weight_result["final_amount"],
                            "sub_packing_form_number" => $data[$packing_form_id]["sub_packing_form_number"],
                            "packing_form_id" => $packing_form_id,
                            "consumed_status_id" => $data[$packing_form_id]["consumed_status_id"],
                            "product_id" => $product_id,
                            "before_gross_weight" => $packing_form->gross_weight,
                            "before_weight" => $packing_form->weight,
                            "before_amount" => $packing_form->getFinalAmount(),
                            "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                        ]);
                    }
                    break;
                case 6021103: // کاملا مصرف شده
                    Modification\MachineAllocationModificationPackingForm::create([
                        "machine_allocation_modification_id" => $modification_temp->id,
                        "gross_weight" => 0,
                        "sub_packing_form_number" => 0,
                        "weight" => 0,
                        "amount" => 0,
                        "packing_form_id" => $packing_form_id,
                        "consumed_status_id" => $data[$packing_form_id]["consumed_status_id"],
                        "product_id" => $product_id,
                        "before_gross_weight" => $packing_form->gross_weight,
                        "before_weight" => $packing_form->weight,
                        "before_amount" => $packing_form->getFinalAmount(),
                        "before_sub_packing_form_number" => $packing_form->sub_packing_form_number,
                    ]);
                    break;

            }


        }

        if ($message != "") {
            return [
                "result" => false,
                "error" => $message
            ];
        }

        //  $modification_temp->check_inventory_for_calculate_actual_consumption = 0; // موجودی بسته بندی را چک نمی کند.
        $modification_temp->allocation_id = $allocation->id;

        $modification_temp->status_id = 6021002; //در انتظار ثبت برگ خروج و فرم ورود
        $modification_temp->user_id = Auth::id();
        $modification_temp->save();

        // لاگ ماشین
        $machine_log = new MachineLog();
        $machine_log->machine_event_type_id = 707;// برگشت مواد اولیه
        event(new MachineLogEvent($machine, $machine_log));

        return [
            "result" => true
        ];
    }

    /******** انبار گردانی انبارک ها **********/
    /********************************************************************************/

    public function warehouse_handling(Machine $machine, Warehouse $warehouse)
    {

        $result_handling = session("result_handling");
        if (!$result_handling) {
            return back()->withErrors("لیست انبارگردانی یافت نشد، لطفا مجدد تلاش کنید.");
        }

        $in_action = Modification\MachineAllocationModification::
        where(["warehouse_id" => $warehouse->id])->
        whereNotIn("status_id", [6021001, 6021003])-> // خاتمه یافته
        first();
        if ($in_action) {
            return redirect()->route($this->route_log_path . "index", $machine)->withErrors("با توجه به اینکه سامانه در حال پردازش درخواست برگشت مواد اولیه شماره " .
                $in_action->id . " (" . $warehouse->caption . ")" .
                " است، امکان ثبت وجود ندارد، لطفا چند دقیقه دیگر اقدام کنید.  ");
        }
        $modification_temp = Modification\MachineAllocationModification::
        where(["machine_id" => $machine->id, "warehouse_id" => $warehouse->id, "status_id" => 6021001])->
        firstOrCreate([
            "machine_id" => $machine->id,
            "warehouse_id" => $warehouse->id,
            "status_id" => 6021001
        ]);

        $machine_type_id = null;
        if ($warehouse->warehouse_type_id == 2) { // انبارک ماشین
            $machine_type_id = $machine->machine_type_id;
        }
        $warehouse_limit = WarehouseHandlingGoodsKindTimeLimit::GetGoodsKindLimit($machine->machine_type);
        $result_handling = Warehouse::warehouse_handling_need($warehouse, null, $warehouse_limit, $machine_type_id);
        if (!$result_handling["result"]) {
            return back()->withErrors($warehouse->caption . " نیاز به انبارگردانی ندارد.");
        }

        /************************************/
        // گرفتن باسکول
        $url_scale = route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, $this->route_path . "index", $machine])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object_value = $result_smart_object["smart_object_value"];
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/

        $modification_temp->machine_allocation_modification_type_id = 2;
        // اگر انبار گردانی است نیاز نیست موجودی را انبارک را چک کند.
        $modification_temp->check_inventory_for_calculate_actual_consumption = 0;
        $modification_temp->save();

        // بسته بندی های که در انبار گردانی وجود دارد ولی نباید برگشت داده شود را از انبار گردانی حذف می کنیم.
        $modification_temp->
        packing_forms()->
        whereNotIn("product_id", $result_handling["product_ids"])->
        delete();

        // لیست بسته بندی هایی که کالای آنها در کارت رزرو و جاری نیست و در انبارک ماشین موجود هستند و باید خارج شوند
        $packing_form_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", $warehouse->id ?? 0)->
        whereIn("product_id", $result_handling["product_ids"])->
        select("packing_forms.*")->
        get();

        $master_packing_form_ids = PackingForm::MasterPackingFormIds($packing_form_list);
        session(["master_packing_form_ids" => $master_packing_form_ids]);

        $master_packing_form_list = PackingForm::whereIn("id", $master_packing_form_ids)->get();

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        $route_log_path = $this->route_log_path;
        $material_list = Product::whereIn("id", $result_handling["product_ids"])->get()->keyBy("id");

        $master_packing_form_group_by_product = PackingFormItem::
        whereIn("packing_form_id", $master_packing_form_ids)->
        whereIn("product_id", $result_handling["product_ids"])->
        groupBy("product_id")->
        selectRaw("product_id,count(distinct(packing_form_id)) as count")->
        pluck("count", "product_id")->
        toArray();

        $modification_packing_form_group_by_product[6021101] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021101)->
            whereIn("product_id", $result_handling["product_ids"])->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();
        $modification_packing_form_group_by_product[6021102] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021102)->
            whereIn("product_id", $result_handling["product_ids"])->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();

        $modification_packing_form_group_by_product[6021103] =
            Modification\MachineAllocationModificationPackingForm::
            where("machine_allocation_modification_id", $modification_temp->id)->
            where("consumed_status_id", 6021103)->
            whereIn("product_id", $result_handling["product_ids"])->
            groupBy("product_id")->
            selectRaw("product_id,count(id) as count")->
            pluck("count", "product_id")->
            toArray();

        $consumed_list = session("consumed");
        $gross_weight_list = session("gross_weight");
        $sub_packing_form_number_list = session("sub_packing_form_number");
        $warehouse_handling_time_limit = $result_handling["warehouse_handling_time_limit"];
        return view($this->view_path . "warehouse_handling", compact(
            "master_packing_form_list", "material_list",
            "machine", "route_log_path", "master_packing_form_group_by_product", "warehouse_handling_time_limit",
            "route_path", "dashboard_route", "warehouse", "modification_packing_form_group_by_product",
            "consumed_list", "gross_weight_list", "sub_packing_form_number_list", "smart_object_value", "smart_object", "url_scale",
            "modification_temp"

        ));
    }

    public function HasAnyWarehouseHandling(Machine $machine)
    {

        $warehouse_limit = WarehouseHandlingGoodsKindTimeLimit::GetGoodsKindLimit($machine->machine_type);
        $result = Warehouse::warehouse_handling_need($machine->warehouse, null, $warehouse_limit, $machine->machine_type_id);
        if ($result["result"] || isset($result["error"])) {
            return $result;
        }

        $machine_type_warehouses = Warehouse::where([
            "warehouse_type_id" => 3,// انبارک گروه ماشین
            "belonging_to_id" => $machine->machine_type_id
        ])->get();
        $station_warehouses = Warehouse::where([
            "warehouse_type_id" => 4,// انبارک ایستگاه کاری
            "belonging_to_id" => $machine->station_id
        ])->get();
        $lines_warehouses = Warehouse::where([
            "warehouse_type_id" => 5,// انبارک خط
            "belonging_to_id" => $machine->station->line_id
        ])->get();

        // انبارک گروه ماشین
        foreach ($machine_type_warehouses as $warehouse) {
            $result = Warehouse::warehouse_handling_need($warehouse, null, $warehouse_limit);
            if ($result["result"]) {
                return $result;

            }
        }
        // انبارک های ایستگاه
        foreach ($station_warehouses as $warehouse) {
            $result = Warehouse::warehouse_handling_need($warehouse, null, $warehouse_limit);
            if ($result["result"]) {
                return $result;

            }
        }
        // انبارک های خط
        foreach ($lines_warehouses as $warehouse) {
            $result = Warehouse::warehouse_handling_need($warehouse, null, $warehouse_limit);
            if ($result["result"]) {
                return $result;

            }
        }

        return ["result" => false];


    }
}
