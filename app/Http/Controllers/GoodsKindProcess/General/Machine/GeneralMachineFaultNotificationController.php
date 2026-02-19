<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\ChangeAllocationAmountController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\EndOfProductionCardTextureController;
use App\Models\LineProduct\GoodsKind\GoodsKindProductFault;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Fault\CurrentMachineFault;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType\MachineTypeMachineFaultProductFault;
use App\Models\LineProduct\Machine\MachineTypeMachineFault;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultSign;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralMachineFaultNotificationController extends Controller {
    var $view_path = "goods_kind_process.general.machine.machine_fault_notification.";
    var $route_path;
    var $dashboard_route;

    public function index( Machine $machine ) {

        $last_log = MachineLog::where( "machine_id", $machine->id )->orderByDesc( "id" )->first();
        if ( $last_log && $last_log->machine_event_type_id == 650 ) { // در حال تحویل شیفت
            return back()->withErrors( "با توجه به اینکه ماشین " . $machine->caption . " در حال تحویل شیفت می باشید، امکان اعلام نقص وجود ندارد، لطفا به اپراتور مسئول (" . $last_log->operator->fullname() . ") جهت تایید تحویل شیفت اطلاع دهید." );
        }
        // لیست رسته کالایی در کالای جاری
        $goods_kind_ids = MachineAllocation::
        join( "products", "products.id", "product_id" )->
        where( "machine_id", $machine->id )->
        whereIn( "machine_allocation.status_id", [ 5310010 ] )->
        groupBy( "goods_kind_id" )->
        pluck( "goods_kind_id" )->
        toArray();

        $goods_kind_ids[] = - 1;

        // لیست نقص ها در رسته کالایی
        // لیست نمود های نقص کالا
        $goods_kind_product_fault_sign = GoodsKindProductFault::
        join( "product_fault_product_fault_sign", "goods_kind_product_fault.product_fault_id", "product_fault_product_fault_sign.product_fault_id" )->
        join( "product_fault_signs", "product_fault_signs.id", "product_fault_sign_id" )->
        select( "product_fault_signs.id", "product_fault_product_fault_sign.product_fault_id", "product_fault_signs.caption" )->
        whereIn( "goods_kind_id", $goods_kind_ids )->
        get( "product_fault_signs.*" )->KeyBy( "id" );


        // لیست نمود های کالا در ماشین
        $machine_type_product_fault_sing = MachineTypeMachineFault::
        join( "machine_type_machine_fault_product_fault", "machine_type_machine_fault_product_fault.machine_type_id", "machine_type_machine_fault.machine_type_id" )->
        join( "product_fault_product_fault_sign", "machine_type_machine_fault_product_fault.product_fault_id", "product_fault_product_fault_sign.product_fault_id" )->
        join( "product_fault_signs", "product_fault_signs.id", "product_fault_sign_id" )->

        where( "machine_type_machine_fault.machine_type_id", $machine->machine_type_id )->
        get( "product_fault_signs.*" )->KeyBy( "id" );

        $product_fault_sings = $goods_kind_product_fault_sign;
        foreach ( $machine_type_product_fault_sing as $item ) {
            $product_fault_sings[ $item->id ] = $item;
        }


        // لیست نمود های  ماشین
        $machine_type_product_fault_sing = MachineTypeMachineFault::
        join( "machine_fault_machine_fault_sign", "machine_type_machine_fault.machine_fault_id", "machine_fault_machine_fault_sign.machine_fault_id" )->
        join( "machine_fault_signs", "machine_fault_signs.id", "machine_fault_sign_id" )->

        where( "machine_type_machine_fault.machine_type_id", $machine->machine_type_id )->
        get()->KeyBy( "id" );


        $route_path      = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        return view( $this->view_path . "index", compact( "machine", "product_fault_sings", "route_path", "dashboard_route", "machine_type_product_fault_sing" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $product_fault_sign_ids = $request->product_fault_sings;

        if ( ! $product_fault_sign_ids ) {
            $product_fault_sign_ids = [ "-1" => - 1 ];
        }

        $product_fault_sign_ids = array_keys( $product_fault_sign_ids );

        $machine_fault_sings = $request->machine_fault_sings;

        if ( ! $machine_fault_sings ) {
            $machine_fault_sings = [ "-1" => - 1 ];
        }

        $machine_fault_sings = array_keys( $machine_fault_sings );


        // لیست نقص های کالا
        $product_fault_ids = ProductFaultProductFaultSign::
        where( "product_fault_sign_id", $product_fault_sign_ids )->
        pluck( "product_fault_id" )->
        toArray();

        // لیست نمود های کالا در ماشین
        $machine_fault_list = MachineTypeMachineFaultProductFault::
        join( "machine_faults", "machine_faults.id", "machine_fault_id" )->
        where( "machine_type_id", $machine->machine_type_id )->
        whereIn( "product_fault_id", $product_fault_ids )->
        get( "machine_faults.*" );


        // لیست نقص هایی که از نمود ماشین ایجاد می شود.
        $machine_fault_list2 = MachineFault::
        join( "machine_fault_machine_fault_sign", "machine_fault_id", "machine_faults.id" )->
        whereIn( "machine_fault_sign_id", $machine_fault_sings )->
        get( "machine_faults.*" );


        foreach ( $machine_fault_list2 as $item ) {
            $machine_fault_list[] = $item;
        }

        if ( count( $machine_fault_list ) == 0 ) {
            return back()->withErrors( "با توجه به اطلاعات وارد شده امکان تشخصی عیب ماشین وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }

        // لیست نقص ها قبل از ثبت نهایی را به دست می آوریم و محاسبه می کنیم که آیا باید کارت جاری را کنسل کنیم یا خیر
        $current_active_fault_list_ids = $this->get_current_active_fault_list( $machine, $machine_fault_list );

        // بررسی کارت جاری
        $current_allocation = $machine->getCurrentAllocation();
        if ( $current_allocation ) {
            if ( $machine->machine_type->machine_module_type_id != 2 ) {
                return back()->withErrors( "ماژول اعلام نقص برای تغییر مقدار تخصیص ماشین نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید." );
            }
            $result_checkAllocationFault = Allocation::CheckAllocationFault( $machine, null, [ $current_allocation ], $current_active_fault_list_ids );
            if ( ! $result_checkAllocationFault["result"] ) {
                // با توجه به اینکه نقص های اعلام شده جزء نقص های غیر مجاز کارت تولید جاری است، پس باید مقدار تصخیص را تغییر داده و یک بار پایان بافت را اجرا کنیم.
                session( [ "machine_fault_list" => $machine_fault_list ] );

                return redirect()->route( $this->route_path . "machine_contour", $machine );
            }
        }


        $result = $this->store_fault_list( $machine, $machine_fault_list );

        if ( ! $result["result"] ) {
            return redirect()->route( $this->dashboard_route . "view", $machine )->withErrors( $result["error"] );
        }

        return redirect()->route( $this->dashboard_route . "view", $machine )->with( [ "success" => "اطلاعات نقص (ها) با موفقیت ثبت گردید." ] );
    }

    public function machine_contour( Machine $machine ) {

        $machine_fault_list = session( "machine_fault_list" );
        if ( ! $machine_fault_list ) {
            return redirect()->route( $this->route_path . "index", $machine )->withErrors( "لطفا حداقل یک نقص را انتخاب نمایید." );
        }
        $route_path = $this->route_path;

        $carrier_id                = session( "carrier_id" );
        $current_machine_fault_ids = [ - 1 ];
        foreach ( $machine_fault_list as $item ) {
            $current_machine_fault_ids[] = $item->id;
        }

         $has_requirement_for_doffs = EndOfProductionCardTextureController::has_requirement_for_doffs( $machine,$current_machine_fault_ids );
        if ( $has_requirement_for_doffs["result"] ) {

            $has_requirement_for_doffs = $has_requirement_for_doffs["doffs"];

            // اگر کارت تولید فرم رزرو داشت اجازه داف ندهد
            $reserve_production = ProductionForm::where( [
                "machine_id" => $machine->id
            ] )->whereIn(
                "status_id", [
                7002011,// در انتظار بارگذاری
            ] )->first();

            if ( $has_requirement_for_doffs && $reserve_production ) {
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
            return redirect()->back()->withErrors( $has_requirement_for_doffs["error"] );
        }

        $packing_type_option = Option::get( "packing_type_from_output_band", 0, $machine->machine_type_id );

        return view( $this->view_path . "machine_contour", compact( "machine", "route_path",
            "has_requirement_for_doffs", "carrier_id", "packing_type_option"
        ) );
    }

    public function submit_machine_contour( Request $request, Machine $machine ) {

        $machine_fault_list = session( "machine_fault_list" );
        if ( ! $machine_fault_list ) {
            return redirect()->route( $this->route_path . "index", $machine )->withErrors( "لطفا حداقل یک نقص را انتخاب نمایید." );
        }

        $current_machine_fault_ids = [ - 1 ];
        foreach ( $machine_fault_list as $item ) {
            $current_machine_fault_ids[] = $item->id;
        }
        // چک کردن اینکه در ماژول پایان بافت خطایی نداشته باشیم
        $result = EndOfProductionCardTextureController::submitHasAnError( $request, $machine, 0, false ,$current_machine_fault_ids);
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }

        if ( ! $result["woven_amount"] ) {
            return back()->withErrors( "مقدار بافته شده به درستی محاسبه نشده لطفا مجدد تلاش کنید." );
        }

// تغییر مقدار تخصیص کارت جاری
        $allocation = $result["allocation"];

        // اگر مقدار باقی مانده تخصیص بیشتر از صفر بود، تخصیص جدید اختصاص می دهیم.
        if ( $allocation->getAllocationAmount() - $result["woven_amount"] > 0 ) {
            // یک تخصیص جدید از باقی مانده درخواست برای کارت تولید ثبت می کنیم.
            $data["allocation_id"]     = $allocation->id;
            $data["allocation_amount"] = $allocation->getAllocationAmount() - $result["woven_amount"];
            $data["priority_number"]   = 1;
            QueueOfLargeOperation::AddToQueue( $data,1 );
        }
        ChangeAllocationAmountController::ChangeAllocationAmount( $allocation, round( $result["woven_amount"], 2 ) );


        // اجرا کردن یک پایان بافت کارت تولید برای کارت جاری
        EndOfProductionCardTextureController::submitConfirm( $request, $result );


        // ذخیره نقص های اعلام شده برای ماشین
        $this->store_fault_list( $machine, $machine_fault_list );

        return redirect()->route( $this->dashboard_route . "view", $machine )->with( [ "success" => "اطلاعات نقص (ها) با موفقیت ثبت گردید." ] );

    }

    public function store_fault_list( Machine $machine, $machine_fault_list ) {
        $before_report_machine_fault_count = 0;
        foreach ( $machine_fault_list as $item ) {

            // بررسی اینکه نقص قبلا گزارش شده است یا خیر
            $before_machine_fault = CurrentMachineFault::
            where( [
                "machine_id"       => $machine->id,
                "machine_fault_id" => $item->id,
            ] )->
            whereIn( "status_id", [ 6004001, 6004002, 6004003 ] )->first();

            if ( $before_machine_fault ) {
                $before_report_machine_fault_count ++;
                continue;
            }

            $status_id   = $item->need_to_confirmation ?
                6003004 : // در انتظار تایید
                6003001; // در انتظار شروع
            $maintenance = Maintenance::AddNew( null, $machine,
                200, $item->caption, $machine->caption, null, null, $status_id, null, $item->id
            );

            CurrentMachineFault::create( [
                "machine_id"       => $machine->id,
                "user_id"          => Auth::id(),
                "machine_fault_id" => $item->id,
                "active_status_id" => $item->need_to_confirmation ? 1210 : 1200,
                // غیر فعال: فعال
                "status_id"        => $item->need_to_confirmation ? 6004001 : 6004002,
                // در انتظار تایید نقص : تایید شده
                "maintenance_id"   => $maintenance->id
            ] );

        }

        if ( $before_report_machine_fault_count == count( $machine_fault_list ) ) {
            return [
                "result" => false,
                "error"  => "این نقص (ها) قبلا اعلام شده است و در دست بررسی و رفع نقص می باشد."
            ];
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 708;
        event( new MachineLogEvent( $machine, $machineLog ) );

        return [
            "result" => true
        ];
    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param                                         $machine_fault_list
     * لیست نقص های فعال ماشین قبل از ثبت نهایی
     *
     * @return array|true[]
     */
    public function get_current_active_fault_list( Machine $machine, $machine_fault_list ) {
        $machine_fault_list_ids = [];
        foreach ( $machine_fault_list as $item ) {
            // نقص هایی که اعلام شده و نیاز به تایید ندارند
            if ( ! $item->need_to_confirmation ) {
                $machine_fault_list_ids[] = $item->id;
            }
        }

        return $machine_fault_list_ids;
    }
}
