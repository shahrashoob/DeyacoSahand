<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw;


use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangeInWarehouseController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangePackingController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangePackingInWarehouseController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangePackingQuickController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\CompleteInformationController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\DeliveryToWarehouseController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\MergerController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\QualityControlController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\RegisterWeightController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLog;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationActualConsumption;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionFormLog;
use App\Models\QualityControl\QualityControlProductFault;
use App\Models\Utility\Option;
use App\Models\Warehouse\Pallet\PalletItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use function back;
use function session;
use function view;

class PackingFormController extends Controller
{
    public static $perfix_packing_status_code = "7007";

    var $view_path = "goods_kind_process.fabric_raw.packing_form.";
    var $route_path = "fabric_raw.packing.dashboard.";

    public function __construct()
    {

        View::share("perfix_status_code", PackingFormController::$perfix_packing_status_code);
        View::share("view_path", $this->view_path);
        View::share("route_path", $this->route_path);
    }

    public function index(Request $request)
    {
        $resultIndex = self::GetIndex($request);
        if ($resultIndex["result"]) {
            $list = $resultIndex["list"];
            $search = $resultIndex["search"];
            $status_option = $resultIndex["status_option"];
            $goods_kind_option = $resultIndex["goods_kind_option"];
            $order_by_Option = $resultIndex["order_by_Option"];
            return view($this->view_path . "index", compact("list", "search", "status_option", "goods_kind_option", "order_by_Option"));

        } else {
            return back()->withErrors($resultIndex["error"]);
        }

    }


    public static function GetIndex(Request $request)
    {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ($request->status_id != 0 && !in_array($request->status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "error" => "شما اجازه دسترسی به مشاهده فرم با وضعیت انتخاب شده را ندارید"
            ];
        }

        $allowed_goods_kind_ids = GoodsKindPost::getAllowedGoodsKindId();

        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $order_by = $request->order_by;
            $goods_kind_id = $request->goods_kind_id;
        } else {
            $search = session("search_packing2");
            $status_id = session("packing2_status_id");
            $order_by = session("order_by_packing2");
            $goods_kind_id = session("goods_kind_id_packing2");
        }
        session([
            "search_packing2" => $search,
            "packing2_status_id" => $status_id,
            "order_by_packing2" => $order_by,
            "goods_kind_id_packing2" => $goods_kind_id,
        ]);

        $goods_kind_option = Option::get("goods_kind", $goods_kind_id, 0, $allowed_goods_kind_ids);

        // search
        if ($status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $status_id;
        }

        // search
        if ($goods_kind_id != 0) {
            $allowed_goods_kind_ids = [];
            $allowed_goods_kind_ids[] = $goods_kind_id;
        }


