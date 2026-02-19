<?php

namespace App\Models\LineProduct\Machine\Fault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MachineFault extends Model {
    use HasFactory;

    protected $table = "machine_faults";
    protected $fillable = [ "caption", "sms_to_posts","need_to_confirmation" ,"need_to_confirmation_for_fix"];

    public function machine_fault_signs() {
        return $this->hasMany( MachineFaultMachineFaultSign::class,"machine_fault_id" );
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
            return MachineFault::
            where( "caption", $caption )->
            where( "id", "!=", $id )->
            exists();
        }

        return MachineFault::
        where( "caption", $caption )->
        exists();
    }

}
