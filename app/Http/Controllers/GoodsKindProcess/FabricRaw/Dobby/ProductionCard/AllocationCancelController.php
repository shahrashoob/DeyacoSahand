<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionCard;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\Production;
use function back;
use function event;

class AllocationCancelController extends Controller {

    public static $info = [
        "route"         => "fabric_raw.allocation_cancel.",
        "enable_status" => [ "001", "002", "003" ],
        "next_status"   => [],
        "button"        => [ "caption" => "کنسل کردن تخصیص ", "class" => "btn-danger" ],
        "view_path"     => "goods_kind_process.fabric_raw.production_card.allocation_cancel."
    ];

    public function index( Production $production, Machine $machine ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیصی برای ماشین یافت نشد." );
        }
        $design_form = FabricRawDesignForm::where( [
            "machine_id"    => $machine->id,
            "allocation_id" => $allocation->id
        ] )->first();

        if ( $machine->production_status_id != 7003026 ) {
            return back()->withErrors( "وضعیت تولید ماشین جهت کنسل کردن تخصیص معتبر نیست." );

        }

        if ( ! $production->machine_reserve()->where( "machine_id", $machine->id )->exists() ) {
            return back()->withErrors( "تنها تخصیص های در حالت رزرو امکان حذف دارند." );
        }
        if ( $design_form && $design_form->status_id != 7004001 ) { // در انتظار شروع طراحی
            return back()->withErrors( "طراحی مربوط به ماشین شروع شده و امکان حذف تخصیص وجود ندارد." );
        }

        $machine_last_log = MachineLog::where( [ "machine_id"            => $machine->id,
                                                 "machine_event_type_id" => 90
        ] )-> // تخصیص ماشین
        orderByDesc( "id" )->
        first();
        if ( isset( $machine_last_log ) ) {
            $machine->production_status_id = $machine_last_log->production_status_id;
            $machine->on_status_id         = $machine_last_log->on_status_id;
            $machine->active_status_id     = $machine_last_log->active_status_id;
            $machine->save();

            $machineLog                        = MachineLog::create();
            $machineLog->machine_event_type_id = 95; // کنسل کردن تخصیص
            event( new MachineLogEvent( $machine, $machineLog ) );

            if(isset($design_form)){
                $design_form->status_id = 7004006;
                $design_form->save();
                event( new FabricRawDesignFormLogEvent( $design_form ) );
            }


            $allocation->status_id = 5310030;// کنسل شده
            $allocation->save();

            foreach ( $allocation->items as $item ) {
                $item->reserve_production_id = null;
                $item->production_id         = null;
                $item->status_id             = 5310030;// کنسل شده
                $item->save();
            }

            // برگرداندان تخصیص قبلی از پایان یافته به جاری
            $before_allocation=$allocation->before_allocation();
            if($before_allocation) {
                $before_allocation->status_id = 5310010;// جاری
                $before_allocation->save();

                foreach ( $before_allocation->items as $item ) {
                    $item->status_id = 5310010;// جاری
                    $item->save();
                }
            }
            return back()->with( [ "success" => "تخصیص ماشین با موفقیت کنسل شد." ] );

        } else {
            return back()->withErrors( "اطلاعات لاگ تخصیص ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید." );
        }

    }

    public function checkPermission( Production $production ) {

        $result = DashboardController::checkPermissionConditions( $production, AllocationCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
