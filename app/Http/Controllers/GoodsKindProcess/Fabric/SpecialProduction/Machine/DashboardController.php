<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction\Machine;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Post\PostStatus;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public static $perfix_production_status_code = "7303";
    public static $info = [
        "route" => "fabric.special_production.machine.dashboard."
    ];
    var $view_path = "goods_kind_process.fabric.special_production.machine.dashboard.";
    var $route_path = "production.machine.";

    public function __construct()
    {

        View::share("perfix_status_code", DashboardController::$perfix_production_status_code);
    }

    public function view(Request $request, Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        if ($request->isMethod('post')) {
            $search = $request->search;
        } else {
            $search = session("search_special_production_dashboard");
        }
        session([
            "search_special_production_dashboard" => $search,
            ]);
        $controller_info = DashboardController::get_controller_info("view");
        $special_condition = DashboardController::enable_special_condition($machine);

        $allocation = $machine->getCurrentAllocation();


        $reserve_allocation_list = MachineAllocation::
        join("products", "products.id", "machine_allocation.product_id")->
        where("machine_id", $machine->id)->
        whereIn("machine_allocation.status_id", [5310040,5310010])->
        when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
               return $query->where("products.caption", "like", "%" . $search . "%")->
                orWhere("products.code", "like", "%" . $search . "%");
            });

        })->
            select("machine_allocation.id","allocation_id","product_id","production_id","machine_allocation.created_at","allocation_amount")->
        with("product", "production")->
        paginate(30);
//        $reserve_allocation_list = [];
//        foreach ($reserve_allocation as $r_allocation) {
//
//                $reserve_allocation_list[$r_allocation->production_id] = $item;
//
//
//        }

        $productionFromItemLot = null;
//        if ( $allocation ) {
//            $productionFromItemLot = FabricRaw::getCurrentLot( $allocation );
//
//            // اگر به هر دلیل لات تولید نشده بود، دوباره لات را ایجاد می کند.
//            if ( count( $productionFromItemLot ) == 0 ) {
//                FabricRaw::ChangeLot( $allocation );
//                $productionFromItemLot = FabricRaw::getCurrentLot( $allocation );
//            }
//
//
//        }


        $production_form = $machine->getCurrentProductionForm();


        $production_form_carrier_code = $production_form->carrier->code ?? "---";
        $production_form_reserve_carrier_code = $production_form_reserve->carrier->code ?? null;

        $current_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();


        $form_list = Form::where([
            "applicant_type_id" => 10, // ماشین
            "applicant_id" => $machine->id
        ])->paginate(10);


        return view($this->view_path . "view", compact(
            "production_form_reserve_carrier_code",
            "current_input_list", "production_form_carrier_code",
            "productionFromItemLot",
            "allocation",
            "machine",
            "controller_info",
            "special_condition",
            "reserve_allocation_list",
            "form_list","search"
        ));
    }


    public function checkPermission(Machine $machine)
    {
        $result = DashboardController::checkPermissionConditions($machine);
        if (!$result["result"]) {
            $message = \Session::get('success');
            if (isset($message)) {
                return redirect()->route($this->route_path . "index")->with(["success" => $message]);
            }

            return redirect()->route($this->route_path . "index")->withErrors($result["message"]);
        }

    }

    public static function checkPermissionConditions(Machine $machine, $info = false, $all_status = false)
    {

        $allowed_status_ids = PostStatus::getAllowedStatus(3);
        if (!in_array($machine->production_status_id, $allowed_status_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }

        $allowed_machine_ids = Line::getAllowedMachine();
        if (!in_array($machine->id, $allowed_machine_ids)) {
            return [
                "result" => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }
        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset($value);
            if (!$all_status && !in_array($machine->production_status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت ماشین جهت عملیات نامعتبر است",
                    "error_type" => "for_machine_status"
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

    public static function enable_special_condition(Machine $machine)
    {

        $result = [];
        $result["01"] = false;

        return $result;


    }


    public static function get_controller_info($type = "")
    {
        $controller_info = [
            "01" => RegisterProductionController::$info,
            "97" => AllocationCardController::$info,
            "99" => LogController::$info,
        ];

        return $controller_info;
    }
}
