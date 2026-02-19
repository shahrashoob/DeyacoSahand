<?php

namespace App\Models\GoodsKindProcess\Warps\RequestForm;

use App\Events\Warps\WarpsAvailableEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Models\Form\Form;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WarpsRequestForm extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "product_request_forms";
    protected $fillable = [
        "machine_id",
        "status_id",
        "allocation_id",
        "user_id",
    ];
    public static $perfix_status_code = "7005";

    public function items() {
        return $this->hasMany( WarpsRequestFormItem::class, "product_request_form_id" );
    }

    public function getCode() {
        if ( isset( $this->code ) ) {
            return $this->code;
        }
// Request Product
        $this->code = "DCRP/" . ( 1000 + $this->id );
        $this->save();

        return $this->code;
    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }

    public static function newRequest( Allocation $allocation, Machine $machine, $warps_is_in_warehouse ) {

        return Product\ProductRequest\ProductRequestForm::newRequest( $allocation, $machine->id, 10, $warps_is_in_warehouse, null,now() );
    }

    public function cancelRequest() {
        $this->status_id = 7005006; // کنسل شده
        $this->save();

        foreach ( $this->items as $item ) {
            event( new WarpsAvailableEvent( $item->product ) );
        }
        event( new WarpsRequestFormLogEvent( $this ) );
    }

    public function machine() {
        if( $this->applicant_type_id == 10){
            return  $this->belongsTo( Machine::class ,"applicant_id","id");
        }
       1/0;
    }

    public function production() {
        return $this->belongsTo( Production::class );
    }


    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function form() {
        return $this->belongsTo( Form::class );
    }

    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }

    public static function getLatestRequestForm( $machine_id ) {
        return Product\ProductRequest\ProductRequestForm::
        where( "status_id", 7005002 )->
        where( "applicant_id", $machine_id )->
        where( "applicant_type_id", 40 )->
        orderByDesc( "id" )->
        first();
    }

    public function getCreateFormUser() {
        $first = Product\ProductRequest\ProductRequestForm::where( [ "product_request_form_id" => $this->id ] )->orderBy( "id" )->first();
        if ( isset( $first ) ) {
            return $first->worker;
        }
    }


    public function lot_number_should_be_check_equals() {
        $machine_input_output_bands = CurrentMachineInput::
        where( [
            "allocation_id" => $this->allocation_id,
            "goods_kind_id" => 3
        ] )->
        get();
        // کمتر از 2 تا باشد
        if ( count( $machine_input_output_bands ) <= 1 ) {
            return false;
        }

        if ( $machine_input_output_bands[0]->band_code == $machine_input_output_bands[1]->band_code ) {
            return true;
        }
        foreach ( $machine_input_output_bands as $item ) {
            if ( $item->percent_of_use != 100 ) {
                return true;
            }
        }

        return false;
    }

    public function get_log_with_status( $status_id = false ) {

        if ( ! $status_id ) {
            return WarpsRequestFormLog::where( [ "product_request_form_id" => $this->id ] )->get();
        }

        return WarpsRequestFormLog::where( [
            "status_id"               => $status_id,
            "product_request_form_id" => $this->id
        ] )->first();
    }

//    public static function setEmptyBeforeWarpsCarrier( Machine $machine, $number_skip = 0 ) {
//
//        // تغییر وضعیت حامل چله قبلی به خالی
//        $product_request_form = Product\ProductRequest\ProductRequestForm::where( [
//            "applicant_id" => $machine->id,
//            "applicant_type_id" => 10
//        ] )->where( "status_id", WarpsRequestForm::$perfix_status_code . "002" )->
//        orderByDesc( "id" )->
//        skip( $number_skip )->
//        first();
//
//        if ( ! isset( $product_request_form ) ) {
//            return [
//                "result"  => false,
//                "message" => "درخواست چله قبلی یافت نشد، لطفا با پشتیبانی تماس بگیرید."
//            ];
//        }
//
//        $setEmpty = false;
//        foreach ( $product_request_form->forms[0]->form->item as $form_item ) {
//            if ( isset( $form_item->carrier ) &&
//                 $form_item->carrier->status_id == 5320003 // در حال مصرف
//            ) {
//                $setEmpty = true;
//                $form_item->carrier->setEmpty();
//            }
//        }
//
//        if ( $setEmpty ) {
//            Warps::updateCarrierInCurrentInputOutputBand( $machine, "setEmpty" );
//        }
//
//        event( new WarpsRequestFormLogEvent( $product_request_form ) );
//
//        return [
//            "result" => true
//        ];
//
//    }
}
