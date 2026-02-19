<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use function back;
use function event;
use function redirect;

class ProductionCardStopOrderController extends Controller {
    //
    public static $info = [
        "route"         => "fabric_raw.machine.production_card_stop_order.",
        "enable_status" => [ "026" ],
        "button"        => [ "caption" => "صدور دستور توقف کارت جاری", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.production_card_stop_order.",
        "message"       => [ "confirm" => "آیا از صدور دستور توقف اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = ProductionCardStopOrderController::$info["route"];
        $this->view_path  = ProductionCardStopOrderController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation  = $machine->getCurrentAllocation();
        $not_allowed = false;
        foreach ( $allocation->items as $item ) {
           $not_allowed=$not_allowed || $item->production_id == null;
        }

        if($not_allowed){
            return back()->withErrors("از آنجایی که ماشین کارت تولید جاری ندارد، امکان صدور دستور توقف وجود ندارد.");
        }

        // کالای قبلی و جاری برابر هستند
        if ( ! $allocation->has_product_change ) {
            return back()->withErrors( "امکان صدور این دستور وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید." );
        }

        if ( $allocation->has_design_change ||
             ! $allocation->has_design_change && $allocation->has_warps_change
        ) {

            $machine->setStatus(
                null,
                53001, // روشن
                7003040, // در انتظار شروع استخراج چله (توقف کارت تولید)
                null,
                "Fabric_Raw"
            );


        } else {

            // ارسال تیکت تغییر تراکم پود
            if ( $allocation->has_weft_density_change ) {

                $value = Maintenance::get_density_value_for_maintenance( $allocation );
                Maintenance::AddNew( $allocation, $machine,
                    100, $value[0]." , ". $value[1], $value[2]." , ". $value[3],null,null,6003001,6002002
                );
            }
            $machine->setStatus(
                null,
                null,
                7003036, //در انتظار تغییر نخ پود (توقف کارت تولید)
                null,
                "Fabric_Raw"
            );
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 410;
        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, ProductionCardStopOrderController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

