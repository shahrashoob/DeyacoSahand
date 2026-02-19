<?php

namespace App\Models\Utility;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\MachineStatus;
use App\Models\LineProduct\Station;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Production\ProductionFormStatus;
use App\Models\Production\ProductionWaitingStatus;
use App\Models\Utility\Module\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Status extends Model {
    use HasFactory;
    public $timestamps = false;
    protected $table = "status";
    protected $fillable = [ "caption", "main_status_id","code","status_type_id" ];

    public static function GetIdFromCaption( $caption, $type = 100) {

        $unit = Status::where( "caption", "like", "%" . $caption . "%" )
                      ->where( "status_type_id", $type )
                      ->first();

        return isset( $unit ) ? $unit->id : - 100;
    }

    public function orderPermissionType() {
        return $this->belongsTo( OrderPermissionType::class, "id", "order_status_id" );
    }

    public function main_status() {
        return $this->belongsTo( Status::class, "main_status_id", "id" );
    }

    public function status_type() {
        return $this->belongsTo( StatusType::class );
    }

    public function getCode() {
        return $this->code;
    }

    public function getCaption( $number = "" ) {
        if ( $this->main_status_id != null ) {
            return ( $this->main_status->caption ) . " (" . $this->caption . ")";
        }

        return $this->caption;
    }

}
