<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction\ProductionCard;


use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use function back;
use function event;
use function redirect;
use function session;

/**
 *  تکمیل SpecialProduction
 */
class MachineAllocationController extends Controller {
    //
    public static $info = [
        "route"         => "fabric.special_production.machine_allocation.",
        "enable_status" => [ "001", "002", "003" ],
        "next_status"   => [],
        "button"        => [ "caption" => "تخصیص ماشین ", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric.special_production.production_card.machine_allocation."
    ];

    public $dashboard_route = "fabric.machine_allocation.";
    public $controller_info;

    public function __construct() {
        $perfix_status_code    = DashboardController::$perfix_status_code;
        $this->view_path       = View::share( "perfix_status_code", $perfix_status_code );
        $this->controller_info = MachineAllocationController::$info;
    }

    public function select_band( Request $request, $machine_id, MachineType $machine_type, Production $production, $is_first_production = false, $lineProductStation=null ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $key                    = "machine_type_" . $machine_type->id;
        $machine                = Machine::find( $request->$key ?? $machine_id );
        $allocation_amount_list = [];

        if ( ! isset( $machine ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->withErrors( "لطفا یک ماشین جهت تخصیص انتخاب نمایید." );
        }


        // حذف کارت تولید های در انتظار تایید
        if ( $is_first_production ) {
            $allocation_delete_ids = MachineAllocation::where( [
                "machine_id" => $machine->id,
                "status_id"  => 5310005
            ] )->pluck( "allocation_id" )->toArray();
            Allocation\AllocationData::whereIn( "allocation_id", $allocation_delete_ids )->delete();
            MachineAllocation::where( [ "machine_id" => $machine->id, "status_id" => 5310005 ] )->delete();

        }

        // چک کردن اینکه گروه ماشین در خط محصول وجود داشته باشد.
        if ( ! $production->product->line_product_station()->where( "machine_type_id", $machine_type->id )->exists() ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "ماشین در مسیر - محصول های تعریف شده برای محصول وجود ندارد." );
        }
        $machine_check = Machine::
        join( "machine_status", "machines.production_status_id", "machine_status.production_status_id" )->
        where( [
            "machine_type_id"                   => $machine_type->id,
            "possibility_of_allocation_machine" => 1,
            "machines.id"                       => $machine->id,
            "machines.active_status_id"         => 1200
        ] )->first();

        // محاسبه برای باند 1
        /**
         * محاسبه حداکثر مقدار قابل تخصیص برای کارت تولید
         * در  باند خروجی
         */
        $open_band_list        = [ 1 ];
        $band_count_allocation = 1;// تعداد باند خورجی ماشین های matthys یک می باشد.
        $sum_allocation_amount = MachineAllocation::where( "production_id", $production->id )->
        whereIn( "status_id", [ "5310010", "5310020", "5310040" ] )->sum( "allocation_amount" );

        if ( $production->number - $sum_allocation_amount <= 0 ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "با توجه به مقدار کارت تولید و تخصیصی ها انجام شده، امکان تخصیص جدید برای کارت تولید   وجود ندارد." );

        }


        //مقدار تخصیص را به تعداد باندهای مشابه تقسیم می کنیم
        $allocation_amount_list[1] = round( ( $production->number - $sum_allocation_amount ) / $band_count_allocation, 2 );


        $reserve_after_allocation_option = Production::getReserveAfterAllocationOption( $production, $machine );

        if ( $reserve_after_allocation_option["result"] == false ) {
            return back()->withErrors( $reserve_after_allocation_option["message"] );
        }

        $reserve_after_allocation_option = $reserve_after_allocation_option["list"];

        return \view( $this->controller_info["view_path"] . "select_band", compact( "allocation_amount_list", "band_count_allocation", "machine", "production", "allocation_amount_list", 'open_band_list', "reserve_after_allocation_option" ) );


    }


