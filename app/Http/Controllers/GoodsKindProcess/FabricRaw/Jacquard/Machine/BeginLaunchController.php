<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use function back;
use function event;
use function redirect;

class BeginLaunchController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_launch.",
        "enable_status" => [ "013" ],
        "button"        => [ "caption" => "شروع راه اندازی شیفت", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از شروع راه انداری شیفت اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginLaunchController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 560;

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید." );
        }
        $productionFromItemLot = FabricRaw::getCurrentLot( $allocation, false, true );
        if ( count( $productionFromItemLot ) == 0 ) {
            return back()->withErrors( "با توجه به اینکه لات پارچه ایجاد نگردید است، امکان شروع راه اندازی شیف وجود ندارد،
             لطفا با پشتیبانی تماس بگیرید." );
        }

        // بررسی اینکه مقداری از پارچه که باید در راه اندازی شیف باید بافته شود، بافته شده است یا خیر
        $some_of_fabric_that_needs_to_woven = MachineModuleTypePropertyValue::getValue("70030021401", $machine->machine_type_id);
        $the_contour_error_value = MachineModuleTypePropertyValue::getValue("70030021402", $machine->machine_type_id);
        $number_of_doff_need_to_woven = MachineModuleTypePropertyValue::getValue("70030021404", $machine->machine_type_id);
        //در صورتی که واحد فرعی 2، کالایی قاب است، <br/>حداقل مقدار راه اندازی ( 1- تعداد قاب 2- مقدار راه اندازی) باشد.
        $min_of_frame_or_value = MachineModuleTypePropertyValue::getValue("70030021405", $machine->machine_type_id);

        if ($some_of_fabric_that_needs_to_woven == "" || $the_contour_error_value == "" || $number_of_doff_need_to_woven == "" || $number_of_doff_need_to_woven == "0" || $min_of_frame_or_value == "") {
            return back()->withErrors("تنظیمات راه اندازی شیف انجام نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }


        $some_of_fabric_that_needs_to_woven = $some_of_fabric_that_needs_to_woven / 100; // متر
        $current_production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");
        if (!$current_production_form) {
            return back()->withErrors("فرم تولید جاری ماشین یافت نشده، لطفا با پشتیبانی تماس بگیرید.");
        }
        $current_production_form_item = $current_production_form->items()->orderByDesc("id")->first();
        $product = $current_production_form_item->product;

        $result=EndOfLaunchController::GetWovenAmount($product,$number_of_doff_need_to_woven,$some_of_fabric_that_needs_to_woven,$min_of_frame_or_value);
        if(!$result["result"]){
            return back()->withErrors($result["error"]);
        }

        $machine->setStatus(
            null,
            53001, // روشن
            7003048,
            null );

        $amount=$result["message"];

        event( new MachineLogEvent( $machine, $machineLog ,$amount." متر ") );




        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" =>$result["message"] ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginLaunchController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
