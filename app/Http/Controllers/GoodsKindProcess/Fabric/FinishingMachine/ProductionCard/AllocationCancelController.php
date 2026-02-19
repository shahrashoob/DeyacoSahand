<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\ChangeAllocationAmountController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Http\Controllers\Utility\Script\Script1007Controller;
use App\Models\GoodsKindProcess\Fabric\Fabric;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AllocationCancelController extends Controller {


    public static $info = [
        "route"              => "fabric.finishing_machine.allocation_cancel.",
        "enable_status"      => [ "001", ],
        "enable_status_full" => [ "7301001" ],
        "next_status"        => [],
        "button"             => [ "caption" => "کنسل کردن تخصیص ", "class" => "btn-danger" ],
        "view_path"          => "goods_kind_process.fabric.finishing_machine.production_card.allocation_cancel."
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

        $result= self:: AutoCancel( $allocation );

        if(!$result["result"]){
            return back()->withErrors( $result["error"] );
        }

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
        where( "priority_number", ">", $allocation->priority_number )->
        where( "machine_id", $allocation->machine_id )->
        where( "status_id", 5310040 )->
        where( "status_id","!=", 5310030 )->
        get();

        if ( count( $has_before_allocation ) > 0 && ! $is_force ) {

            $list = "";
            foreach ( $has_before_allocation as $item ) {
                $list .="";// "<b>" . $item->items()->first()->production->serial() . "</b>,&nbsp;&nbsp;";
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
        // اگر تخصیصی دارد که در انتظار تخصیص مجدد است، نباید بتواند کارت را خاتمه یافته کند.
        $list= MachineAllocation::where("production_id",$production->id)->
        whereIn("status_id",[5310050,5310060])->get();

        if(count($list) > 0){
            return [
                "result" => false,
                "error"  => "با توجه به اینکه کارت یک یا چند تخصیص دارد که در انتظار تخصیص مجدد به ماشین بعدی می باشد، امکان خاتمه یافته کردن کارت وجود ندارد."
            ];
        }

        $list = [
            -1
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


        // چک کردن اینکه ماده اولیه برای تخصیص تحویل داده نشده باشد
        $amount_delivered = ProductRequestFromAllocation::where( "allocation_id", $allocation->id )->sum( "amount_delivered" );
        if ( $amount_delivered > 0 && ! $is_force ) {
            return [
                "result" => false,
                "error"  => "با توجه به تحویل مواد اولیه برای این تخصیص امکان کنسل کردن وجود ندارد."
            ];

        }



        //چک کردن اینکه اگر یک تخصیصی وجود دارد که این تخصیص پدر است، آن تخصیص باید حتما کنسل شده باشد،
        $chide_allocation=MachineAllocation::
        where("parent_allocation_id",$allocation->id)->
        where("status_id","!=",5310030)-> // کنسل شده
        first();
        if($chide_allocation){
            return [
                "result" => false,
                "error"=>"با توجه به اینکه تخصیص شماره ".$chide_allocation->allocation_id." بر روی ماشین ".$chide_allocation->machine->caption." انجام شده است، برای کنسل کردن تخصیص، لازم است تا ابتدا تخصیص ".$chide_allocation->allocation_id." را کنسل نمایید."
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

        $has_parent_allocation=false;

        // اگر درخواست از انبار وجود دارد که شماره تخصیص آن این شماره است، باید کنسل شود.
        ProductRequestForm::where("allocation_id",$allocation->id)->
        whereIn("status_id",[7005001])->
        update(["status_id"=>7005006]);

        foreach ( $allocation->items as $item ) {
            if($item->parent_allocation_id){
// تخصیص های رزوری که در انتظار به صورت تخصیص مجدد ایجاد شده اند، وقتی کنسل می شوند، باید یک تخصیص دیگری مشابه آن ایجاد شود تا بتوانند به ماشین دیگری تخصیص دهند.
                $new_machine_allocation = $item->toArray();
                $new_machine_allocation["status_id"]=5310050;
               unset( $new_machine_allocation["allocation_id"]);

                $new_machine_allocation_obj=MachineAllocation::create($new_machine_allocation);
                $new_machine_allocation_obj->parent_allocation_id=$item->parent_allocation_id;
                $new_machine_allocation_obj->allocation_id=null;
                $new_machine_allocation_obj->status_id=5310050;
                $new_machine_allocation_obj->save();

            }
            $item->status_id = 5310030;// کنسل شده
            $item->save();
        }




        // بررسی وضعیت کارت تولید
        if ( $production->get_allocation_amount() == 0 ) {

            $new_status = Fabric::GetProductionStatus($production);
            $production->waiting_status_id = $new_status;
            if($new_status == 7301004){
                $production->status_id = 520;
            }
            $production->save();

            event( new ProductionCardLogEvent( $production ,"",$user_id,7008004) );
        }


        // کم کردن مقدار تخصیص از کانال تولید
        ChangeAllocationAmountController::ChangeAllocationAmount( $allocation, 0 ,true);

        // ارسال پیامک حذف تخصیص
        Production::sendSmsAfterAllocationCancel( $production, $allocation->machine );

        Script1007Controller::handle($allocation->machine->id,true);

        return [
            "result" => true
        ];

    }

    public function checkPermission( Production $production ) {

        $result = DashboardController::checkPermissionConditions( $production,
            \App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCard\AllocationCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }


}
