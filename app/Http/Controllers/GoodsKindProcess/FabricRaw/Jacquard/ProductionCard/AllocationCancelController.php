<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\ChangeAllocationAmountController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFromAllocation;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AllocationCancelController extends Controller {

    public static $info = [
        "route"              => "fabric_raw.jacquard.allocation_cancel.",
        "enable_status"      => [ "001", "002", "003" ],
        "enable_status_full" => [ "7051001", "7051002", "7051003" ],
        "next_status"        => [],
        "button"             => [ "caption" => "کنسل کردن تخصیص ", "class" => "btn-danger" ],
        "view_path"          => "goods_kind_process.fabric_raw.production_card.allocation_cancel."
    ];

    public function index( Allocation $allocation, Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $result_cancel=  self:: CheckForChancel( $allocation );
        if ( ! $result_cancel["result"] ) {
            return back()->withErrors( $result_cancel["error"] );
        }

        self:: AutoCancel( $allocation );

        //        // اگر کارت رزرو ندارد و   در انتظار پایان بافت (کارت تولید جاری) است، وضعیت ماشین باید به درحال بافت تغییر کند.
        if ( ! $allocation->machine->getFirstReserveAllocation() && $allocation->machine->production_status_id == 7003042 ) {


            if(!$allocation->machine->getCurrentAllocation()){ // کالای جاری ندارد
                $allocation->machine->setStatus(
                    null,
                    53002,
                    7003016, // نداشتن سفارش
                    1615,
                    "Fabric_Raw");
            }
            else {
                $allocation->machine->setStatus(
                    null,
                    53001,
                    7003016, // درحال بافت
                    null,
                    "Fabric_Raw");
            }

        }


        return back()->with( [ "success" => "تخصیص ماشین با موفقیت کنسل شد." ] );


    }

    public static function CheckForChancel( Allocation $allocation, $is_force = false ) {

        $allocation_item = $allocation->items()->first();
        if ( ! $allocation_item ) {
            return [
                "result" => false,
                "error"  => "کارت تولید مربوط به تخصیص یافت نشد."
            ];
        }
        $production=$allocation_item->production;

        $has_before_allocation = Allocation::
        where( "id", ">", $allocation->id )->
        where( "machine_id", $allocation->machine_id )->
        where( "status_id", 5310040 )->
        get();

        if ( count( $has_before_allocation ) > 0 && ! $is_force ) {

            $list = "";
            foreach ( $has_before_allocation as $item ) {
                $list .= "<b>" . $item->items()->first()->production->serial() . "</b>,&nbsp;&nbsp;";
            }

            return [
                "result" => false,
                "error"  => "لطفا ابتدا  " . count( $has_before_allocation ) .
                            "  کارت تولید که قبل از کارت " . $production->serial() .
                            " به ماشین " . $allocation->machine->fullCaption() .
                            " تخصیص داده شده اند را حذف نموده و سپس اقدام نمایید. " .
                            "<br/>" . $list
            ];

        }

        $list = [
            7003026,
            7003028,
            7003030,
            7003043,
            7003047,
        ];
        if ( in_array( $allocation->machine->production_status_id, $list ) && ! $is_force ) {
            return [
                "result" => false,
                "error"  => "وضعیت تولید ماشین جهت کنسل کردن تخصیص معتبر نیست."
            ];

        }

        if ( $allocation->status_id != 5310040 ) {
            return [
                "result" => false,
                "error"  => "تنها تخصیص های در حالت رزرو امکان حذف دارند."
            ];
        }


        //چک کردن اینکه برای تخصیص هیچ فرم تولیدی ایجاد نشده باشد
        $production_form_item = ProductionFormItem::where( "allocation_id", $allocation->id )->first();
        if ( $production_form_item ) {
            return [
                "result" => false,
                "error"  => "با توجه به فرم تولید به شماره " . $production_form_item->production_form->code . " برای تخصیص ایجاد شده است، امکان حذف تخصیص وجود ندارد."
            ];
        }

        // چک کردن اینکه فرم درخواست چله برای کارت تولید نرفته باشد
        $warps_form = ProductRequestForm::where( [
            "applicant_id"      => $allocation->machine->id,
            "applicant_type_id" => 10,
            "allocation_id"     => $allocation->id
        ] )->
        orderByDesc( "id" )->first();

        if ( $warps_form ) {
            if ( in_array( $warps_form->status_id, [ 7005001, 7005003, 7005006 ] ) ) {
                $warps_form->status_id = 7005006;
                $warps_form->save();
                event( new ProductRequestFormLogEvent( $warps_form, "", null, 7005008 ) );
            } else {
                return [
                    "result" => false,
                    "error"  => "با توجه به اینکه  چله برای درخواست کالا از انبار به شماره " . $warps_form->getCode() . " مربوط به این کارت تولید تحویل شده است، امکان حذف تخصیص وجود ندارد."
                ];

            }
        }

        // چک کردن اینکه ماده اولیه برای تخصیص تحویل داده نشده باشد
        $amount_delivered = ProductRequestFromAllocation::where( "allocation_id", $allocation->id )->sum( "amount_delivered" );
        if ( $amount_delivered > 0 && ! $is_force ) {
            return [
                "result" => false,
                "error"  => "با توجه به تحویل مواد اولیه برای این تخصیص امکان کنسل کردن وجود ندارد."
            ];

        }
        return [
            "result" => true,
            "production"=>$production
        ];
    }

    public static function AutoCancel( Allocation $allocation,   $user_id = null ) {

        $allocation_item = $allocation->items()->first();
        if ( ! $allocation_item ) {
            return [
                "result" => false,
                "error"  => "کارت تولید مربوط به تخصیص یافت نشد."
            ];
        }
        $production=$allocation_item->production;

        $machineLog                        = MachineLog::create();
        $machineLog->machine_event_type_id = 95; // کنسل کردن تخصیص
        $machineLog->allocation_id         = $allocation->id;
        event( new MachineLogEvent( $allocation->machine, $machineLog, "", false, false, $user_id ) );

        // کنسل کردن تخصیص
        $allocation->status_id = 5310030;// کنسل شده
        $allocation->save();

        foreach ( $allocation->items as $item ) {
            $item->status_id = 5310030;// کنسل شده
            $item->save();
        }

        // بررسی وضعیت کارت تولید
        if ( $production->get_allocation_amount() == 0 ) {
            $production->status_id = 500;
            //در انتظار تخصیص ماشین، .
            $production->waiting_status_id = 7001001;
            $production->save();
            event( new ProductionCardLogEvent( $production ,"",$user_id,7005008) );
        }

        // کم کردن مقدار تخصیص از کانال تولید
        ChangeAllocationAmountController::ChangeAllocationAmount( $allocation, 0 ,true);

        // ارسال پیامک حذف تخصیص
        Production::sendSmsAfterAllocationCancel( $production, $allocation->machine );

        return [
            "result" => true
        ];

    }

    public function checkPermission( Production $production ) {

        $result = DashboardController::checkPermissionConditions( $production,
            \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard\AllocationCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }


}
