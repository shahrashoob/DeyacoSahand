<?php

namespace App\Listeners\Warehouse;

use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Utility\Script\Script1013Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\LineProductStation;
use App\Models\Utility\Financial\FinancialSoftwareTransKind;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PutInWarehouseListener {
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
    public function handle( PutInWarehouseEvent $event ) {
        $entry_type_id        = $event->form->trans_kind_item->entry_type_id;
        $packing_form_id      = $event->packing_form_id;
        $master_packing_form  = $event->master_packing_form;
        $sub_packing_form_ids = [];
        if ( isset( $master_packing_form ) ) {
            $sub_packing_form_ids = $master_packing_form->packing_form_contents()->pluck( "id" )->toArray();
        }

        $list = $event->form->item()->
        when( isset( $packing_form_id ) && count( $sub_packing_form_ids ) == 0, function ( $query ) use ( $packing_form_id ) {

            return $query->join( "packing_form_item", "packing_form_item.id", "packing_form_item_id" )->
            where( "packing_form_id", $packing_form_id );

        } )->select( "form_item.*" )->
        when( count( $sub_packing_form_ids ) > 0, function ( $query ) use ( $sub_packing_form_ids ) {

            // اگر بسته بندی اصلی است، برای همه بسته بندی های فرعی تراکنش ثبت می گردد.

            return $query->join( "packing_form_item", "packing_form_item.id", "packing_form_item_id" )->
            whereIn( "packing_form_id", $sub_packing_form_ids );

        } )->select( "form_item.*" )->
        get();

        // آیا این نوع رخداد نیاز به ثبت در سامانه مالی را دارد، اگر هیچ رکوردی پیدا نشد یعنی نیاز به ثبت در ساماننه مالی نداریم.
        $financial_software_trans_kind = FinancialSoftwareTransKind::where( [
            "trans_kind_id"         => $event->form->trans_kind,
            "warehouse_id"          => $event->form->warehouse_id
        ] )->exists();

        // وضعیت ثبت در نرم افزار مالی
        $financial_software_status_id=5103100; // عدم نیاز به ثبت
        if ( $financial_software_trans_kind ) {
            $financial_software_status_id = 5103200; // در انتظار بررسی
        }


        foreach ( $list as $form_item ) {

            WarehouseProduct::create( [

                "warehouse_id" => $event->form->warehouse_id,
                "product_id"   => $form_item->product_id,
                "input"        => $entry_type_id == 1 ? $form_item->amount : 0,
                "sub_input"    => $entry_type_id == 1 ? $form_item->sub_amount : 0,
                "output"       => $entry_type_id == 2 ? $form_item->amount : 0,
                "sub_output"   => $entry_type_id == 2 ? $form_item->sub_amount : 0,

                "form_id"              => $event->form->id,
                "form_item_id"         => $form_item->id,
                "packing_form_item_id" => $form_item->packing_form_item_id,

                "master_packing_form_id" => isset( $master_packing_form ) ?
                    $master_packing_form->id :
                    null,

                "packing_type_id"              => $form_item->packing_type_id,
                "ic"                           => $event->form->ic,
                "opp_kind"                     => 1,
                "trans_kind"                   => $event->form->trans_kind_item->id,
                "carrier_id"                   => $form_item->carrier_id,
                "degree_id"                    => $form_item->degree_id,
                "lot_number_id"                => $form_item->lot_number_id,
                "financial_software_status_id" => $financial_software_status_id
            ] );


            if ( $form_item->product->goods_kind_id == 3 && $entry_type_id == 1 ) {
                // ورود چله به انبار
                event( new WarpsAvailableEvent( $form_item->product,$event->user_id ) );
            }

            // آیا وضعیت انبار را تغییر بدهد یا خیر ( خیر برای تراکنش های مصرف و تغییر بسته بندی داخل انبار و ...)
            if ( $event->change_warehouse_status && $form_item->packing_form_item ) {
                // آیا بسته در انبار است یا خارج از انبار و داخل بسته دیگری نباشد.
                $form_item->packing_form_item->packing_form->warehouse_status_id =
                    PutInWarehouseListener::getWarehouseStatus( $entry_type_id, $form_item->packing_form_item->packing_form->warehouse_status_id ?? 0 );


                $form_item->packing_form_item->packing_form->warehouse_id = $entry_type_id == 1 ? $event->form->warehouse_id : null;
                $form_item->packing_form_item->packing_form->save();
            }
        }

        if ( isset( $master_packing_form ) ) {
            $master_packing_form->warehouse_status_id = PutInWarehouseListener::getWarehouseStatus( $entry_type_id, $master_packing_form->warehouse_status_id );

            $master_packing_form->warehouse_id = $entry_type_id == 1 ? $event->form->warehouse_id : null;
            if($entry_type_id == 2) {
                $master_packing_form->warehouse_shelving_id = null;
            }

            $master_packing_form->save();
        }

    }

    public static function getWarehouseStatus( $entry_type_id, $warehouse_status_id ) {
        return $entry_type_id == 1 ?
            // تراکنش های ورود فقط برای بسته بندی های معمولی تغییر می کند.
            ( $warehouse_status_id == 4205 ? 4205 : 4201 )
            :
            // تراکنش خروج فقط برای بسته بندی های معمولی تغییر می کند
            ( $warehouse_status_id == 4205 ? 4205 : 4202 );
    }
}