        $list = PackingForm::
        join("packing_form_item", "packing_form_id", "packing_forms.id")->
        join("products", "product_id", "products.id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("packing_forms.code", "like", "%" . $search . "%");
            });

        })->
        whereIn("packing_forms.status_id", $allowed_status_ids)->
        whereIn("goods_kind_id", $allowed_goods_kind_ids)->
        whereNull("packing_form_master_id")-> // بسته داخل بسته بزرگتر
        orderBy("packing_forms.id", "desc")-> /////////////////
        groupBy("packing_forms.id")->
        select("packing_forms.*")->
        with("form")->
        with("packing_type")->
        with("carrier")->
        paginate(20);;
        $order_by_Option = Option::OrderBy("public", $order_by);
        $status_option = Option::get("status", $status_id, 7007);

        return [
            "result" => true,
            "list" => $list,
            "search" => $search,
            "status_option" => $status_option,
            "goods_kind_option" => $goods_kind_option,
            "order_by_Option" => $order_by_Option,

        ];
    }

    public function view(PackingForm $packing_form, $page = 1, $back_url_route = false, $back_url_route_id = false, $back_url_route_id2 = false)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $controller_info = PackingFormController::get_controller_info();
        $cancel_item_route = "dashboard";
        $special_condition = [];

        $packing_form_contents_list = $packing_form->packing_form_contents()->paginate(20);

        if (count($packing_form_contents_list) > 0) {
            $special_condition["02"] = false;
            $special_condition["05"] = false;
        }

        $removed_sub_packing = PackingFormLog::join("packing_forms", "packing_forms.id", "packing_form_logs.packing_form_master_id")->
        where("packing_form_logs.packing_form_master_id", $packing_form->id)->select("packing_form_logs.*")->
        paginate(20);

        // اطلاعات مصرف واقعی
        $machine_allocation_consumption = [];
        foreach ($packing_form->items as $packing_form_item) {
            // به ازای هر کالا تخصیص یک ردیف در نظر می گیریم.
            if (isset($packing_form_item->production_form_item->allocation_id) &&
                !isset($machine_allocation_consumption[$packing_form_item->production_form_item->allocation_id][$packing_form_item->product_id])) {

                $machine_allocation_consumption["material"]
                [$packing_form_item->production_form_item->allocation_id]
                [$packing_form_item->product_id]
                    = CurrentMachineInput::
                where("allocation_id", $packing_form_item->production_form_item->allocation_id)->
                groupBy("material_id")->
                selectRaw("current_machine_inputs.*,sum(number*amount*percent_of_use/100) as predictive_amount")->
                get()->keyBy("material_id");


                $machine_allocation_consumption["allocation"]
                [$packing_form_item->production_form_item->allocation_id]
                [$packing_form_item->product_id] =
                    MachineAllocationActualConsumption::
                    where("allocation_id", $packing_form_item->production_form_item->allocation_id)->
                    get()->keyBy("material_id");

                $machine_allocation_consumption["product"][$packing_form_item->product_id] = $packing_form_item->product;
            }
        }

        $warehouse_status_reference_code = $packing_form->GetWarehouseStatusReferenceCode();
        $warehouse_status_reference_code = "(کد مرجع:" . $warehouse_status_reference_code . ")";
        $logs = $packing_form->logs()->orderBy("id", "desc")->paginate(10);

        $packing_form_actual_costs =
            PackingFormActualCost::
            with(["actual_cost_type", "status"])->
            where("packing_form_id", $packing_form->id)->
            orderBy("actual_cost_type_id")->
            get();

        if (!Route::has($back_url_route)) {
            $back_url_route = false;
        }

        //Pallet
        $pallet_item=PalletItem::where("packing_form_id",$packing_form->id)->with("pallet")->first();

        // اطلاعات مربوط به کنترل کیفی کالا
      //  $production_form_item_ids=$packing_form->items()->pluck("production_form_item_id")->toArray();
      //  $production_form_item_ids[]=-1;
       // $quality_control_product_faults=QualityControlProductFault::whereIn("production_form_item_id",$production_form_item_ids)->get();
        $quality_control_product_faults=QualityControlProductFault::where("packing_form_id",$packing_form->id)->paginate();

        return view($this->view_path . "view", compact(
            "packing_form", "special_condition",
            "packing_form_contents_list", "controller_info", "cancel_item_route",
            "removed_sub_packing", "page", "machine_allocation_consumption",
            "warehouse_status_reference_code", "logs","quality_control_product_faults",
            "back_url_route", "back_url_route_id", "back_url_route_id2",
            "packing_form_actual_costs","pallet_item"));
    }

    public function qr(PackingForm $packing_form)
    {

        $controller_info = PackingFormController::get_controller_info();
        $cancel_item_route = "dashboard";

        $button = [];
        if ($packing_form->form && $packing_form->status_id == 7007002 && \App\Http\Controllers\Warehouse\DashboardController::check_permission($packing_form->form)) {
            $button["warehouse_button"] = 1;
        }

        return view($this->view_path . "qr_view", compact("button", "packing_form", "controller_info", "cancel_item_route"));
    }

    public function DCPK_QR(PackingForm $packing_form, $key)
    {

        if ($packing_form->random != $key) {
            // این شرط به صورت موفقت باری فرم های بسته بندی که ای دی آنها کمتر از 2805 باشد، می باشد.
            if ($packing_form->id > 2806) {
                return back()->withErrors("آدرس نامعتبر است.");
            }

        }
        $controller_info = PackingFormController::get_controller_info();
        $cancel_item_route = "dashboard";

        $button = [];
        if ($packing_form->form && $packing_form->status_id == 7007002 && \App\Http\Controllers\Warehouse\DashboardController::check_permission($packing_form->form)) {
            $button["warehouse_button"] = 1;
        }

        return view($this->view_path . "qr_view", compact("button", "packing_form", "controller_info", "cancel_item_route"));
    }

    public static function checkPermissionConditions(PackingForm $packing_form, $info = false)
    {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if (!in_array($packing_form->status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده فرم را ندارید.",
            ];
        }

        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = PackingFormController::$perfix_packing_status_code . $value;
            }
            unset($value);
            if (!in_array($packing_form->status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت فرم بسته بندی جهت عملیات نامعتبر است",
                ];
            }
            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }

    public function checkPermission(PackingForm $packing_form)
    {
        $result = PackingFormController::checkPermissionConditions($packing_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        $result_check = PackingForm::CheckChangePackingIsOK($packing_form->packing_form_parent);

        if (!$result_check["result"]) {
            return back()->withErrors($result_check["error"]);

        }

    }

    public static function get_controller_info()
    {
        return $controller_info = [
            "01" => DeliveryToWarehouseController::$info,
            "02" => ChangePackingController::$info,
            "04" => PrintQRController::$info,
            "05" => ChangeInWarehouseController::$info,
            "06" => CompleteInformationController::$info,
            "07" => MergerController::$info,
            "08" => QualityControlController::$info,
            "09" => ChangePackingQuickController::$info
        ];
    }


}