    public function confirm_submit( Request $request, Machine $machine ) {


        // در صورتی که کارت نمونه گیری باشد، این متغیر در select_band مقدار دهی می شود.
        $reserve_after_allocation_id = session( "reserve_after_allocation_id" );


        /**
         * حدف تخصیص های معلق قبلی
         */
        MachineAllocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->delete();


        $production = Production::find( $request->production_id );
        if ( ! $production ) {
            return back()->withErrors( "کارت تولید جهت تخصیص یافت نشد." );
        }


        $band_id              = "band_1"; // چون فقط یک باند دارد
        $band_name            = "band_name_1";// مقدار تخصیص به باند 1
        $band_amount          = "band_amount_1";
        $allocation_amount    = $request->$band_amount;
        $number_of_doffs_done = 0;
        $max_number_of_doffs  = 1;
        $amount_of_each_doffs = $allocation_amount;


        if ( $production->get_allocation_amount() + $allocation_amount > $production->number ) {
            return back()->withErrors( "مقدار تخصیص بیش از مقدار کارت تولید می باشد." );
        }
        // ثبت یک تخصیص با وضعیت معلق
        event( new MachineAllocationEvent(
            $production,
            $machine,
            "Fabric",
            $request->$band_id,
            $allocation_amount,
            $number_of_doffs_done,
            $max_number_of_doffs,
            $amount_of_each_doffs
        ) );

        // گرفتن تخصیص معلق
        $allocation = Allocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->first();


        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $allocation_status = 5310040; // تخصیص رزرو شده

        // تغییر وضعیت تخصیص فعلی
        $allocation->status_id = $allocation_status;

        // به دست آوردن اولویت کارت
        switch ( $production->production_type_id ) {
            case 1:
                $priority_number = $machine->ReserveAllocation()->count() + 1;
                break;
            case 2:
                $after_allocation = Allocation::find( $reserve_after_allocation_id );
                if ( ! $after_allocation ) {
                    return back()->withErrors( "تخصیصی که باید بعد از آن کارت نمونه گیری تخصیصی داده شود، یافت نشد." );
                }
                $priority_number = $after_allocation->priority_number + .5;
                break;
            default:
                return back()->withErrors( "نوع کارت تولید مشخص نشده است" );
        }

        $allocation->priority_number = $priority_number;
        $allocation->save();

        // تغیر وضعیت همه آیتم های تخصیص
        foreach ( $allocation->items as $item ) {

            $item->status_id = $allocation_status;
            $item->save();

// بروز رسانی وضعیت کارت تولید
            if ( $item->production->waiting_status_id == "7301" . "001" ) { // در انتظار تخصیص
                $item->production->waiting_status_id = "7301" . "002"; // در انتظار نصب و راه اندازی
                $item->production->save();
                event( new ProductionCardLogEvent( $item->production ) );
            }
        }

        // آخرین وضعیت  قبل از تخصیص ماشین
        $machineLog = MachineLog::create();

        $machineLog->machine_event_type_id = 90; // وضعیت قبل از تخصیص ماشین
        event( new MachineLogEvent( $machine, $machineLog ) );


        // لاگ تخصیص جدید ماشین
        $machineLog                        = MachineLog::create();
        $machineLog->machine_event_type_id = 92;
        $machineLog->allocation_id         = $allocation->id;
        event( new MachineLogEvent( $machine, $machineLog ) );

        Production::sendSmsAfterAllocation( $production, $machine );


        MachineAllocationController::updatePriorityNumber( $machine );

        // اگر وضعیت ماشین نداشتن سفارش باشد ماژول *** می شود.
        if ( $machine->production_status_id == 7203001 ) {
//            EndOfBeamingController::submitConfirm( $request, $resultEndOfBeamingController );
        }


        return redirect()->route( "production.dashboard.list" )->with( [ "success" => "عملیات تخصیص با موفقیت انجام شد." ] );

    }

    public function checkPermission( Production $production ) {

//         بررسی دسترسی در ماژول
        $result = DashboardController::checkPermissionConditions( $production, null, );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }


    public static function updatePriorityNumber( Machine $machine ) {
        $reserve_list    = $machine->ReserveAllocation()->get();
        $priority_number = 1;
        foreach ( $reserve_list as $allocation ) {

            $allocation->priority_number = $priority_number;
            $allocation->save();

            $priority_number ++;
        }

        $current_allocation = $machine->getCurrentAllocation();
        if ( $current_allocation ) {
            $current_allocation->priority_number = 0;
            $current_allocation->save();
        }
    }

}

