<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class MachineAllocationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine_allocation.",
        "enable_status" => [ "001", "002", "003" ],
        "enable_route"  => [
            "fabric_raw.jacquard.machine_allocation.index",
            "fabric_raw.dobby.machine_allocation.index"
        ],
        "next_status"   => [],
        "button"        => [ "caption" => "تخصیص ماشین", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.production_card.machine_allocation."
    ];

    public $shoulder_width_id = 220228;
    public $dashboard_route = "fabric_raw.machine_allocation.";
    public $controller_info;

    public function __construct() {
        $this->controller_info = MachineAllocationController::$info;
    }

    public function index( Production $production ) {
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $production=ProductionCardController::NormalAmount( $production );
        // حذف سشن حالت های انتخاب
        session( ["permutation_list" =>null]);

        if ( $production->get_allocation_amount() >= $production->number ) {
            return back()->withErrors( "با توجه به اینکه مقدار تخصیص داده شده به اندازه مقدار کارت تولید می باشد، امکان تخصیص جدید وجود ندارد." );
        }
        if ( $production->product->line_product_station()->where("status_id",1200)->count() ==0 ) {
            return back()->withErrors( "هیچ مسیر محصول فعالی برای کالا یافت نشد." );
        }


        return view( $this->controller_info["view_path"] . "index", compact( "production" ) );
    }

    public function select_machine_type( Request $request, Production $production, MachineType $machine_type ) {


        $machine_id_name = "machine_type_" . $machine_type->id;

        if ( ! isset( $request->$machine_id_name ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->withErrors( "لطفا یک ماشین جهت تخصیص انتخاب نمایید." );
        }
        $machine_id = $request->$machine_id_name;

        // $dashboard_info=  ($machine_type->machine_module_type->directory_namespace."\ProductionCard\DashboardController")::$info;
        $machine_allocation_info = ( $machine_type->machine_module_type->directory_namespace . "\ProductionCard\MachineAllocationController" )::$info;

        $machine=Machine::find($machine_id);

        // دریافت قطب ها
        $last_row_log = MachineLog::getLastLogWithContour( $machine );

       $machine_log=MachineLog::where("machine_id",$machine->id)->whereNotNull("contour_1_value")->orderByDesc("id")->first();

        session( [
            "contour_1_value" => $machine_log->contour_1_value??0,
            "contour_2_value" => $machine_log->contour_2_value??0,
            "contour_3_value" => $machine_log->contour_3_value??0,
            "contour_4_value" => $machine_log->contour_4_value??0,
            "contour_5_value" => $machine_log->contour_5_value??0,
        ] );

        return redirect()->route(
            $machine_allocation_info["route"] . "select_band",
            [ $machine_id, $machine_type, $production, true ]
        );
    }

    public function checkPermission( Production $production ) {

        $result = ProductionCardController::checkPermissionConditions( $production, MachineAllocationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
