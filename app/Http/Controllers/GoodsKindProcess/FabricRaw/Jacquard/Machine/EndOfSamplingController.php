<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\SampleEndOfProductionController;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;

use Illuminate\Http\Request;

class EndOfSamplingController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_sampling.",
        "enable_status" => [ "054" ],
        "button"        => [ "caption" => "پایان نمونه گیری", "class" => "btn-primary" ],
//        "message"       => [ "confirm" => "آیا از پایان راه انداری شیفت اطمینان دارید؟" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.end_of_sampling.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfSamplingController::$info["route"];
        $this->view_path  = EndOfSamplingController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $carrier_id      = session( "carrier_id" );
        $shift_work_option = Option::get( "shift_work" );



        $has_requirement_for_doffs = EndOfProductionCardTextureController::has_requirement_for_doffs( $machine );
        if ( $has_requirement_for_doffs["result"]  ) {

            $has_requirement_for_doffs = $has_requirement_for_doffs["doffs"];

            // اگر کارت تولید فرم رزرو داشت اجازه داف ندهد
            $reserve_production = ProductionForm::where( [
                "machine_id" => $machine->id
            ] )->whereIn(
                "status_id", [
                7002011,// در انتظار بارگذاری
            ] )->first();

            if($has_requirement_for_doffs && $reserve_production) {
                $current_production = ProductionForm::where( [
                    "machine_id" => $machine->id
                ] )->whereIn(
                    "status_id", [
                    7002008, // در حال بافت پارچه پایانی)
                ] )->first();

                if ( ! $current_production ) {
                    return back()->withErrors( "غلطک پارچه برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید. " );
                }

                return back()->withErrors( " لطفا غلطک" .
                                           $current_production->carrier->code
                                           . " را استخراج و غلطک " .
                                           $reserve_production->carrier->code . " را بارگذاری نمایید." );
            }

        } else {
            return redirect()->back()->withErrors($has_requirement_for_doffs["error"]);
        }



        $packing_type_option = Option::get( "packing_type_from_output_band", 0, $machine->machine_type_id );


        return view( $this->view_path . "index", compact(
            "machine",
            "shift_work_option",
            "has_requirement_for_doffs","carrier_id","packing_type_option"
        ) );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

// چک کردن اینکه در ماژول پایان بافت خطایی نداشته باشیم
        $result = EndOfProductionCardTextureController::submitHasAnError( $request, $machine );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }

        $allocation=$result["allocation"];
        $machine_allocation=$allocation->items()->first();

        // اگر کارت نمونه گیری، دارای درخواست طراحی کالا باشد، باید درخواست طراحی کالا هم بروز شود.
        $product_creation_process=ProductCreationProcess::
            where("sample_production_id",$machine_allocation->production_id)->
            first();
        if($product_creation_process){
            $result_sampling = SampleEndOfProductionController::PostSubmit($product_creation_process);
            if (!$result_sampling["result"]) {
                return back()->withErrors($result_sampling["error"]);
            }
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 690;
        event( new MachineLogEvent( $machine, $machineLog ) );

        // اجرا کردن یک پایان بافت کارت تولید برای کارت جاری
        EndOfProductionCardTextureController::submitConfirm( $request, $result);


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfSamplingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
