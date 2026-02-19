<?php

namespace App\Models\LineProduct\Product\Fault;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductFault extends Model {
    use HasFactory;

    protected $table = "product_faults";
    protected $fillable = [ "caption", "need_to_move_shift", "need_to_confirmation", "sms_to_posts","product_fault_type_id","product_fault_fixed_type_id" ];

    public function product_fault_signs() {
        return $this->hasMany( ProductFaultProductFaultSign::class, "product_fault_id" );
    }

    public function getCode() {

        if ( $this->code ) {
            return $this->code;
        }
        $code = $this->id;

        $code = Str::of( $code )->
        when( $code < 100, function ( $string ) {
            return Str::of( '0' )->append( $string );
        } )->
        when( $code < 10, function ( $string ) {
            return Str::of( '0' )->append( $string );
        } );

        $this->code = $code;
        $this->save();

        return $code;

    }

    public function fullCaption() {
        return $this->caption . "-کد " . $this->getCode();
    }

    public static function ExistsCaption( $caption, $id = null ) {
        if ( $id ) {
            return ProductFault::
            where( "caption", $caption )->
            where( "id", "!=", $id )->
            exists();
        }

        return ProductFault::
        where( "caption", $caption )->
        exists();
    }

}
