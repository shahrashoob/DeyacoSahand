<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\Station;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineStatus extends Model
{
    use HasFactory;
    protected $table="machine_status";
    public static function Exists( $caption, Station $station, $status_id = false ) {

        $table = \App\Models\LineProduct\MachineStatus::join( "status", "status.id", "=", "status_id" )->where( [
            "caption"       => $caption,
            "station_id" => $station->id
        ] );
        if ( $status_id ) {
            return $table->where( "status_id", "!=", $status_id )->exists();
        }

        return $table->exists();
    }

    public function production_status() {
        return $this->belongsTo( Status::class );
    }
    public function station() {
        return $this->belongsTo( Station::class);
    }
}

