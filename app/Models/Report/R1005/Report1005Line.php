<?php

namespace App\Models\Report\R1005;

use App\Models\LineProduct\LinePost;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Report1005Line extends Model
{
    use HasFactory;
    protected $table="report_1005_line";
    protected $fillable=[
        "user_id",
        "line_id",
        "station_id",
        "machine_type_id",
        "machine_id"
    ];


    public function get_access_level_line( $line_id ) {
        $user_id=Auth::id();
        $result_full = Report1005Line::
        where( [ "user_id" => $user_id, "line_id" => $line_id ] )->
        whereNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        exists();
        if ( $result_full ) {
            return "full";
        }

        $result_empty = Report1005Line::
        where( [ "user_id" => $user_id, "line_id" => $line_id ] )->
        exists();
        if ( ! $result_empty ) {
            return "empty";
        }

        return "half";

    }

    public function get_access_level_station( $line_id ,$station_id) {
        $user_id=Auth::id();
        $result_full = Report1005Line::
        where( [ "user_id" => $user_id, "line_id" =>$line_id,"station_id"=>$station_id ] )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        exists();
        if ( $result_full ) {
            return "full";
        }

        $result_empty = Report1005Line::
        where( ["user_id" => $user_id,"line_id" =>$line_id,"station_id"=>$station_id ] )->
        exists();
        if ( ! $result_empty ) {
            return "empty";
        }

        return "half";

    }

    public function get_access_level_machine_type( $station_id,$machine_type_id ) {
        $user_id=Auth::id();
        $result_full = Report1005Line::
        where( [  "user_id" => $user_id,  "station_id" => $station_id, "machine_type_id" => $machine_type_id] )->
        whereNull( "machine_id" )->
        exists();
        if ( $result_full ) {
            return "full";
        }

        $result_empty = Report1005Line::
        where( [  "user_id" => $user_id,  "station_id" => $station_id, "machine_type_id" =>$machine_type_id ] )->
        exists();
        if ( ! $result_empty ) {
            return "empty";
        }

        return "";
    }

    public function has_machine_permission($machine_id){
        $user_id=Auth::id();
        $result_full = Report1005Line::
        where( [  "user_id" => $user_id,  "machine_id" => $machine_id] )->
        exists();
        if ( $result_full ) {
            return true;
        }
        return false;
    }


    public static function getAllowedMachine() {

      $user_id = Auth::user()->id;
        $machine_list=[];
        // Full Line
        $line_ids = Report1005Line::
        where( "user_id", $user_id )->
        whereNotNull( "line_id" )->
        whereNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "line_id" );
        if ( count( $line_ids ) > 0 ) {
            $machine_list= array_merge($machine_list, Machine::
            join( "stations", "stations.id", "station_id" )->
            whereIn( "line_id", $line_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        //Full Station
        $station_ids = Report1005Line::
        where( "user_id", $user_id )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "station_id" );
        if ( count( $station_ids ) > 0 ) {
            $machine_list= array_merge($machine_list, Machine::
            whereIn( "station_id", $station_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        //Full Machine Type
        $machine_type_ids = Report1005Line::
        where( "user_id", $user_id )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNotNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "machine_type_id" );
        if ( count( $machine_type_ids ) > 0 ) {
            $machine_list= array_merge($machine_list, Machine::
            whereIn( "machine_type_id", $machine_type_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        // Machine
        $machine_ids = Report1005Line::
        where( "user_id", $user_id )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNotNull( "machine_type_id" )->
        whereNotNull( "machine_id" )->
        pluck( "machine_id" );
        if ( count( $machine_ids ) > 0 ) {
            $machine_list= array_merge($machine_list, Machine::
            whereIn( "id", $machine_ids )->
            pluck( "id" )->toArray()
            );
        }

        return $machine_list;
    }
}
