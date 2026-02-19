<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypeProperty;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Http\Request;


class FailureShelvingController extends Controller {
    public static $info = [
        "route"         => "warps.matthys.machine.failure_shelving.",
        "enable_status" => [ "003" ],
        "button"        => [ "caption" => "عدم قفسه گذاری", "class" => "btn-danger" ],
        "view_path"     => "goods_kind_process.warps.matthys.machine.failure_shelving.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";

    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path  = self::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین یافت نشد." );
        }

        return view( $this->view_path . "index", compact( "machine" ) );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید." );
        }


        $machine_allocation = $allocation->items()->first();
        if ( ! $machine_allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید." );
        }

        $production_form = $machine->getCurrentProductionForm();
        if ( $production_form ) {
            $production_form->ChangeStatus( 7202004, $text = "", $event_id = 7202002 );
        }

        // تغییر وضعیت تخصیص فعلی
        $allocation->status_id = 5310030; //  تخصیص های کنسل شده
        $allocation->save();
        foreach ( $allocation->items as $item ) {
            $item->status_id = 5310030; //  تخصیص های کنسل شده
            $item->save();
            $production = $item->production;
            // بررسی وضعیت کارت تولید
            if ( $production->get_allocation_amount() == 0 ) {
                $production->status_id = 500;
                //در انتظار تخصیص ماشین، .
                $production->waiting_status_id = 7201001;
                $production->save();
                event( new ProductionCardLogEvent( $production ) );
            }
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 1060; // عدم قفسه گذاری
        $machineLog->allocation_id         = $allocation->id;
        $machineLog->save();

        event( new MachineLogEvent( $machine, $machineLog ) );

        $message = $request->description . " بر روی ماشین قفسه گذاری نشد.";
        MachineModuleTypeProperty::SendSms( $message, $allocation, 72030011201 );

        // اجرای ماژول پایان قفسه گذاری
        $result = EndOfBeamingController::submitHasAnError( $request, $machine );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }


        $result = EndOfBeamingController::submitConfirm( $request, $result );

        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfShelvingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
