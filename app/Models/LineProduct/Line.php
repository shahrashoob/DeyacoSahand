<?php

namespace App\Models\LineProduct;

use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Post\PostUser;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Line extends Model {
    use HasFactory;
    use Loggable;

    protected $fillable = [ "caption", "active_status_id", "goods_kind_id","ic" ];

    public static function GetIdFromCode( $code ) {

        $line = Line::where( "code", $code )->first();

        return isset( $line ) ? $line : null;
    }

    public function fullCaption() {
        return $this->code . " - " . $this->caption;
    }
    public function cost_center() {
        return $this->belongsTo( CostCenter::class, "ic" );
    }
    public function active_status() {
        return $this->belongsTo( Status::class, "active_status_id" );
    }

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public static function ExistsCaption( $caption, $id = false ) {
        if ( $id ) {
            return Line::where( "caption", $caption )->where( "id", "!=", $id )->exists();
        }

        return Line::where( "caption", $caption )->exists();
    }

    public function station() {
        return $this->hasMany( Station::class );
    }
    public function warehouses() {
        return $this->hasMany( Warehouse::class ,"belonging_to_id")->where("warehouse_type_id",5);
    }

    public function getCode() {

        if ( $this->code != "" ) {
            return $this->code;
        }
        $code_number = Line::where( "id", "<", $this->id )->count() + 1;
        $code        = $string = Str::of( $code_number )
                                    ->when( $code_number < 10, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } );
        $this->code  = $code;
        $this->save();

        return $this->code;
    }


    public function get_access_level_line( $post_id ) {
        $result_full = LinePost::
        where( [ "post_id" => $post_id, "line_id" => $this->id ] )->
        whereNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        exists();
        if ( $result_full ) {
            return "full";
        }

        $result_empty = LinePost::
        where( [ "post_id" => $post_id, "line_id" => $this->id ] )->
        exists();
        if ( ! $result_empty ) {
            return "empty";
        }

        return "half";

    }

    public static function getAllowedMachine( $post_ids = null,$worker=null ) {

        if ( ! $post_ids ) {
            $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids",$worker);
            //$post_ids = \Auth::user()->posts->pluck( "post_id" );
        }
        $machine_list = [];
        // Full Line
        $line_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "line_id" );
        if ( count( $line_ids ) > 0 ) {
            $machine_list = array_merge( $machine_list, Machine::
            join( "stations", "stations.id", "station_id" )->
            whereIn( "line_id", $line_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        //Full Station
        $station_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "station_id" );
        if ( count( $station_ids ) > 0 ) {
            $machine_list = array_merge( $machine_list, Machine::
            whereIn( "station_id", $station_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        //Full Machine Type
        $machine_type_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNotNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "machine_type_id" );
        if ( count( $machine_type_ids ) > 0 ) {
            $machine_list = array_merge( $machine_list, Machine::
            whereIn( "machine_type_id", $machine_type_ids )->
            pluck( "machines.id" )->toArray()
            );
        }

        // Machine
        $machine_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNotNull( "machine_type_id" )->
        whereNotNull( "machine_id" )->
        pluck( "machine_id" );
        if ( count( $machine_ids ) > 0 ) {
            $machine_list = array_merge( $machine_list, Machine::
            whereIn( "id", $machine_ids )->
            pluck( "id" )->toArray()
            );
        }
//        $machine_list[]=-1;
        return $machine_list;
    }


    public static function getAllowedMachineType() {

        $post_ids          = \Auth::user()->posts->pluck( "post_id" );
        $machine_type_list = [];
        // Full Line
        $line_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "line_id" );
        if ( count( $line_ids ) > 0 ) {
            $machine_type_list = array_merge( $machine_type_list, MachineType::
            join( "stations", "stations.id", "station_id" )->
            whereIn( "line_id", $line_ids )->
            pluck( "machine_types.id" )->toArray()
            );
        }

        //Full Station
        $station_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "station_id" );
        if ( count( $station_ids ) > 0 ) {
            $machine_type_list = array_merge( $machine_type_list, MachineType::
            whereIn( "station_id", $station_ids )->
            pluck( "machine_types.id" )->toArray()
            );
        }

        //Full Machine Type
        $machine_type_ids = LinePost::
        whereIn( "post_id", $post_ids )->
        whereNotNull( "line_id" )->
        whereNotNull( "station_id" )->
        whereNotNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        pluck( "machine_type_id" )->toArray();
        if ( count( $machine_type_ids ) > 0 ) {
            $machine_type_list = array_merge( $machine_type_list, $machine_type_ids );
        }


        return $machine_type_list;
    }
}
