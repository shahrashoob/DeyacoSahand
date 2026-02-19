<?php

namespace App\Listeners\Warps;

use App\Events\Machine\MachineLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\GoodsKindProcess;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\Notification\SMSMessage;

class WarpsAvailableListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( WarpsAvailableEvent $event ) {

        $productRequestFormList = ProductRequestForm::where( [
                "status_id" => ProductRequestForm::$perfix_status_code . "003", // در انتظار تکمیل موجودی
            ]
        )->get();

        foreach ( $productRequestFormList as $productRequestForm ) {


            // چک کردن موجودی چله برای ماشین بافندگی
            $warps_is_in_warehouse = Warps::warpsExistInWarehouse(null,  $productRequestForm );
            if ( $warps_is_in_warehouse ) {

                // ویرایش فرم درخواست اگر در انتظار چله است
                $productRequestForm->status_id = ProductRequestForm::$perfix_status_code . "001";
                $productRequestForm->save();
                event( new ProductRequestFormLogEvent( $productRequestForm, "", null, 7005015, $event->user_id ) ); //موجود شدن کالا

                // ویرایش وضعیت ماشین اگر در انتظار چله است
                $machine = Machine::where( "warehouse_id", $productRequestForm->applicant_id )->first();
                if ( ! $machine ) {
                    SMSMessage::ExceptionError( "برای درخواست شماره " . $productRequestForm->code . " امکان به دست آوردن ماشین درخواست دهنده وجود ندارد." );
                    continue;
                }
                $new_production_status = 0;

                if ( $machine->production_status_id == "7003019" ) {
                    $new_production_status = MachineModuleType::getChecklist( $machine->machine_type->machine_module_type_id, "warps_delivery_7003019" );
                }
                // ویرایش وضعیت ماشین اگر در انتظار آماده سازی چله جهت تعویض بود است
                if ( $machine->production_status_id == "7003021" ) {
                    $new_production_status = MachineModuleType::getChecklist( $machine->machine_type->machine_module_type_id, "warps_delivery_7003021" );
                }
                // ویرایش وضعیت ماشین اگر در انتظار اماده سازی چله جهت تغییر کالیته بود
                if ( $machine->production_status_id == "7003029" ) {
                    $new_production_status = MachineModuleType::getChecklist( $machine->machine_type->machine_module_type_id, "warps_delivery_7003029" );

                }

                if ( $new_production_status != 0 ) {
                    $machine->on_status_id          = 53002;
                    $machine->machine_off_reason_id = 1617;
                    $machine->production_status_id  = $new_production_status;
                    $machine->save();

                    $machineLog                        = new MachineLog();
                    $machineLog->machine_event_type_id = 250;
                    event( new MachineLogEvent( $machine, $machineLog, "", null, null, $event->user_id ) );
                }
            }
        }
    }
}
