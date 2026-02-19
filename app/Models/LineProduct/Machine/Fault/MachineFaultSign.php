<?php

namespace App\Models\LineProduct\Machine\Fault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineFaultSign extends Model {
    use HasFactory;

    protected $table = "machine_fault_signs";
    protected $fillable = [ "caption",  "sms_to_posts" ];



    public function fullCaption() {
        return $this->caption . "-کد " . $this->getCode();
    }

    public static function ExistsCaption(  $caption, $id = null ) {
        if ( $id ) {
            return MachineFaultSign::
            where( "caption", $caption )->
            where( "id", "!=", $id )->
            exists();
        }

        return MachineFaultSign::
        where( "caption", $caption )->
        exists();
    }

}
